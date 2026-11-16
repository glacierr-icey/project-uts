<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\SalesTransaction;
use App\Models\SalesDetail;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PosTransactionController extends Controller
{
    /**
     * Menampilkan antarmuka Halaman Kasir / POS Apotek Sehat Sehati.
     */
    public function index(): View
    {
        $products = Product::with(['category', 'activeFefoBatches'])
            ->orderBy('name')
            ->get()
            ->filter(function ($product) {
                return $product->total_stock > 0;
            })
            ->values()
            ->map(function ($product) {
                $nearestBatch = $product->activeFefoBatches->first();
                $product->sell_price = $nearestBatch ? (float) $nearestBatch->sell_price : 0;
                $product->nearest_batch_number = $nearestBatch?->batch_number;
                $product->nearest_expired_date = $nearestBatch?->expired_date?->format('d/m/Y');
                return $product;
            });

        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact('products', 'customers'));
    }

    /**
     * Memproses transaksi penjualan kasir (POS) dengan algoritma FEFO
     * dan validasi pembayaran anti-minus.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkout(Request $request): JsonResponse
    {
        // 1. Validasi Input Request
        $validated = $request->validate([
            'customer_id'    => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,qris,debit,transfer',
            'paid_amount'    => 'required|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $userId = Auth::id() ?? 1; // ID kasir yang bertugas

        try {
            // 2. Eksekusi dalam DB Transaction dengan Row Locking untuk Integritas ACID
            $transactionResult = DB::transaction(function () use ($validated, $userId) {
                $allocatedDetails = [];
                $calculatedTotal = 0.00;

                // 3. Iterasi setiap produk yang dibeli untuk dialokasikan menggunakan FEFO
                foreach ($validated['items'] as $item) {
                    $productId = $item['product_id'];
                    $qtyNeeded = (int) $item['quantity'];

                    $product = Product::findOrFail($productId);

                    // Ambil batch obat aktif (stok > 0 dan belum kadaluarsa),
                    // diurutkan dari tanggal kadaluarsa paling dekat (FEFO),
                    // dikunci dengan lockForUpdate() guna mencegah race condition.
                    $batches = ProductBatch::where('product_id', $productId)
                        ->where('stock', '>', 0)
                        ->where('expired_date', '>=', now()->toDateString())
                        ->orderBy('expired_date', 'asc')
                        ->lockForUpdate()
                        ->get();

                    $totalAvailableStock = $batches->sum('stock');

                    // Validasi kecukupan stok total batch aktif
                    if ($totalAvailableStock < $qtyNeeded) {
                        throw ValidationException::withMessages([
                            'items' => "Stok obat '{$product->name}' tidak mencukupi atau sudah kadaluarsa! Tersedia: {$totalAvailableStock} {$product->unit}, Dibutuhkan: {$qtyNeeded} {$product->unit}."
                        ]);
                    }

                    // 4. Algoritma FEFO Allocation (Mengambil stok dari batch terdekat hingga kuantitas terpenuhi)
                    $remainingQty = $qtyNeeded;

                    foreach ($batches as $batch) {
                        if ($remainingQty <= 0) {
                            break;
                        }

                        // Tentukan kuantitas yang diambil dari batch saat ini
                        $takeQty = min($batch->stock, $remainingQty);
                        $itemPrice = (float) $batch->sell_price;
                        $itemSubtotal = $takeQty * $itemPrice;

                        // Kurangkan stok batch riil di database
                        $batch->decrement('stock', $takeQty);

                        // Simpan alokasi detail untuk di-insert ke sales_details
                        $allocatedDetails[] = [
                            'product_batch_id' => $batch->id,
                            'batch_number'     => $batch->batch_number,
                            'expired_date'     => $batch->expired_date->format('Y-m-d'),
                            'product_name'     => $product->name,
                            'quantity'         => $takeQty,
                            'price'            => $itemPrice,
                            'subtotal'         => $itemSubtotal,
                        ];

                        $calculatedTotal += $itemSubtotal;
                        $remainingQty -= $takeQty;
                    }
                }

                // 5. Validasi Finansial Anti-Minus (Mencegah celah bug uang kembalian minus)
                $paidAmount = (float) $validated['paid_amount'];

                if ($validated['payment_method'] === 'cash') {
                    if ($paidAmount < $calculatedTotal) {
                        $shortfall = $calculatedTotal - $paidAmount;
                        throw ValidationException::withMessages([
                            'paid_amount' => "Nominal pembayaran tunai kurang! Total tagihan: Rp " . number_format($calculatedTotal, 2, ',', '.') . 
                                             ", Uang dibayar: Rp " . number_format($paidAmount, 2, ',', '.') . 
                                             ". Kekurangan: Rp " . number_format($shortfall, 2, ',', '.') . "."
                        ]);
                    }
                    $changeAmount = $paidAmount - $calculatedTotal;
                } else {
                    // Untuk non-tunai (QRIS / EDC / Transfer), nominal bayar harus pas dengan total transaksi
                    $paidAmount = $calculatedTotal;
                    $changeAmount = 0.00;
                }

                // 6. Buat Nomor Invoice Unik (Format: INV-YYYYMMDD-RANDOM)
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

                // 7. Simpan Header Transaksi (sales_transactions)
                $salesTransaction = SalesTransaction::create([
                    'user_id'        => $userId,
                    'customer_id'    => $validated['customer_id'] ?? null,
                    'invoice_number' => $invoiceNumber,
                    'order_channel'  => 'pos',
                    'total_amount'   => $calculatedTotal,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'paid',
                ]);

                // 8. Simpan Rincian Item Transaksi (sales_details)
                foreach ($allocatedDetails as $detail) {
                    SalesDetail::create([
                        'sales_transaction_id' => $salesTransaction->id,
                        'product_batch_id'     => $detail['product_batch_id'],
                        'quantity'             => $detail['quantity'],
                        'price'                => $detail['price'],
                        'subtotal'             => $detail['subtotal'],
                    ]);
                }

                return [
                    'transaction' => $salesTransaction->load(['customer', 'user:id,name']),
                    'details'     => $allocatedDetails,
                    'summary'     => [
                        'total_amount'  => $calculatedTotal,
                        'paid_amount'   => $paidAmount,
                        'change_amount' => $changeAmount,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi kasir berhasil diproses secara otomatis dengan alokasi FEFO!',
                'data'    => $transactionResult,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi transaksi gagal.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses transaksi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}


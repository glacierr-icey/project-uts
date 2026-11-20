<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\SalesDetail;
use App\Models\SalesTransaction;
use App\Models\User;
use App\Services\PaymentNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    /**
     * Halaman depan publik: portal pelanggan untuk tamu, redirect role untuk petugas.
     */
    public function home(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return match (Auth::user()->role) {
                'kasir'    => redirect()->route('pos.index'),
                'apoteker' => redirect()->route('products.index'),
                default    => redirect()->route('pos.index'),
            };
        }

        return $this->index($request);
    }

    /**
     * Katalog obat publik dengan harga dan stok real-time dari batch FEFO.
     */
    public function index(Request $request): View
    {
        $query = Product::with([
            'category',
            'activeFefoBatches',
            'batches' => fn ($q) => $q->orderByDesc('expired_date'),
        ])->orderBy('name');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->integer('kategori'));
        }

        $products = $query->get()->map(fn (Product $product) => $this->decorateProduct($product));
        $categories = Category::orderBy('name')->get();

        return view('portal.index', [
            'products'   => $products,
            'categories' => $categories,
            'search'     => (string) $request->input('q', ''),
            'categoryId' => $request->integer('kategori') ?: null,
            'store'      => config('pharmacy'),
        ]);
    }

    /**
     * Halaman "Tentang Toko".
     */
    public function about(): View
    {
        return view('portal.about', [
            'store' => config('pharmacy'),
            'stats' => [
                'products'   => Product::count(),
                'categories' => Category::count(),
                'customers'  => Customer::count(),
            ],
        ]);
    }

    /**
     * Halaman "Alamat Toko" beserta peta lokasi.
     */
    public function address(): View
    {
        return view('portal.address', [
            'store' => config('pharmacy'),
        ]);
    }

    /**
     * Form checkout publik.
     */
    public function checkoutForm(): View
    {
        $productsPayload = $this->catalogProducts()->map(fn (Product $product) => [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => (float) $product->display_price,
            'unit'     => $product->unit,
            'stock'    => (int) $product->display_stock,
            'category' => $product->category?->name ?? 'Umum',
        ])->values();

        return view('portal.checkout', [
            'store'           => config('pharmacy'),
            'productsPayload' => $productsPayload,
            'banks'           => config('pharmacy.banks'),
        ]);
    }

    /**
     * Menyimpan pesanan online pelanggan sebagai transaksi baru (status pending).
     */
    public function storeOrder(Request $request, PaymentNotifier $notifier): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s().]{8,24}$/'],
            'delivery_method'  => 'required|in:pickup,delivery',
            'payment_method'   => 'required|in:cod,transfer,qris',
            'order_notes'      => 'nullable|string|max:500',
            'items'            => 'required|string',
            'address_street'   => 'nullable|required_if:delivery_method,delivery|string|max:255',
            'address_village'  => 'nullable|required_if:delivery_method,delivery|string|max:100',
            'address_district' => 'nullable|required_if:delivery_method,delivery|string|max:100',
            'address_city'     => 'nullable|required_if:delivery_method,delivery|string|max:100',
            'address_postal'   => 'nullable|string|max:10',
            'address_notes'    => 'nullable|string|max:255',
        ], [
            'customer_name.required'    => 'Nama penerima wajib diisi.',
            'customer_phone.required'   => 'Nomor HP/WhatsApp wajib diisi.',
            'customer_phone.regex'      => 'Format nomor HP tidak valid. Contoh: 0812-3456-7890.',
            'delivery_method.required'  => 'Pilih metode pengiriman.',
            'payment_method.required'   => 'Pilih metode pembayaran.',
            'items.required'            => 'Keranjang belanja masih kosong. Silakan pilih obat terlebih dahulu.',
            'address_street.required_if'=> 'Alamat jalan/nomor rumah wajib diisi untuk pengiriman.',
            'address_village.required_if'=> 'Desa/kelurahan wajib diisi untuk pengiriman.',
            'address_district.required_if'=> 'Kecamatan wajib diisi untuk pengiriman.',
            'address_city.required_if'  => 'Kabupaten/kota wajib diisi untuk pengiriman.',
        ]);

        $items = $this->parseCartItems($validated['items']);

        $deliveryAddress = null;
        if ($validated['delivery_method'] === 'delivery') {
            $deliveryAddress = collect([
                trim((string) ($validated['address_street'] ?? '')),
                'Desa/Kel. ' . trim((string) ($validated['address_village'] ?? '')) . ', Kec. ' . trim((string) ($validated['address_district'] ?? '')),
                trim((string) ($validated['address_city'] ?? '')) . ' ' . trim((string) ($validated['address_postal'] ?? '')),
                !empty($validated['address_notes']) ? 'Patokan: ' . trim((string) $validated['address_notes']) : null,
            ])->filter()->implode("\n");
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $items, $deliveryAddress) {
                $allocatedDetails = [];
                $subtotal = 0.00;

                foreach ($items as $productId => $quantity) {
                    $product = Product::find($productId);

                    if (! $product) {
                        throw ValidationException::withMessages([
                            'items' => 'Salah satu obat pada keranjang tidak lagi tersedia.',
                        ]);
                    }

                    $batches = ProductBatch::where('product_id', $productId)
                        ->where('stock', '>', 0)
                        ->where('expired_date', '>=', now()->toDateString())
                        ->orderBy('expired_date', 'asc')
                        ->lockForUpdate()
                        ->get();

                    $availableStock = (int) $batches->sum('stock');

                    if ($availableStock < $quantity) {
                        throw ValidationException::withMessages([
                            'items' => "Stok obat '{$product->name}' tidak mencukupi. Tersedia {$availableStock} {$product->unit}, diminta {$quantity} {$product->unit}.",
                        ]);
                    }

                    $remaining = $quantity;

                    foreach ($batches as $batch) {
                        if ($remaining <= 0) {
                            break;
                        }

                        $take = min((int) $batch->stock, $remaining);
                        $price = (float) $batch->sell_price;
                        $lineSubtotal = $take * $price;

                        $batch->decrement('stock', $take);

                        $allocatedDetails[] = [
                            'product_batch_id' => $batch->id,
                            'quantity'         => $take,
                            'price'            => $price,
                            'subtotal'         => $lineSubtotal,
                        ];

                        $subtotal += $lineSubtotal;
                        $remaining -= $take;
                    }
                }

                $deliveryFee = 0.00;
                $totalAmount = $subtotal + $deliveryFee;

                $customer = Customer::firstOrCreate(
                    ['phone' => $validated['customer_phone']],
                    [
                        'name'    => $validated['customer_name'],
                        'address' => $deliveryAddress,
                    ]
                );

                $customer->update([
                    'name'    => $validated['customer_name'],
                    'address' => $deliveryAddress ?: $customer->address,
                ]);

                $systemUserId = User::query()->where('role', 'admin')->orderBy('id')->value('id')
                    ?? User::query()->orderBy('id')->value('id');

                if (! $systemUserId) {
                    throw new \RuntimeException('Akun petugas belum tersedia. Jalankan seeder terlebih dahulu.');
                }

                $invoiceNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

                $salesTransaction = SalesTransaction::create([
                    'user_id'         => $systemUserId,
                    'customer_id'     => $customer->id,
                    'invoice_number'  => $invoiceNumber,
                    'order_channel'   => 'online',
                    'total_amount'    => $totalAmount,
                    'delivery_fee'    => $deliveryFee,
                    'payment_method'  => $validated['payment_method'],
                    'payment_status'  => 'pending',
                    'delivery_method' => $validated['delivery_method'],
                    'recipient_name'  => $validated['customer_name'],
                    'recipient_phone' => $validated['customer_phone'],
                    'delivery_address'=> $deliveryAddress,
                    'order_notes'     => $validated['order_notes'] ?? null,
                ]);

                foreach ($allocatedDetails as $detail) {
                    SalesDetail::create([
                        'sales_transaction_id' => $salesTransaction->id,
                        'product_batch_id'     => $detail['product_batch_id'],
                        'quantity'             => $detail['quantity'],
                        'price'                => $detail['price'],
                        'subtotal'             => $detail['subtotal'],
                    ]);
                }

                return $salesTransaction;
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'items' => 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.',
            ])->withInput();
        }

        // Notifikasi otomatis (fail-safe: dilewati bila env Telegram belum diset).
        $notifier->orderPlaced($transaction);

        return redirect()
            ->route('portal.order.show', $transaction->invoice_number)
            ->with('order_success', true);
    }

    /**
     * Halaman detail / konfirmasi pesanan berdasarkan nomor invoice.
     */
    public function showOrder(string $invoice, PaymentNotifier $notifier): View
    {
        $order = SalesTransaction::with([
            'customer',
            'details.productBatch.product',
        ])
            ->where('order_channel', 'online')
            ->whereRaw('UPPER(invoice_number) = ?', [strtoupper($invoice)])
            ->firstOrFail();

        // Dipakai untuk mengisi otomatis tautan WhatsApp/Telegram di halaman ini.
        $confirmationMessage = $notifier->buildMessage(
            $order,
            $order->payment_status === 'paid' ? '[ PEMBAYARAN DIKONFIRMASI ]' : '[ KONFIRMASI PEMBAYARAN ]',
            $order->payment_status === 'paid' ? 'Lunas (paid)' : 'Menunggu konfirmasi (pending)'
        );

        return view('portal.order', [
            'order' => $order,
            'store' => config('pharmacy'),
            'banks' => config('pharmacy.banks'),
            'confirmationMessage' => $confirmationMessage,
        ]);
    }

    /**
     * Form lacak pesanan.
     */
    public function trackForm(): View
    {
        return view('portal.track', [
            'store' => config('pharmacy'),
        ]);
    }

    /**
     * Cari pesanan online berdasarkan nomor invoice.
     */
    public function track(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50',
        ], [
            'invoice_number.required' => 'Nomor pesanan/invoice wajib diisi.',
        ]);

        $invoice = strtoupper(trim($validated['invoice_number']));

        $order = SalesTransaction::query()
            ->where('order_channel', 'online')
            ->whereRaw('UPPER(invoice_number) = ?', [$invoice])
            ->first();

        if (! $order) {
            return back()
                ->withErrors(['invoice_number' => 'Nomor pesanan tidak ditemukan. Periksa kembali invoice Anda.'])
                ->withInput();
        }

        return redirect()->route('portal.order.show', $order->invoice_number);
    }

    /**
     * Ambil katalog produk online (hanya produk yang memiliki batch aktif).
     */
    private function catalogProducts(): Collection
    {
        return Product::with([
            'category',
            'activeFefoBatches',
            'batches' => fn ($q) => $q->orderByDesc('expired_date'),
        ])
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => $this->decorateProduct($product));
    }

    /**
     * Lengkapi atribut tampilan produk: harga batch terdekat, total stok, dan ED.
     */
    private function decorateProduct(Product $product): Product
    {
        $nearestBatch = $product->activeFefoBatches->first();
        $fallbackBatch = $product->batches->first();

        $product->display_price = (float) ($nearestBatch?->sell_price ?? $fallbackBatch?->sell_price ?? 0);
        $product->display_stock = (int) $product->activeFefoBatches->sum('stock');
        $product->nearest_expiry = $nearestBatch?->expired_date;

        return $product;
    }

    /**
     * Decode & normalisasi item keranjang dari localStorage.
     *
     * @return array<int, int> product_id => quantity
     */
    private function parseCartItems(string $rawItems): array
    {
        $decoded = json_decode($rawItems, true);

        if (! is_array($decoded) || count($decoded) === 0) {
            throw ValidationException::withMessages([
                'items' => 'Keranjang belanja kosong atau tidak valid. Silakan pilih obat terlebih dahulu.',
            ]);
        }

        $normalized = [];

        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $normalized[$productId] = ($normalized[$productId] ?? 0) + min($quantity, 999);
        }

        if (count($normalized) === 0) {
            throw ValidationException::withMessages([
                'items' => 'Tidak ada item valid pada keranjang. Silakan pilih obat kembali.',
            ]);
        }

        return $normalized;
    }
}

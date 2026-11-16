<?php

namespace App\Http\Controllers;

use App\Models\ProductBatch;
use App\Models\SalesTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OnlineOrderController extends Controller
{
    /**
     * Daftar pesanan online yang masuk dari portal pelanggan.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = trim((string) $request->input('q', ''));

        $query = SalesTransaction::with([
            'customer',
            'details.productBatch.product',
        ])->where('order_channel', 'online');

        if (in_array($status, ['pending', 'paid', 'cancelled', 'failed'], true)) {
            $query->where('payment_status', $status);
        }

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customer) use ($search) {
                        $customer->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $baseQuery = SalesTransaction::where('order_channel', 'online');

        $stats = [
            'total'        => (clone $baseQuery)->count(),
            'pending'      => (clone $baseQuery)->where('payment_status', 'pending')->count(),
            'pendingValue' => (clone $baseQuery)->where('payment_status', 'pending')->sum('total_amount'),
            'paidValue'    => (clone $baseQuery)->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('orders.index', compact('orders', 'status', 'search', 'stats'));
    }

    /**
     * Tandai pesanan online sudah dibayar / dikonfirmasi oleh petugas.
     */
    public function markPaid(SalesTransaction $order): RedirectResponse
    {
        abort_unless($order->order_channel === 'online', 404);

        if ($order->payment_status !== 'pending') {
            return back()->withErrors([
                'order' => "Pesanan {$order->invoice_number} tidak berada pada status menunggu konfirmasi.",
            ]);
        }

        $order->update(['payment_status' => 'paid']);

        return back()->with('success', "Pesanan {$order->invoice_number} berhasil dikonfirmasi dan ditandai lunas.");
    }

    /**
     * Batalkan pesanan online dan kembalikan stok ke batch asal (FEFO audit tetap terjaga).
     */
    public function cancel(SalesTransaction $order): RedirectResponse
    {
        abort_unless($order->order_channel === 'online', 404);

        if ($order->payment_status !== 'pending') {
            return back()->withErrors([
                'order' => "Hanya pesanan berstatus menunggu yang dapat dibatalkan.",
            ]);
        }

        DB::transaction(function () use ($order) {
            $order->loadMissing('details');

            foreach ($order->details as $detail) {
                $batch = ProductBatch::query()
                    ->whereKey($detail->product_batch_id)
                    ->lockForUpdate()
                    ->first();

                if ($batch) {
                    $batch->increment('stock', $detail->quantity);
                }
            }

            $order->update(['payment_status' => 'cancelled']);
        });

        return back()->with('success', "Pesanan {$order->invoice_number} dibatalkan dan stok obat telah dikembalikan.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Menampilkan rekapitulasi laporan penjualan apotek dengan filter tanggal/periode.
     */
    public function sales(Request $request): View
    {
        // Default filter ke 30 hari terakhir agar riwayat penjualan langsung terlihat.
        // Pengguna tetap bisa mempersempit periode lewat form filter.
        $startDate = $request->input('start_date', now()->subDays(29)->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->format('Y-m-d'));
        $paymentMethod = $request->input('payment_method');

        $query = SalesTransaction::with([
            'user:id,name',
            'customer:id,name,phone',
            'details.productBatch.product:id,name,unit',
        ])
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->whereIn('payment_status', ['paid', 'pending']);

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        // Simpan salinan query sebelum pagination agar ringkasan finansial
        // menghitung SELURUH transaksi pada periode, bukan hanya halaman pertama.
        $summaryQuery = clone $query;

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Ringkasan Finansial Periode Terpilih
        $allMatching = $summaryQuery->get();
        $totalRevenue = $allMatching->sum('total_amount');
        $totalTransactions = $allMatching->count();
        $totalItemsSold = $allMatching->flatMap->details->sum('quantity');

        // Rekapitulasi per Metode Pembayaran
        $paymentSummary = $allMatching->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ];
        });

        // Ringkasan pesanan online yang masih menunggu konfirmasi petugas.
        $pendingCount = $allMatching->where('payment_status', 'pending')->count();
        $pendingValue = $allMatching->where('payment_status', 'pending')->sum('total_amount');
        $onlineCount = $allMatching->where('order_channel', 'online')->count();

        return view('reports.sales', compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'totalItemsSold',
            'paymentSummary',
            'pendingCount',
            'pendingValue',
            'onlineCount',
            'startDate',
            'endDate',
            'paymentMethod'
        ));
    }
}


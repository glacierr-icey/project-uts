@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi Penjualan')

@push('styles')
<style>
    @media print {
        nav, .no-print, .btn, .pagination, .filter-card {
            display: none !important;
        }
        body {
            background-color: white !important;
            color: black !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .print-header {
            display: block !important;
        }
    }
    .print-header {
        display: none;
    }
</style>
@endpush

@section('content')
<!-- Header Khusus Cetak Kertas -->
<div class="print-header text-center mb-4">
    <h3 class="fw-bold mb-1">APOTEK SEHAT SEHATI PUNGGUR</h3>
    <p class="mb-1 text-muted">Jl. Metro - Kotagajah, Dusun Tanggul Angin, Kec. Punggur, Kab. Lampung Tengah</p>
    <h5 class="fw-bold text-decoration-underline mt-3">LAPORAN REKAPITULASI PENJUALAN</h5>
    <small class="text-muted">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</small>
    <hr>
</div>

<!-- Kartu Filter Periode Penjualan -->
<div class="card p-4 mb-4 filter-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-teal mb-0">
            <i class="bi bi-funnel me-2"></i>Filter Rentang Tanggal & Metode Bayar
        </h5>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i>Cetak Laporan
        </button>
    </div>

    <form method="GET" action="{{ route('reports.sales') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Metode Pembayaran</label>
            <select name="payment_method" class="form-select">
                <option value="">-- Semua Metode --</option>
                <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                <option value="cod" {{ $paymentMethod === 'cod' ? 'selected' : '' }}>COD (Bayar di Tempat)</option>
                <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                <option value="debit" {{ $paymentMethod === 'debit' ? 'selected' : '' }}>Debit</option>
                <option value="transfer" {{ $paymentMethod === 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-pharmacy w-100">
                <i class="bi bi-search me-1"></i>Tampilkan
            </button>
            <a href="{{ route('reports.sales') }}" class="btn btn-light border">Reset</a>
        </div>
    </form>
</div>

<!-- Kartu Ringkasan Metrik Finansial -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-semibold">Total Nilai Transaksi</small>
                    <h3 class="fw-bold text-success mb-0 mt-1" style="font-size: 1.35rem;">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="rounded-circle bg-success-subtle p-3 text-success">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-semibold">Total Transaksi</small>
                    <h3 class="fw-bold text-primary mb-0 mt-1" style="font-size: 1.35rem;">
                        {{ number_format($totalTransactions, 0, ',', '.') }} Transaksi
                    </h3>
                    <small class="text-muted" style="font-size: 10px;">{{ number_format($onlineCount, 0, ',', '.') }} dari portal online</small>
                </div>
                <div class="rounded-circle bg-primary-subtle p-3 text-primary">
                    <i class="bi bi-receipt fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-semibold">Pesanan Menunggu</small>
                    <h3 class="fw-bold text-warning mb-0 mt-1" style="font-size: 1.35rem;">
                        Rp {{ number_format($pendingValue, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 10px;">{{ number_format($pendingCount, 0, ',', '.') }} pesanan pending</small>
                </div>
                <div class="rounded-circle bg-warning-subtle p-3 text-warning">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-semibold">Total Unit Obat</small>
                    <h3 class="fw-bold text-info mb-0 mt-1" style="font-size: 1.35rem;">
                        {{ number_format($totalItemsSold, 0, ',', '.') }} Item
                    </h3>
                    <small class="text-muted" style="font-size: 10px;">Termasuk pesanan pending</small>
                </div>
                <div class="rounded-circle bg-info-subtle p-3 text-info">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rekap Ringkas per Metode Pembayaran -->
@php
    $methodMeta = [
        'cash'     => ['label' => 'Tunai',    'icon' => 'bi-cash-coin',       'tone' => 'success'],
        'cod'      => ['label' => 'COD',      'icon' => 'bi-cash-stack',      'tone' => 'success'],
        'qris'     => ['label' => 'QRIS',     'icon' => 'bi-qr-code',         'tone' => 'info'],
        'debit'    => ['label' => 'Debit',    'icon' => 'bi-credit-card-2-front', 'tone' => 'primary'],
        'transfer' => ['label' => 'Transfer', 'icon' => 'bi-bank2',           'tone' => 'warning'],
    ];
@endphp
<div class="card p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-wallet2 me-2"></i>Rekap Metode Pembayaran
        </h5>
        <small class="text-muted">
            Periode {{ date('d/m/Y', strtotime($startDate)) }} &ndash; {{ date('d/m/Y', strtotime($endDate)) }}
        </small>
    </div>

    <div class="row g-3">
        @foreach ($methodMeta as $methodKey => $meta)
            @php $methodRow = $paymentSummary[$methodKey] ?? null; @endphp
            <div class="col-6 col-lg-3">
                <div class="border rounded-4 p-3 h-100 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-{{ $meta['tone'] }}-subtle text-{{ $meta['tone'] }} d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi {{ $meta['icon'] }} fs-5"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="small text-muted fw-semibold">{{ $meta['label'] }}</div>
                        <div class="fw-bold text-dark text-nowrap">
                            Rp {{ number_format($methodRow['total'] ?? 0, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">{{ number_format($methodRow['count'] ?? 0, 0, ',', '.') }} transaksi</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Tabel Rincian Riwayat Transaksi -->
<div class="card p-4">
    <h5 class="fw-bold text-dark mb-3">
        <i class="bi bi-table me-2"></i>Daftar Riwayat Penjualan
    </h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Waktu</th>
                    <th>No. Invoice</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th>Rincian Item (Obat, Batch, Exp, Qty)</th>
                    <th class="text-end">Total Tagihan</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="small text-nowrap">
                            <div class="fw-semibold">{{ $trx->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $trx->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td class="fw-bold font-monospace text-teal small">
                            {{ $trx->invoice_number }}
                        </td>
                        <td class="small text-muted">
                            @if ($trx->order_channel === 'online')
                                <span class="badge bg-info-subtle text-info-emphasis border">
                                    <i class="bi bi-globe2 me-1"></i>Pesanan Online
                                </span>
                            @else
                                <i class="bi bi-person me-1"></i>{{ $trx->user?->name ?? 'Kasir' }}
                            @endif
                        </td>
                        <td class="small">
                            <div class="fw-semibold">{{ $trx->customer?->name ?? 'Pembeli Umum' }}</div>
                            @if ($trx->order_channel === 'online')
                                <small class="text-muted">
                                    <i class="bi {{ $trx->delivery_method === 'delivery' ? 'bi-truck' : 'bi-shop' }} me-1"></i>
                                    {{ $trx->delivery_method === 'delivery' ? 'Kirim ke alamat' : 'Ambil di toko' }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <ul class="list-unstyled mb-0 small">
                                @foreach ($trx->details as $detail)
                                    <li class="py-1 border-bottom-dashed">
                                        <strong>{{ $detail->productBatch?->product?->name ?? 'Obat' }}</strong>
                                        <span class="badge bg-light text-dark border">
                                            Batch: {{ $detail->productBatch?->batch_number ?? '-' }}
                                        </span>
                                        <span class="text-muted">
                                            ({{ $detail->quantity }} {{ $detail->productBatch?->product?->unit ?? 'pcs' }} &times; Rp {{ number_format($detail->price, 0, ',', '.') }})
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @php $trxMeta = $methodMeta[$trx->payment_method] ?? null; @endphp
                            <span class="badge bg-{{ $trxMeta['tone'] ?? 'secondary' }}-subtle text-{{ $trxMeta['tone'] ?? 'secondary' }} border">
                                <i class="bi {{ $trxMeta['icon'] ?? 'bi-wallet2' }} me-1"></i>{{ $trxMeta['label'] ?? strtoupper($trx->payment_method) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $trxStatus = [
                                    'paid'      => ['label' => 'LUNAS',      'tone' => 'success'],
                                    'pending'   => ['label' => 'MENUNGGU',   'tone' => 'warning'],
                                    'cancelled' => ['label' => 'DIBATALKAN', 'tone' => 'danger'],
                                    'failed'    => ['label' => 'GAGAL',      'tone' => 'danger'],
                                ][$trx->payment_status] ?? ['label' => strtoupper($trx->payment_status), 'tone' => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $trxStatus['tone'] }}-subtle text-{{ $trxStatus['tone'] }} border">
                                {{ $trxStatus['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block text-secondary opacity-50 mb-2"></i>
                            Tidak ditemukan transaksi pada rentang tanggal terpilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4 no-print">
        {{ $transactions->links() }}
    </div>
</div>
@endsection


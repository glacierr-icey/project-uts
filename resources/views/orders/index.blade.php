@extends('layouts.app')

@section('title', 'Pesanan Online Pelanggan')

@push('styles')
<style>
    .stat-mini { padding: 16px; height: 100%; }
    .stat-mini .label { font-size: 10px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase; color: var(--ink-400); }
    .stat-mini .value { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.35rem; color: var(--ink-900); line-height: 1.2; margin-top: 4px; }
    .stat-mini .hint { font-size: 10.5px; color: var(--ink-400); margin-top: 3px; }
    .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .order-items { list-style: none; margin: 0; padding: 0; }
    .order-items li { font-size: 11.5px; color: var(--ink-500); padding: 2px 0; }
    .order-items strong { color: var(--ink-700); }
    .invoice-code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-weight: 700; font-size: 11.5px; color: var(--cyan-700); }
    .filter-card { padding: 16px; }
    .search-wrap { position: relative; }
    .search-wrap > .bi { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--ink-400); font-size: 14px; pointer-events: none; }
    .search-wrap .form-control { padding-left: 38px; }
    .table-actions .btn { font-size: 11px; font-weight: 700; border-radius: 9px; }
    .delivery-note { font-size: 10.5px; color: var(--ink-400); line-height: 1.45; max-width: 220px; }
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">
            <span class="title-icon"><i class="bi bi-bag-check"></i></span>
            Pesanan Online Pelanggan
        </h1>
        <p class="page-sub">Pesanan yang dikirim dari portal pelanggan. Konfirmasi pembayaran atau batalkan pesanan langsung dari halaman ini.</p>
    </div>
    <a href="{{ route('portal.index') }}" target="_blank" rel="noopener" class="btn btn-soft">
        <i class="bi bi-box-arrow-up-right me-1"></i>Buka Portal Pelanggan
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-mini">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="label">Total Pesanan Online</div>
                    <div class="value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
                    <div class="hint">Seluruh pesanan dari portal</div>
                </div>
                <span class="stat-icon" style="background: rgba(6,182,212,.12); color: var(--cyan-700);"><i class="bi bi-bag-check"></i></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-mini">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="label">Menunggu Konfirmasi</div>
                    <div class="value" style="color: #b45309;">{{ number_format($stats['pending'], 0, ',', '.') }}</div>
                    <div class="hint">Perlu diproses petugas</div>
                </div>
                <span class="stat-icon" style="background: rgba(245,158,11,.14); color: #b45309;"><i class="bi bi-hourglass-split"></i></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-mini">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="label">Nilai Pending</div>
                    <div class="value" style="color: #b45309; font-size: 1.08rem;">Rp {{ number_format($stats['pendingValue'], 0, ',', '.') }}</div>
                    <div class="hint">Belum dikonfirmasi</div>
                </div>
                <span class="stat-icon" style="background: rgba(245,158,11,.14); color: #b45309;"><i class="bi bi-wallet2"></i></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-mini">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="label">Nilai Lunas</div>
                    <div class="value" style="color: #047857; font-size: 1.08rem;">Rp {{ number_format($stats['paidValue'], 0, ',', '.') }}</div>
                    <div class="hint">Sudah dikonfirmasi</div>
                </div>
                <span class="stat-icon" style="background: rgba(16,185,129,.12); color: #047857;"><i class="bi bi-check-circle"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="card filter-card mb-4">
    <form method="GET" action="{{ route('orders.index') }}" class="row g-2 align-items-end">
        <div class="col-lg-5">
            <label class="form-label small fw-semibold text-muted">Cari invoice / nama / no. HP</label>
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="q" class="form-control" value="{{ $search }}" placeholder="Contoh: ORD-20261113 atau nama pelanggan">
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <label class="form-label small fw-semibold text-muted">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Lunas / Diproses</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <button type="submit" class="btn btn-cyan w-100"><i class="bi bi-funnel me-1"></i>Tampilkan</button>
        </div>
        <div class="col-lg-2">
            <a href="{{ route('orders.index') }}" class="btn btn-soft w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
        </div>
    </form>
</div>

<div class="card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-table me-2"></i>Daftar Pesanan</h5>
        <span class="soft-chip"><i class="bi bi-arrow-repeat" style="color: var(--cyan-600);"></i>{{ $orders->total() }} pesanan ditemukan</span>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle">
            <thead>
                <tr>
                    <th>Waktu / Invoice</th>
                    <th>Pelanggan</th>
                    <th>Item &amp; Pengiriman</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Pembayaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @php
                        $statusMeta = [
                            'pending'   => ['label' => 'MENUNGGU',  'class' => 'pill-warning', 'icon' => 'bi-hourglass-split'],
                            'paid'      => ['label' => 'LUNAS',     'class' => 'pill-success', 'icon' => 'bi-check-circle'],
                            'cancelled' => ['label' => 'DIBATALKAN','class' => 'pill-danger',  'icon' => 'bi-x-circle'],
                            'failed'    => ['label' => 'GAGAL',     'class' => 'pill-danger',  'icon' => 'bi-exclamation-triangle'],
                        ][$order->payment_status] ?? ['label' => strtoupper($order->payment_status), 'class' => 'pill-neutral', 'icon' => 'bi-info-circle'];

                        $paymentMeta = [
                            'cod'      => ['label' => 'COD',      'icon' => 'bi-cash-coin'],
                            'transfer' => ['label' => 'Transfer', 'icon' => 'bi-bank2'],
                            'qris'     => ['label' => 'QRIS',     'icon' => 'bi-qr-code'],
                        ][$order->payment_method] ?? ['label' => strtoupper($order->payment_method), 'icon' => 'bi-wallet2'];
                    @endphp
                    <tr>
                        <td class="text-nowrap">
                            <div class="fw-semibold small">{{ $order->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted d-block">{{ $order->created_at->format('H:i') }} WIB</small>
                            <span class="invoice-code">{{ $order->invoice_number }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold small" style="color: var(--ink-900);">{{ $order->recipient_name ?: $order->customer?->name ?? 'Pelanggan' }}</div>
                            <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $order->recipient_phone ?: $order->customer?->phone ?? '-' }}</small>
                            <small class="text-muted d-block"><i class="bi bi-person me-1"></i>{{ $order->customer?->name ?? 'Tamu' }}</small>
                        </td>
                        <td>
                            <ul class="order-items">
                                @foreach ($order->details as $detail)
                                    <li>
                                        <strong>{{ $detail->productBatch?->product?->name ?? 'Obat' }}</strong>
                                        &times; {{ $detail->quantity }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-1">
                                @if ($order->delivery_method === 'delivery')
                                    <span class="badge-soft badge-warning-soft"><i class="bi bi-truck"></i>Kirim ke alamat</span>
                                    <div class="delivery-note mt-1">{{ $order->delivery_address }}</div>
                                @else
                                    <span class="badge-soft badge-success-soft"><i class="bi bi-shop"></i>Ambil di toko</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end fw-bold text-dark text-nowrap">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info-emphasis border">
                                <i class="bi {{ $paymentMeta['icon'] }} me-1"></i>{{ $paymentMeta['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="pill {{ $statusMeta['class'] }}">
                                <i class="bi {{ $statusMeta['icon'] }}"></i>{{ $statusMeta['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column gap-1 table-actions" style="min-width: 112px;">
                                @if ($order->payment_status === 'pending')
                                    <form method="POST" action="{{ route('orders.mark-paid', $order) }}"
                                          onsubmit="return confirm('Konfirmasi pembayaran pesanan {{ $order->invoice_number }}? Pesanan akan ditandai lunas dan siap diproses.');">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-check2-circle me-1"></i>Konfirmasi Lunas
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('orders.cancel', $order) }}"
                                          onsubmit="return confirm('Batalkan pesanan {{ $order->invoice_number }}? Stok obat akan dikembalikan ke batch asal.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-x-circle me-1"></i>Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Tidak ada aksi</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x d-block mb-2" style="font-size: 2.2rem; opacity: .5;"></i>
                            Belum ada pesanan online yang sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection

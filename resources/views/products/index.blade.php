@extends('layouts.app')

@section('title', 'Katalog Obat & Manajemen Stok')

@push('styles')
<style>
    /* ==========================================================
       HALAMAN APOTEKER — GAYA SPESIFIK
       ========================================================== */

    /* ---------- Filter bar ---------- */
    .search-wrap { position: relative; }
    .search-wrap > .bi {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--ink-400); font-size: 14px; pointer-events: none;
    }
    .search-wrap .form-control {
        padding: 10px 14px 10px 40px;
        border-radius: 13px;
        border: 1.5px solid rgba(148, 163, 184, 0.26);
        background: rgba(255, 255, 255, 0.82);
        font-size: 13.5px;
    }
    .search-wrap .form-control::placeholder { color: var(--ink-400); }
    .select-wrap { position: relative; }
    .select-wrap > .bi {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        color: var(--ink-400); font-size: 14px; pointer-events: none; z-index: 5;
    }
    .select-wrap .form-select {
        padding: 10px 14px 10px 38px;
        border-radius: 13px;
        border: 1.5px solid rgba(148, 163, 184, 0.26);
        background: rgba(255, 255, 255, 0.82);
        font-size: 13.5px;
    }
    .filter-bar { margin-bottom: 18px; }

    /* ---------- Sel tabel ---------- */
    .med-name { font-weight: 600; color: var(--ink-900); font-size: 13.5px; line-height: 1.35; }
    .med-unit { color: var(--ink-400); font-size: 11px; }
    .stock-num { font-variant-numeric: tabular-nums; }
    .stock-hint { color: var(--ink-400); font-size: 10px; margin-top: 4px; }

    /* ---------- Kartu batch FEFO ---------- */
    .batch-list { display: flex; flex-direction: column; gap: 6px; max-width: 460px; }
    .batch-item {
        display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px;
        background: rgba(255, 255, 255, 0.68);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 11px;
        padding: 7px 11px;
        font-size: 11.5px;
        transition: all 0.15s ease;
    }
    .batch-item:hover { border-color: rgba(6, 182, 212, 0.4); background: #fff; }
    .batch-first { border-color: rgba(6, 182, 212, 0.45); background: linear-gradient(135deg, rgba(236, 254, 255, 0.85), rgba(240, 253, 250, 0.85)); }
    .batch-flag {
        display: inline-flex; align-items: center; gap: 4px;
        background: linear-gradient(135deg, var(--cyan-400), var(--cyan-600));
        color: #fff; border-radius: 6px;
        font-size: 9px; font-weight: 700; letter-spacing: 0.6px;
        padding: 2.5px 7px; text-transform: uppercase;
    }
    .batch-no {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-weight: 600; color: var(--ink-700); font-size: 11px;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .batch-no .bi { color: var(--cyan-600); font-size: 11px; }
    .batch-exp {
        display: inline-flex; align-items: center; gap: 5px;
        color: var(--ink-500); font-weight: 500;
        font-variant-numeric: tabular-nums;
    }
    .batch-exp .bi { color: var(--ink-400); }
    .hint-pill {
        display: inline-flex; align-items: center; gap: 4px;
        border-radius: 999px; padding: 2px 8px;
        font-size: 10px; font-weight: 700; letter-spacing: 0.2px;
    }
    .hint-ok      { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .hint-soon    { background: rgba(245, 158, 11, 0.16); color: #b45309; }
    .hint-expired { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .batch-stock { font-weight: 700; color: var(--ink-700); font-variant-numeric: tabular-nums; }
    .batch-price { font-weight: 600; color: var(--cyan-700); font-variant-numeric: tabular-nums; }

    /* ---------- Kolom status ---------- */
    .status-hint { color: var(--ink-400); font-size: 10px; margin-top: 4px; max-width: 150px; line-height: 1.45; }

    /* ---------- Legend chips ---------- */
    .legend-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .legend-chip {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10.5px; font-weight: 600; color: var(--ink-500);
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 999px; padding: 4px 11px;
    }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .dot-safe { background: #10b981; }
    .dot-low  { background: #f59e0b; }
    .dot-out  { background: #ef4444; }
    .dot-fefo { background: linear-gradient(135deg, var(--cyan-400), var(--cyan-600)); }

    /* ---------- Info pagination ---------- */
    .page-info { font-size: 12px; color: var(--ink-500); }
    .pagination { --bs-pagination-color: var(--ink-500); --bs-pagination-active-bg: var(--cyan-500); --bs-pagination-active-border-color: var(--cyan-500); --bs-pagination-hover-color: var(--cyan-700); --bs-pagination-border-radius: 10px; }
    .pagination .page-link { border-radius: 10px !important; margin: 0 2px; border: 1px solid rgba(148, 163, 184, 0.25); font-size: 13px; }
</style>
@endpush

@section('content')
<!-- ================= PAGE HEADER ================= -->
<div class="page-head">
    <div>
        <h1 class="page-title">
            <span class="title-icon"><i class="bi bi-boxes"></i></span>
            Katalog Obat &amp; Rincian Batch (FEFO)
        </h1>
        <p class="page-sub">Pantau stok riil per nomor batch dan tanggal kadaluarsa &mdash; penjualan kasir otomatis mengambil dari batch yang paling cepat kadaluarsa.</p>
    </div>
    <div class="text-lg-end">
        <span class="soft-chip mb-2">
            <i class="bi bi-collection" style="color: var(--cyan-600);"></i>
            {{ $products->total() }} produk terdaftar
        </span>
        <div class="legend-chips justify-content-lg-end">
            <span class="legend-chip"><span class="legend-dot dot-safe"></span> Aman (di atas min. stok)</span>
            <span class="legend-chip"><span class="legend-dot dot-low"></span> Menipis</span>
            <span class="legend-chip"><span class="legend-dot dot-out"></span> Habis</span>
            <span class="legend-chip"><span class="legend-dot dot-fefo"></span> Prioritas FEFO</span>
        </div>
    </div>
</div>

<div class="glass-card p-3 p-md-4">
    <!-- Filter Pencarian Obat & Kategori -->
    <form method="GET" action="{{ route('products.index') }}" class="row g-2 g-md-3 filter-bar">
        <div class="col-md-5">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari nama obat..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="select-wrap">
                <i class="bi bi-tags"></i>
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-cyan flex-fill"><i class="bi bi-funnel me-1"></i>Saring</button>
            <a href="{{ route('products.index') }}" class="btn btn-soft" title="Reset filter"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
        </div>
    </form>

    <!-- Tabel Master Obat & Batch FEFO -->
    <div class="table-responsive scroll-area">
        <table class="table table-modern table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th class="text-center">Total Stok</th>
                    <th>Rincian Batch Aktif &mdash; No. Batch | Exp Date | Stok | Harga</th>
                    <th class="text-center">Status Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>
                            <div class="med-name">{{ $p->name }}</div>
                            <small class="med-unit">Satuan: {{ $p->unit }}</small>
                        </td>
                        <td>
                            <span class="badge-cat">{{ $p->category?->name ?? 'Umum' }}</span>
                        </td>
                        <td class="text-center">
                            @if ($p->total_stock <= $p->min_stock)
                                <span class="pill pill-danger stock-num">
                                    <i class="bi bi-exclamation-triangle-fill"></i>{{ $p->total_stock }}
                                </span>
                            @else
                                <span class="pill pill-success stock-num">
                                    <i class="bi bi-check-circle-fill"></i>{{ $p->total_stock }}
                                </span>
                            @endif
                            <div class="stock-hint">Min. stok: {{ $p->min_stock }} {{ $p->unit }}</div>
                        </td>
                        <td>
                            @if ($p->batches->count() > 0)
                                <div class="batch-list">
                                    @foreach ($p->batches as $b)
                                        @php
                                            // Hitung jarak hari menuju tanggal kadaluarsa (hint FEFO)
                                            $daysLeft = (int) round(now()->startOfDay()->diffInDays(
                                                \Illuminate\Support\Carbon::parse($b->expired_date)->startOfDay()
                                            ));
                                            if ($daysLeft < 0) {
                                                $hintClass = 'hint-expired';
                                                $hintText  = 'Kadaluarsa';
                                            } elseif ($daysLeft === 0) {
                                                $hintClass = 'hint-soon';
                                                $hintText  = 'Exp hari ini';
                                            } elseif ($daysLeft <= 60) {
                                                $hintClass = 'hint-soon';
                                                $hintText  = $daysLeft . ' hari lagi';
                                            } else {
                                                $hintClass = 'hint-ok';
                                                $hintText  = $daysLeft . ' hari lagi';
                                            }
                                        @endphp
                                        <div class="batch-item {{ $loop->first ? 'batch-first' : '' }}">
                                            @if ($loop->first)
                                                <span class="batch-flag"><i class="bi bi-lightning-charge-fill"></i>FEFO</span>
                                            @endif
                                            <span class="batch-no"><i class="bi bi-box-seam"></i>{{ $b->batch_number }}</span>
                                            <span class="batch-exp"><i class="bi bi-calendar-event"></i>{{ $b->expired_date->format('d/m/Y') }}</span>
                                            <span class="hint-pill {{ $hintClass }}">
                                                @if ($daysLeft < 0)<i class="bi bi-x-octagon"></i>@elseif ($daysLeft <= 60)<i class="bi bi-hourglass-split"></i>@else<i class="bi bi-clock"></i>@endif
                                                {{ $hintText }}
                                            </span>
                                            <span class="batch-stock">{{ $b->stock }} {{ $p->unit }}</span>
                                            <span class="batch-price">Rp {{ number_format($b->sell_price, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="pill pill-danger"><i class="bi bi-slash-circle"></i>Tidak Ada Batch Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($p->total_stock > $p->min_stock)
                                <span class="pill pill-success"><i class="bi bi-shield-check"></i>Aman</span>
                                <div class="status-hint">Stok riil {{ $p->total_stock }} di atas minimum ({{ $p->min_stock }})</div>
                            @elseif ($p->total_stock > 0)
                                <span class="pill pill-warning"><i class="bi bi-exclamation-triangle-fill"></i>Menipis</span>
                                <div class="status-hint">Sisa {{ $p->total_stock }} dari min. {{ $p->min_stock }} {{ $p->unit }} &mdash; segera restok</div>
                            @else
                                <span class="pill pill-danger"><i class="bi bi-x-octagon-fill"></i>Habis</span>
                                <div class="status-hint">Stok riil habis / seluruh batch kadaluarsa</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-search" style="font-size: 2rem; color: var(--ink-400); opacity: .5;"></i>
                            <div class="text-muted mt-2">Data obat tidak ditemukan.<br>Coba ubah kata kunci atau kategori pencarian.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info & Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="page-info">
            @if ($products->total() > 0)
                Menampilkan <strong>{{ $products->firstItem() }}&ndash;{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
            @else
                Tidak ada produk untuk ditampilkan.
            @endif
        </span>
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

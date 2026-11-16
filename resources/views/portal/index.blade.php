@extends('layouts.portal')

@section('title', 'Belanja Obat Online')

@push('styles')
<style>
    .hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: clamp(28px, 5vw, 54px);
        background:
            radial-gradient(700px 420px at 88% -12%, rgba(103, 232, 249, 0.38), transparent 62%),
            radial-gradient(520px 340px at 4% 110%, rgba(45, 212, 191, 0.26), transparent 64%),
            linear-gradient(135deg, #083344 0%, #0e7490 56%, #0891b2 100%);
        color: #fff;
        box-shadow: 0 24px 60px rgba(8, 47, 63, 0.28);
    }
    .hero::after {
        content: '';
        position: absolute;
        width: 360px; height: 360px;
        border-radius: 50%;
        right: -120px; bottom: -160px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.14), transparent 68%);
        pointer-events: none;
    }
    .hero-eyebrow {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 999px; padding: 6px 13px;
        font-size: 11px; font-weight: 700; letter-spacing: .4px;
        color: var(--cyan-100);
    }
    .hero-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: clamp(1.85rem, 4.1vw, 3.05rem);
        line-height: 1.12;
        margin: 16px 0 14px;
        letter-spacing: -0.6px;
    }
    .hero-title .accent { color: var(--cyan-300); }
    .hero-text { color: rgba(255, 255, 255, 0.82); font-size: 14.5px; line-height: 1.7; max-width: 540px; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
    .btn-hero-light {
        background: #fff; color: var(--cyan-700); border: none;
        font-weight: 800; border-radius: 13px; padding: 12px 20px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.14);
        transition: all .2s ease;
    }
    .btn-hero-light:hover { color: var(--cyan-700); transform: translateY(-2px); }
    .btn-hero-ghost {
        background: rgba(255, 255, 255, 0.10); color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.26);
        font-weight: 700; border-radius: 13px; padding: 12px 20px;
        transition: all .2s ease;
    }
    .btn-hero-ghost:hover { background: rgba(255, 255, 255, 0.18); color: #fff; }
    .hero-stats { display: flex; flex-wrap: wrap; gap: 22px; margin-top: 28px; }
    .hero-stat strong { display: block; font-family: 'Poppins', sans-serif; font-size: 1.35rem; font-weight: 800; color: #fff; }
    .hero-stat span { font-size: 11px; color: rgba(255, 255, 255, 0.66); font-weight: 600; letter-spacing: .3px; }
    .hero-visual {
        position: relative;
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 22px;
        padding: 20px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }
    .hero-visual-title { font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: var(--cyan-200); }
    .hero-step { display: flex; gap: 12px; align-items: flex-start; padding: 13px 0; border-bottom: 1px dashed rgba(255, 255, 255, 0.17); }
    .hero-step:last-child { border-bottom: none; padding-bottom: 0; }
    .hero-step-icon {
        width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.15); color: #fff; font-size: 1rem;
    }
    .hero-step-title { font-weight: 700; font-size: 12.5px; color: #fff; line-height: 1.3; }
    .hero-step-sub { font-size: 10.5px; color: rgba(255, 255, 255, 0.62); margin-top: 2px; }

    .feature-card {
        background: rgba(255, 255, 255, 0.84);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 17px;
        padding: 17px;
        height: 100%;
        box-shadow: 0 4px 16px rgba(8, 145, 178, 0.05);
    }
    .feature-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.1rem; margin-bottom: 11px;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.13), rgba(8, 145, 178, 0.2));
        color: var(--cyan-700);
    }
    .feature-title { font-weight: 800; font-size: 13px; color: var(--ink-900); margin-bottom: 4px; }
    .feature-text { font-size: 11.5px; color: var(--ink-500); line-height: 1.55; margin: 0; }

    .filter-card { padding: 16px; }
    .search-wrap { position: relative; }
    .search-wrap > .bi { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--ink-400); font-size: 14px; pointer-events: none; }
    .search-wrap .form-control { padding-left: 38px; }
    .category-pill {
        display: inline-flex; align-items: center; gap: 6px;
        border-radius: 999px; padding: 7px 13px;
        border: 1px solid rgba(148, 163, 184, 0.26);
        background: rgba(255, 255, 255, 0.8);
        font-size: 11.5px; font-weight: 700; color: var(--ink-500);
        text-decoration: none; transition: all .16s ease; white-space: nowrap;
    }
    .category-pill:hover { color: var(--cyan-700); border-color: rgba(6, 182, 212, 0.45); background: #fff; }
    .category-pill.active { color: #fff; border-color: transparent; background: linear-gradient(135deg, var(--cyan-500), var(--cyan-700)); box-shadow: 0 5px 14px rgba(6, 182, 212, .26); }
    .catalog-count { font-size: 12px; color: var(--ink-500); }
    .catalog-count strong { color: var(--ink-900); }

    .about-teaser {
        border-radius: 24px;
        padding: clamp(24px, 4vw, 40px);
        background:
            radial-gradient(560px 320px at 0% 0%, rgba(103, 232, 249, 0.24), transparent 60%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(240, 253, 250, 0.93));
        border: 1px solid rgba(6, 182, 212, 0.18);
        box-shadow: var(--shadow-soft);
    }
    .info-tile {
        display: flex; gap: 12px; align-items: flex-start;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 14px; padding: 13px;
    }
    .info-tile-icon {
        width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(6, 182, 212, 0.10); color: var(--cyan-700);
    }
    .info-tile-title { font-size: 11.5px; font-weight: 800; color: var(--ink-900); }
    .info-tile-text { font-size: 11px; color: var(--ink-500); line-height: 1.5; }
</style>
@endpush

@section('content')
<div class="container">
    {{-- ================= HERO ================= --}}
    <section class="hero">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="hero-eyebrow"><i class="bi bi-shield-check"></i>Apotek terpercaya di Punggur</span>
                <h1 class="hero-title">Belanja obat lebih mudah,<br><span class="accent">tanpa harus antre.</span></h1>
                <p class="hero-text">
                    Lihat stok dan harga obat secara real-time, pesan dari rumah, lalu pilih
                    ambil sendiri di toko atau kirim langsung ke alamat Anda. Bayar dengan
                    COD, transfer bank, atau QRIS.
                </p>
                <div class="hero-actions">
                    <a href="#katalog" class="btn btn-hero-light"><i class="bi bi-bag-plus me-1"></i>Mulai Belanja</a>
                    <a href="{{ route('portal.address') }}" class="btn btn-hero-ghost"><i class="bi bi-geo-alt me-1"></i>Lihat Alamat Toko</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong>{{ number_format($products->count(), 0, ',', '.') }}+</strong>
                        <span>Produk Obat</span>
                    </div>
                    <div class="hero-stat">
                        <strong>{{ number_format($categories->count(), 0, ',', '.') }}</strong>
                        <span>Kategori</span>
                    </div>
                    <div class="hero-stat">
                        <strong>3</strong>
                        <span>Metode Bayar</span>
                    </div>
                    <div class="hero-stat">
                        <strong>2</strong>
                        <span>Opsi Pengiriman</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-visual">
                    <div class="hero-visual-title mb-1">Cara Pesan</div>
                    <div class="hero-step">
                        <span class="hero-step-icon"><i class="bi bi-search"></i></span>
                        <div>
                            <div class="hero-step-title">Pilih obat dari katalog</div>
                            <div class="hero-step-sub">Stok &amp; harga selalu diperbarui dari sistem apotek.</div>
                        </div>
                    </div>
                    <div class="hero-step">
                        <span class="hero-step-icon"><i class="bi bi-truck"></i></span>
                        <div>
                            <div class="hero-step-title">Tentukan pengiriman</div>
                            <div class="hero-step-sub">Ambil sendiri di toko atau kirim ke alamat lengkap.</div>
                        </div>
                    </div>
                    <div class="hero-step">
                        <span class="hero-step-icon"><i class="bi bi-wallet2"></i></span>
                        <div>
                            <div class="hero-step-title">Bayar sesuai pilihan</div>
                            <div class="hero-step-sub">COD, transfer bank, atau scan QRIS.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= KEUNGGULAN ================= --}}
    <section class="row g-3 mt-1">
        <div class="col-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                <div class="feature-title">Stok Real-Time</div>
                <p class="feature-text">Jumlah stok diambil langsung dari batch obat yang masih aktif.</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="feature-title">Bayar Fleksibel</div>
                <p class="feature-text">COD, transfer bank, atau QRIS sesuai kenyamanan Anda.</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-truck"></i></div>
                <div class="feature-title">Antar atau Ambil</div>
                <p class="feature-text">Pilih kirim ke alamat atau ambil sendiri di apotek.</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-patch-check"></i></div>
                <div class="feature-title">Obat Terjamin</div>
                <p class="feature-text">Produk dikelola apoteker dengan pengawasan batch &amp; ED.</p>
            </div>
        </div>
    </section>

    {{-- ================= KATALOG ================= --}}
    <section id="katalog" class="mt-4 pt-2">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
            <div>
                <span class="section-eyebrow"><i class="bi bi-grid-3x3-gap"></i>Katalog Obat</span>
                <h2 class="section-title">Temukan obat yang Anda butuhkan</h2>
                <p class="section-sub">Harga dan stok ditampilkan secara real-time dari sistem apotek.</p>
            </div>
            <span class="badge-soft"><i class="bi bi-arrow-repeat"></i>Diperbarui {{ now()->format('d/m/Y H:i') }} WIB</span>
        </div>

        <div class="portal-card filter-card mb-3">
            <form method="GET" action="{{ route('portal.index') }}#katalog" class="row g-2 align-items-end">
                <div class="col-lg-5">
                    <label class="form-label" for="searchInput"><i class="bi bi-search me-1"></i>Cari obat atau kategori</label>
                    <div class="search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" name="q" class="form-control" value="{{ $search }}"
                               placeholder="Contoh: paracetamol, vitamin, sirup...">
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <label class="form-label" for="categorySelect"><i class="bi bi-tags me-1"></i>Kategori</label>
                    <select id="categorySelect" name="kategori" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) $categoryId === (int) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <button type="submit" class="btn btn-portal w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
                <div class="col-lg-2">
                    <a href="{{ route('portal.index') }}#katalog" class="btn btn-portal-outline w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                </div>
            </form>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <a href="{{ route('portal.index') }}#katalog" class="category-pill {{ ! $categoryId ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>Semua
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('portal.index', ['kategori' => $category->id, 'q' => $search]) }}#katalog"
                       class="category-pill {{ (int) $categoryId === (int) $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="catalog-count">
                Menampilkan <strong>{{ number_format($products->count(), 0, ',', '.') }}</strong> obat
                @if ($search !== '')
                    untuk pencarian &ldquo;<strong>{{ $search }}</strong>&rdquo;
                @endif
            </span>
            <span class="soft-chip d-none d-md-inline-flex"><i class="bi bi-shield-check text-info"></i>Stok aktif &amp; belum kadaluarsa</span>
        </div>

        <div class="row g-3">
            @forelse ($products as $product)
                @php
                    $stock = (int) $product->display_stock;
                    $stockClass = $stock <= 0 ? 'stock-empty' : ($stock <= 10 ? 'stock-low' : 'stock-safe');
                    $stockText = $stock <= 0 ? 'Stok habis' : ($stock <= 10 ? "Stok terbatas ({$stock})" : "Stok tersedia ({$stock})");
                @endphp
                <div class="col-6 col-md-4 col-xl-3">
                    <article class="product-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="product-icon"><i class="bi bi-capsule-pill"></i></span>
                            @if ($product->nearest_expiry)
                                <span class="badge-soft" title="Batch terdekat yang akan dipakai (FEFO)">
                                    <i class="bi bi-calendar2-check"></i>{{ $product->nearest_expiry->format('m/Y') }}
                                </span>
                            @endif
                        </div>
                        <div class="product-cat">{{ $product->category?->name ?? 'Umum' }}</div>
                        <h3 class="product-name">{{ $product->name }}</h3>
                        <div class="product-unit">Per {{ $product->unit }}</div>

                        <div class="mt-auto pt-3">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <span class="product-price">{{ $stock > 0 ? 'Rp ' . number_format($product->display_price, 0, ',', '.') : '—' }}</span>
                                <span class="product-stock"><span class="stock-dot {{ $stockClass }}"></span>{{ $stockText }}</span>
                            </div>
                            <button type="button" class="btn-add-cart" data-add-to-cart
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ (float) $product->display_price }}"
                                    data-unit="{{ $product->unit }}"
                                    data-stock="{{ $stock }}"
                                    data-category="{{ $product->category?->name ?? 'Umum' }}"
                                    {{ $stock <= 0 ? 'disabled' : '' }}>
                                @if ($stock <= 0)
                                    <i class="bi bi-slash-circle me-1"></i>Stok Habis
                                @else
                                    <i class="bi bi-plus-lg me-1"></i>Tambah ke Keranjang
                                @endif
                            </button>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="portal-card p-5 text-center">
                        <i class="bi bi-search text-secondary opacity-50" style="font-size: 2.8rem;"></i>
                        <h5 class="fw-bold mt-3 mb-1">Obat tidak ditemukan</h5>
                        <p class="text-muted small mb-3">Coba kata kunci lain atau lihat semua kategori.</p>
                        <a href="{{ route('portal.index') }}#katalog" class="btn btn-portal-outline">Lihat Semua Obat</a>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ================= TENTANG & ALAMAT ================= --}}
    <section class="about-teaser mt-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <span class="section-eyebrow"><i class="bi bi-shop"></i>Tentang Toko</span>
                <h2 class="section-title">Apotek Sehat Sehati, mitra sehat keluarga Punggur</h2>
                <p class="text-muted" style="font-size: 13.5px; line-height: 1.75;">
                    Kami melayani kebutuhan obat dan produk kesehatan masyarakat Punggur dan sekitarnya.
                    Kini Anda bisa melihat katalog, memesan, dan memilih pengiriman langsung dari rumah.
                    Setiap pesanan tetap diproses oleh petugas apotek agar aman dan tepat.
                </p>
                <div class="row g-2 mt-1">
                    <div class="col-sm-6">
                        <div class="info-tile">
                            <span class="info-tile-icon"><i class="bi bi-geo-alt-fill"></i></span>
                            <div>
                                <div class="info-tile-title">Alamat Toko</div>
                                <div class="info-tile-text">{{ config('pharmacy.address') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="info-tile">
                            <span class="info-tile-icon"><i class="bi bi-clock-fill"></i></span>
                            <div>
                                <div class="info-tile-title">Jam Buka</div>
                                <div class="info-tile-text">Senin - Sabtu 07.30 - 21.00 WIB<br>Minggu 08.00 - 20.00 WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('portal.about') }}" class="btn btn-portal"><i class="bi bi-info-circle me-1"></i>Tentang Toko</a>
                    <a href="{{ route('portal.address') }}" class="btn btn-portal-outline"><i class="bi bi-map me-1"></i>Alamat &amp; Peta</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="portal-card p-3 h-100 text-center">
                            <div class="feature-icon mx-auto"><i class="bi bi-cash-coin"></i></div>
                            <div class="feature-title">COD</div>
                            <p class="feature-text">Bayar saat obat diterima atau diambil.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="portal-card p-3 h-100 text-center">
                            <div class="feature-icon mx-auto"><i class="bi bi-bank2"></i></div>
                            <div class="feature-title">Transfer Bank</div>
                            <p class="feature-text">Mandiri, BRI, dan BSI tersedia.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="portal-card p-3 h-100 text-center">
                            <div class="feature-icon mx-auto"><i class="bi bi-qr-code"></i></div>
                            <div class="feature-title">QRIS</div>
                            <p class="feature-text">Scan sekali, bayar dari e-wallet favorit.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="portal-card p-3 h-100 text-center">
                            <div class="feature-icon mx-auto"><i class="bi bi-truck"></i></div>
                            <div class="feature-title">Kirim / Ambil</div>
                            <p class="feature-text">Pilih sesuai kebutuhan Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

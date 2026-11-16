@extends('layouts.portal')

@section('title', 'Tentang Toko')

@push('styles')
<style>
    .about-hero {
        border-radius: 26px;
        padding: clamp(28px, 5vw, 50px);
        background:
            radial-gradient(600px 360px at 92% -12%, rgba(103, 232, 249, 0.36), transparent 62%),
            radial-gradient(520px 320px at 4% 110%, rgba(45, 212, 191, 0.24), transparent 64%),
            linear-gradient(135deg, #083344 0%, #0e7490 58%, #0891b2 100%);
        color: #fff;
        box-shadow: 0 24px 60px rgba(8, 47, 63, 0.26);
        position: relative;
        overflow: hidden;
    }
    .about-hero::after {
        content: '';
        position: absolute; width: 340px; height: 340px; border-radius: 50%;
        right: -110px; bottom: -150px;
        background: radial-gradient(circle, rgba(255,255,255,.13), transparent 68%);
    }
    .about-hero h1 { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(1.7rem, 3.6vw, 2.6rem); line-height: 1.16; letter-spacing: -.4px; }
    .about-hero p { color: rgba(255,255,255,.8); font-size: 14px; line-height: 1.75; }
    .stat-card { text-align: center; padding: 18px 12px; }
    .stat-card strong { display: block; font-family: 'Poppins', sans-serif; font-size: 1.55rem; font-weight: 800; color: var(--cyan-700); }
    .stat-card span { font-size: 11px; font-weight: 700; color: var(--ink-500); letter-spacing: .3px; }
    .value-card { padding: 19px; height: 100%; }
    .value-icon { width: 46px; height: 46px; border-radius: 13px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(6,182,212,.13), rgba(8,145,178,.2)); color: var(--cyan-700); font-size: 1.15rem; margin-bottom: 12px; }
    .value-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 13.5px; color: var(--ink-900); }
    .value-text { font-size: 11.5px; color: var(--ink-500); line-height: 1.6; margin: 4px 0 0; }
    .hours-row { display: flex; justify-content: space-between; gap: 12px; padding: 9px 0; border-bottom: 1px dashed rgba(148,163,184,.22); font-size: 12.5px; }
    .hours-row:last-child { border-bottom: none; }
    .hours-row span:first-child { color: var(--ink-500); font-weight: 600; }
    .hours-row span:last-child { color: var(--ink-900); font-weight: 800; }
    .contact-tile { display: flex; gap: 12px; align-items: flex-start; padding: 12px; border: 1px solid rgba(148,163,184,.18); background: rgba(255,255,255,.75); border-radius: 13px; height: 100%; }
    .contact-tile i { color: var(--cyan-600); font-size: 1.05rem; margin-top: 2px; }
    .contact-tile-label { font-size: 10px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; color: var(--ink-400); }
    .contact-tile-value { font-size: 12px; font-weight: 600; color: var(--ink-900); line-height: 1.5; }
</style>
@endpush

@section('content')
<div class="container">
    <section class="about-hero mb-4">
        <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
            <div class="col-lg-8">
                <span class="hero-eyebrow"><i class="bi bi-shop"></i>Tentang {{ config('pharmacy.name') }}</span>
                <h1 class="mt-3 mb-3">Mitra sehat keluarga<br>Punggur dan sekitarnya.</h1>
                <p class="mb-0">
                    {{ config('pharmacy.name') }} hadir melayani kebutuhan obat dan produk kesehatan
                    masyarakat Punggur, Lampung Tengah. Kami percaya bahwa akses terhadap obat yang
                    aman dan terpercaya harus mudah dijangkau, termasuk melalui layanan pesan online
                    seperti sekarang.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="p-3 rounded-4" style="background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.18);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-patch-check-fill" style="color: var(--cyan-300); font-size: 1.25rem;"></i>
                        <span class="fw-bold" style="font-size: 12.5px;">Komitmen Kami</span>
                    </div>
                    <p class="mb-0" style="font-size: 11.5px; color: rgba(255,255,255,.74); line-height: 1.6;">
                        Obat asli, penyimpanan sesuai standar, pengawasan batch dan tanggal kadaluarsa,
                        serta pelayanan yang ramah untuk setiap pelanggan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="portal-card stat-card">
                <strong>{{ number_format($stats['products'], 0, ',', '.') }}+</strong>
                <span>Produk Obat</span>
            </div>
        </div>
        <div class="col-4">
            <div class="portal-card stat-card">
                <strong>{{ number_format($stats['categories'], 0, ',', '.') }}</strong>
                <span>Kategori</span>
            </div>
        </div>
        <div class="col-4">
            <div class="portal-card stat-card">
                <strong>{{ number_format($stats['customers'], 0, ',', '.') }}+</strong>
                <span>Pelanggan Terlayani</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="portal-card p-4 h-100">
                <span class="section-eyebrow"><i class="bi bi-heart-pulse"></i>Cerita Kami</span>
                <h2 class="section-title">Melayani dengan sepenuh hati</h2>
                <p class="text-muted" style="font-size: 13.5px; line-height: 1.8;">
                    Berawal dari kebutuhan warga sekitar akan layanan kesehatan yang dekat dan mudah
                    dijangkau, {{ config('pharmacy.name') }} terus berupaya menghadirkan pelayanan
                    kefarmasian yang profesional. Kami tidak hanya menyediakan obat bebas, tetapi juga
                    produk kesehatan sehari-hari, vitamin, dan kebutuhan perawatan lainnya.
                </p>
                <p class="text-muted" style="font-size: 13.5px; line-height: 1.8;">
                    Melalui portal pelanggan ini, Anda dapat melihat katalog obat secara real-time,
                    memesan tanpa harus datang ke toko, memilih pengiriman atau pengambilan di toko,
                    serta membayar dengan COD, transfer bank, atau QRIS. Setiap pesanan tetap
                    diverifikasi oleh petugas apotek sebelum diproses.
                </p>

                <div class="row g-2 mt-2">
                    <div class="col-sm-6">
                        <div class="contact-tile">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <div class="contact-tile-label">Alamat</div>
                                <div class="contact-tile-value">{{ config('pharmacy.address') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="contact-tile">
                            <i class="bi bi-telephone-fill"></i>
                            <div>
                                <div class="contact-tile-label">Telepon / WhatsApp</div>
                                <div class="contact-tile-value">{{ config('pharmacy.phone') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="contact-tile">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <div class="contact-tile-label">Email</div>
                                <div class="contact-tile-value">{{ config('pharmacy.email') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="contact-tile">
                            <i class="bi bi-clock-fill"></i>
                            <div>
                                <div class="contact-tile-label">Jam Operasional</div>
                                <div class="contact-tile-value">Senin - Sabtu 07.30 - 21.00<br>Minggu 08.00 - 20.00 WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="portal-card p-4 mb-3">
                <h2 class="section-title" style="font-size: 1.15rem;">Nilai yang kami pegang</h2>
                <div class="row g-2">
                    <div class="col-12">
                        <div class="value-card">
                            <div class="d-flex gap-3">
                                <span class="value-icon mb-0"><i class="bi bi-shield-check"></i></span>
                                <div>
                                    <div class="value-title">Aman &amp; Terpercaya</div>
                                    <p class="value-text">Produk berasal dari sumber resmi dan disimpan sesuai ketentuan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="value-card">
                            <div class="d-flex gap-3">
                                <span class="value-icon mb-0"><i class="bi bi-people"></i></span>
                                <div>
                                    <div class="value-title">Ramah &amp; Membantu</div>
                                    <p class="value-text">Petugas siap membantu memilih obat dan menjelaskan penggunaannya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="value-card">
                            <div class="d-flex gap-3">
                                <span class="value-icon mb-0"><i class="bi bi-lightning-charge"></i></span>
                                <div>
                                    <div class="value-title">Cepat &amp; Mudah</div>
                                    <p class="value-text">Pesan online, pilih antar atau ambil, lalu bayar sesuai kenyamanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="portal-card p-4">
                <h2 class="section-title" style="font-size: 1.15rem;">Jam Buka</h2>
                @foreach (config('pharmacy.hours') as $hour)
                    <div class="hours-row">
                        <span>{{ $hour['day'] }}</span>
                        <span>{{ $hour['time'] }}</span>
                    </div>
                @endforeach
                <a href="{{ route('portal.address') }}" class="btn btn-portal-outline w-100 mt-3">
                    <i class="bi bi-map me-1"></i>Lihat Lokasi &amp; Peta
                </a>
            </div>
        </div>
    </div>

    <section class="portal-card p-4 mt-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="section-eyebrow"><i class="bi bi-bag-heart"></i>Belanja Sekarang</span>
                <h2 class="section-title">Siap memesan obat dari rumah?</h2>
                <p class="section-sub">Lihat katalog lengkap dengan harga dan stok real-time, lalu buat pesanan dalam beberapa menit.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('portal.index') }}#katalog" class="btn btn-portal px-4"><i class="bi bi-bag-plus me-1"></i>Buka Katalog</a>
            </div>
        </div>
    </section>
</div>
@endsection

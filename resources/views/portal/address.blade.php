@extends('layouts.portal')

@section('title', 'Alamat Toko')

@push('styles')
<style>
    .address-hero {
        border-radius: 24px;
        padding: clamp(24px, 4vw, 40px);
        background:
            radial-gradient(520px 320px at 92% -12%, rgba(103, 232, 249, 0.34), transparent 60%),
            linear-gradient(135deg, #083344, #0e7490 58%, #0891b2);
        color: #fff;
        box-shadow: 0 22px 54px rgba(8, 47, 63, 0.24);
        position: relative;
        overflow: hidden;
    }
    .address-hero::after {
        content: '';
        position: absolute; width: 300px; height: 300px; border-radius: 50%;
        right: -100px; bottom: -130px;
        background: radial-gradient(circle, rgba(255,255,255,.13), transparent 68%);
    }
    .address-hero h1 { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(1.6rem, 3.2vw, 2.35rem); }
    .address-hero p { color: rgba(255,255,255,.8); font-size: 13.5px; line-height: 1.75; }
    .map-card { padding: 12px; overflow: hidden; }
    .map-frame { width: 100%; height: 430px; border: 0; border-radius: 15px; display: block; }
    .direction-card { padding: 20px; }
    .direction-step { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px dashed rgba(148,163,184,.22); }
    .direction-step:last-child { border-bottom: none; }
    .direction-num { width: 28px; height: 28px; border-radius: 9px; background: linear-gradient(135deg, var(--cyan-500), var(--cyan-700)); color: #fff; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .direction-title { font-size: 12px; font-weight: 800; color: var(--ink-900); }
    .direction-text { font-size: 11px; color: var(--ink-500); line-height: 1.55; }
    .contact-tile { display: flex; gap: 12px; align-items: flex-start; padding: 13px; border: 1px solid rgba(148,163,184,.18); background: rgba(255,255,255,.8); border-radius: 13px; height: 100%; }
    .contact-tile i { color: var(--cyan-600); font-size: 1.05rem; margin-top: 2px; }
    .contact-tile-label { font-size: 10px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; color: var(--ink-400); }
    .contact-tile-value { font-size: 12px; font-weight: 600; color: var(--ink-900); line-height: 1.5; }
    .hours-row { display: flex; justify-content: space-between; gap: 12px; padding: 9px 0; border-bottom: 1px dashed rgba(148,163,184,.22); font-size: 12.5px; }
    .hours-row:last-child { border-bottom: none; }
    .hours-row span:first-child { color: var(--ink-500); font-weight: 600; }
    .hours-row span:last-child { color: var(--ink-900); font-weight: 800; }
</style>
@endpush

@section('content')
<div class="container">
    <section class="address-hero mb-4">
        <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
            <div class="col-lg-8">
                <span class="hero-eyebrow"><i class="bi bi-geo-alt"></i>Alamat Toko</span>
                <h1 class="mt-3 mb-2">Temukan kami di Punggur</h1>
                <p class="mb-0">{{ config('pharmacy.address') }}</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('pharmacy.maps_query')) }}"
                   target="_blank" rel="noopener" class="btn btn-hero-light">
                    <i class="bi bi-signpost-split me-1"></i>Buka di Google Maps
                </a>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="portal-card map-card mb-3">
                <iframe class="map-frame"
                        src="https://www.google.com/maps?q={{ urlencode(config('pharmacy.maps_query')) }}&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta lokasi {{ config('pharmacy.name') }}"></iframe>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="contact-tile">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <div class="contact-tile-label">Alamat Lengkap</div>
                            <div class="contact-tile-value">{{ config('pharmacy.address') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-tile">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <div class="contact-tile-label">Telepon / WhatsApp</div>
                            <div class="contact-tile-value">
                                <a href="https://wa.me/{{ config('pharmacy.whatsapp') }}" target="_blank" rel="noopener"
                                   class="text-decoration-none" style="color: var(--ink-900);">{{ config('pharmacy.phone') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-tile">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <div class="contact-tile-label">Email</div>
                            <div class="contact-tile-value">{{ config('pharmacy.email') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-tile">
                        <i class="bi bi-truck"></i>
                        <div>
                            <div class="contact-tile-label">Layanan</div>
                            <div class="contact-tile-value">Ambil di toko &amp; kirim ke alamat area Punggur</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="portal-card direction-card mb-3">
                <h2 class="section-title" style="font-size: 1.15rem;">Petunjuk Arah</h2>
                <div class="direction-step">
                    <span class="direction-num">1</span>
                    <div>
                        <div class="direction-title">Dari Kota Metro</div>
                        <div class="direction-text">Ambil arah Jl. Metro - Kotagajah menuju Punggur.</div>
                    </div>
                </div>
                <div class="direction-step">
                    <span class="direction-num">2</span>
                    <div>
                        <div class="direction-title">Masuk Dusun Tanggul Angin</div>
                        <div class="direction-text">Patokan: dekat area pertokoan Punggur, Lampung Tengah.</div>
                    </div>
                </div>
                <div class="direction-step">
                    <span class="direction-num">3</span>
                    <div>
                        <div class="direction-title">Tiba di Apotek</div>
                        <div class="direction-text">Cari papan nama {{ config('pharmacy.name') }}. Parkir tersedia di depan toko.</div>
                    </div>
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('pharmacy.maps_query')) }}"
                   target="_blank" rel="noopener" class="btn btn-portal w-100 mt-3">
                    <i class="bi bi-navigation me-1"></i>Navigasi Sekarang
                </a>
            </div>

            <div class="portal-card p-4 mb-3">
                <h2 class="section-title" style="font-size: 1.15rem;">Jam Operasional</h2>
                @foreach (config('pharmacy.hours') as $hour)
                    <div class="hours-row">
                        <span>{{ $hour['day'] }}</span>
                        <span>{{ $hour['time'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="portal-card p-4">
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-info-circle-fill" style="color: var(--cyan-600); margin-top: 2px;"></i>
                    <div class="checkout-note">
                        Ingin pesan online? Kembali ke
                        <a href="{{ route('portal.index') }}#katalog" class="fw-bold text-decoration-none" style="color: var(--cyan-700);">katalog obat</a>
                        dan pilih <strong>Ambil Sendiri di Toko</strong> atau <strong>Kirim ke Alamat</strong> saat checkout.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

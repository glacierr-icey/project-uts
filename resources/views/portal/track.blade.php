@extends('layouts.portal')

@section('title', 'Lacak Pesanan')

@push('styles')
<style>
    .track-hero {
        border-radius: 24px;
        padding: clamp(24px, 4vw, 38px);
        background:
            radial-gradient(520px 300px at 92% -12%, rgba(103, 232, 249, 0.32), transparent 62%),
            linear-gradient(135deg, #083344, #0e7490 58%, #0891b2);
        color: #fff;
        box-shadow: 0 20px 50px rgba(8, 47, 63, 0.24);
    }
    .track-hero h1 { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(1.55rem, 3vw, 2.2rem); }
    .track-hero p { color: rgba(255,255,255,.78); font-size: 13px; }
    .track-card { padding: 22px; }
    .track-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; background: rgba(6,182,212,.11); color: var(--cyan-700); font-size: 1.25rem; }
    .track-tip { display: flex; gap: 10px; align-items: flex-start; padding: 9px 0; border-bottom: 1px dashed rgba(148,163,184,.2); font-size: 12px; color: var(--ink-500); }
    .track-tip:last-child { border-bottom: none; }
    .track-tip i { color: var(--cyan-600); margin-top: 2px; }
</style>
@endpush

@section('content')
<div class="container">
    <section class="track-hero mb-4">
        <span class="hero-eyebrow"><i class="bi bi-search"></i>Lacak Pesanan</span>
        <h1 class="mt-3 mb-2">Cek status pesanan Anda</h1>
        <p class="mb-0">Masukkan nomor invoice yang Anda terima setelah checkout untuk melihat detail dan status pesanan.</p>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="portal-card track-card">
                <div class="d-flex gap-3 align-items-start mb-3">
                    <span class="track-icon"><i class="bi bi-receipt-cutoff"></i></span>
                    <div>
                        <h2 class="section-title mb-1" style="font-size: 1.2rem;">Nomor Invoice</h2>
                        <p class="section-sub" style="font-size: 12px;">Contoh format: <strong>ORD-20261113-ABC12</strong></p>
                    </div>
                </div>

                <form method="POST" action="{{ route('portal.track.search') }}">
                    @csrf
                    <label class="form-label" for="invoice_number">Nomor Pesanan / Invoice</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                            <i class="bi bi-hash text-muted"></i>
                        </span>
                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                               id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}"
                               placeholder="ORD-20261113-ABC12" autocomplete="off" required
                               style="border-radius: 0 12px 12px 0; text-transform: uppercase;">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-portal w-100 mt-3">
                        <i class="bi bi-search me-1"></i>Cari Pesanan
                    </button>
                </form>

                <div class="mt-3 p-3 rounded-3" style="background: rgba(6,182,212,.06);">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-info-circle-fill" style="color: var(--cyan-600); margin-top: 2px;"></i>
                        <div style="font-size: 11.5px; color: var(--ink-500); line-height: 1.6;">
                            Nomor invoice dikirim pada halaman konfirmasi setelah Anda menekan
                            <strong>Kirim Pesanan</strong>. Simpan nomor tersebut untuk melacak pesanan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="portal-card track-card mb-3">
                <h2 class="section-title" style="font-size: 1.1rem;">Butuh bantuan?</h2>
                <div class="track-tip">
                    <i class="bi bi-whatsapp"></i>
                    <span>Hubungi WhatsApp apotek di <strong>{{ config('pharmacy.phone') }}</strong> dengan menyebutkan nomor invoice.</span>
                </div>
                <div class="track-tip">
                    <i class="bi bi-shop"></i>
                    <span>Atau datang langsung ke {{ config('pharmacy.name') }}, {{ config('pharmacy.address') }}.</span>
                </div>
                <div class="track-tip">
                    <i class="bi bi-clock"></i>
                    <span>Senin - Sabtu 07.30 - 21.00 WIB, Minggu 08.00 - 20.00 WIB.</span>
                </div>
            </div>

            <div class="portal-card p-4">
                <h2 class="section-title" style="font-size: 1.1rem;">Belum pernah memesan?</h2>
                <p class="section-sub mb-3" style="font-size: 12px;">Lihat katalog obat dan buat pesanan pertama Anda sekarang.</p>
                <a href="{{ route('portal.index') }}#katalog" class="btn btn-portal w-100">
                    <i class="bi bi-bag-plus me-1"></i>Buka Katalog Obat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

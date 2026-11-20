@extends('layouts.portal')

@section('title', 'Detail Pesanan ' . $order->invoice_number)

@push('styles')
<style>
    .order-hero {
        border-radius: 24px;
        padding: clamp(22px, 3.4vw, 34px);
        background:
            radial-gradient(520px 300px at 92% -10%, rgba(103, 232, 249, 0.32), transparent 62%),
            linear-gradient(135deg, #083344, #0e7490 60%, #0891b2);
        color: #fff;
        box-shadow: 0 20px 50px rgba(8, 47, 63, 0.24);
        position: relative;
        overflow: hidden;
    }
    .order-hero::after {
        content: '';
        position: absolute; right: -90px; bottom: -120px;
        width: 280px; height: 280px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.14), transparent 68%);
    }
    .order-check {
        width: 54px; height: 54px; border-radius: 16px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.22);
        font-size: 1.5rem; color: #fff; flex-shrink: 0;
    }
    .order-invoice {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-weight: 800; letter-spacing: .4px; color: #fff;
        background: rgba(255, 255, 255, 0.11);
        border: 1px dashed rgba(255, 255, 255, 0.28);
        border-radius: 10px; padding: 5px 10px; display: inline-flex; align-items: center; gap: 7px;
        font-size: 12.5px;
    }
    .order-card { padding: 20px; }
    .order-card-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 14px; color: var(--ink-900); margin-bottom: 13px; display: flex; align-items: center; gap: 8px; }
    .order-card-title i { color: var(--cyan-600); }
    .order-table { margin: 0; }
    .order-table thead th {
        font-size: 10px; letter-spacing: .5px; text-transform: uppercase; color: var(--cyan-700);
        background: rgba(236, 254, 255, .8); border-bottom: 1px solid rgba(6, 182, 212, .18);
        padding: 9px 10px; white-space: nowrap;
    }
    .order-table tbody td { font-size: 12px; padding: 11px 10px; vertical-align: middle; border-bottom: 1px dashed rgba(148,163,184,.2); }
    .order-summary-row { display: flex; justify-content: space-between; font-size: 12.5px; color: var(--ink-500); padding: 4px 0; }
    .order-summary-total { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148,163,184,.24); margin-top: 8px; padding-top: 12px; }
    .order-summary-total span:first-child { font-size: 13px; font-weight: 700; color: var(--ink-900); }
    .order-summary-total span:last-child { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.3rem; color: var(--cyan-700); }
    .info-line { display: flex; gap: 10px; align-items: flex-start; padding: 8px 0; border-bottom: 1px dashed rgba(148,163,184,.2); }
    .info-line:last-child { border-bottom: none; }
    .info-line i { color: var(--cyan-600); margin-top: 2px; }
    .info-line-label { font-size: 10.5px; color: var(--ink-400); font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
    .info-line-value { font-size: 12.5px; color: var(--ink-900); font-weight: 600; line-height: 1.5; }
    .bank-card { display: flex; justify-content: space-between; align-items: center; gap: 12px; border: 1px solid rgba(148,163,184,.2); border-radius: 12px; padding: 11px 13px; background: rgba(255,255,255,.9); }
    .bank-name { font-size: 11px; font-weight: 800; letter-spacing: .3px; text-transform: uppercase; }
    .bank-number { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-weight: 700; font-size: 13px; color: var(--ink-900); }
    .bank-holder { font-size: 10.5px; color: var(--ink-400); }
    .btn-copy { border: 1px solid rgba(6,182,212,.3); background: rgba(6,182,212,.08); color: var(--cyan-700); border-radius: 9px; font-size: 10.5px; font-weight: 700; padding: 5px 9px; }
    .qr-frame { display: inline-flex; align-items: center; justify-content: center; background: #fff; border: 1px solid rgba(148,163,184,.24); border-radius: 16px; padding: 10px; box-shadow: 0 8px 22px rgba(8,145,178,.09); }
    .timeline { display: grid; gap: 0; }
    .timeline-step { display: flex; gap: 12px; position: relative; padding-bottom: 18px; }
    .timeline-step:last-child { padding-bottom: 0; }
    .timeline-step::before { content: ''; position: absolute; left: 14px; top: 30px; bottom: 0; width: 2px; background: rgba(148,163,184,.25); }
    .timeline-step:last-child::before { display: none; }
    .timeline-dot { width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(148,163,184,.15); color: var(--ink-400); font-size: .85rem; z-index: 1; }
    .timeline-step.done .timeline-dot { background: rgba(16,185,129,.14); color: #047857; }
    .timeline-step.active .timeline-dot { background: linear-gradient(135deg, var(--cyan-500), var(--cyan-700)); color: #fff; box-shadow: 0 6px 14px rgba(6,182,212,.3); }
    .timeline-title { font-size: 12px; font-weight: 800; color: var(--ink-900); line-height: 1.3; }
    .timeline-sub { font-size: 10.5px; color: var(--ink-400); }
</style>
@endpush

@section('content')
@php
    $statusMeta = [
        'pending'   => ['label' => 'Menunggu Konfirmasi', 'class' => 'badge-warning-soft', 'icon' => 'bi-hourglass-split'],
        'paid'      => ['label' => 'Lunas / Diproses',    'class' => 'badge-success-soft', 'icon' => 'bi-check-circle-fill'],
        'cancelled' => ['label' => 'Dibatalkan',          'class' => 'badge-danger-soft',  'icon' => 'bi-x-circle-fill'],
        'failed'    => ['label' => 'Gagal',               'class' => 'badge-danger-soft',  'icon' => 'bi-exclamation-triangle-fill'],
    ][$order->payment_status] ?? ['label' => strtoupper($order->payment_status), 'class' => 'badge-soft', 'icon' => 'bi-info-circle'];

    $paymentMeta = [
        'cod'      => ['label' => 'COD (Bayar di Tempat)', 'icon' => 'bi-cash-coin'],
        'transfer' => ['label' => 'Transfer Bank',         'icon' => 'bi-bank2'],
        'qris'     => ['label' => 'QRIS',                  'icon' => 'bi-qr-code'],
    ][$order->payment_method] ?? ['label' => strtoupper($order->payment_method), 'icon' => 'bi-wallet2'];

    $subtotal = (float) $order->total_amount - (float) $order->delivery_fee;
@endphp

<div class="container">
    @if (session('order_success'))
        <div class="alert alert-portal alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Pesanan berhasil dikirim!</strong> Simpan nomor invoice di bawah untuk melacak status pesanan Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="order-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 position-relative" style="z-index: 1;">
            <div class="d-flex gap-3 align-items-start">
                <span class="order-check"><i class="bi bi-bag-check"></i></span>
                <div>
                    <div class="section-eyebrow" style="color: var(--cyan-200);">Detail Pesanan</div>
                    <h1 class="mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(1.4rem, 2.6vw, 1.95rem);">
                        Terima kasih, {{ $order->recipient_name ?: $order->customer?->name ?: 'Pelanggan' }}!
                    </h1>
                    <p class="mb-3" style="font-size: 13px; color: rgba(255,255,255,.78); max-width: 560px;">
                        Pesanan Anda sudah masuk ke sistem apotek. Petugas akan segera memeriksa dan mengonfirmasi pesanan ini.
                    </p>
                    <span class="order-invoice"><i class="bi bi-receipt"></i>{{ $order->invoice_number }}</span>
                </div>
            </div>
            <div class="text-lg-end">
                <span class="badge {{ $statusMeta['class'] }}" style="font-size: 11px; padding: 7px 12px;">
                    <i class="bi {{ $statusMeta['icon'] }} me-1"></i>{{ $statusMeta['label'] }}
                </span>
                <div class="mt-2" style="font-size: 11px; color: rgba(255,255,255,.62);">
                    Dibuat {{ $order->created_at->format('d/m/Y H:i') }} WIB
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Item pesanan --}}
            <div class="portal-card order-card mb-3">
                <h2 class="order-card-title"><i class="bi bi-box-seam"></i>Rincian Obat</h2>
                <div class="table-responsive">
                    <table class="table order-table align-middle">
                        <thead>
                            <tr>
                                <th>Obat</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->details as $detail)
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: var(--ink-900);">{{ $detail->productBatch?->product?->name ?? 'Obat' }}</div>
                                        <div class="text-muted" style="font-size: 10.5px;">
                                            Per {{ $detail->productBatch?->product?->unit ?? 'pcs' }}
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $detail->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold" style="color: var(--cyan-700);">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 ms-auto" style="max-width: 320px;">
                    <div class="order-summary-row"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                    <div class="order-summary-row"><span>Ongkos kirim</span><span class="text-success fw-semibold">{{ (float) $order->delivery_fee > 0 ? 'Rp ' . number_format($order->delivery_fee, 0, ',', '.') : 'Gratis' }}</span></div>
                    <div class="order-summary-total">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Pengiriman --}}
            <div class="portal-card order-card mb-3">
                <h2 class="order-card-title"><i class="bi bi-truck"></i>Informasi Pengiriman</h2>

                @if ($order->delivery_method === 'delivery')
                    <div class="info-line">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <div class="info-line-label">Alamat Pengiriman</div>
                            <div class="info-line-value">{!! nl2br(e($order->delivery_address ?: '-')) !!}</div>
                        </div>
                    </div>
                @else
                    <div class="info-line">
                        <i class="bi bi-shop"></i>
                        <div>
                            <div class="info-line-label">Metode Pengambilan</div>
                            <div class="info-line-value">Ambil Sendiri di Toko (Pick-up)</div>
                        </div>
                    </div>
                    <div class="info-line">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <div class="info-line-label">Alamat Toko</div>
                            <div class="info-line-value">{{ config('pharmacy.address') }}</div>
                        </div>
                    </div>
                    <div class="info-line">
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <div class="info-line-label">Jam Operasional</div>
                            <div class="info-line-value">
                                @foreach (config('pharmacy.hours') as $hour)
                                    {{ $hour['day'] }}: {{ $hour['time'] }}@if (! $loop->last)<br>@endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="info-line">
                    <i class="bi bi-person-fill"></i>
                    <div>
                        <div class="info-line-label">Penerima</div>
                        <div class="info-line-value">{{ $order->recipient_name ?: $order->customer?->name }} &middot; {{ $order->recipient_phone ?: $order->customer?->phone }}</div>
                    </div>
                </div>

                @if ($order->order_notes)
                    <div class="info-line">
                        <i class="bi bi-chat-left-text-fill"></i>
                        <div>
                            <div class="info-line-label">Catatan Pesanan</div>
                            <div class="info-line-value">{{ $order->order_notes }}</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Timeline status --}}
            <div class="portal-card order-card">
                <h2 class="order-card-title"><i class="bi bi-activity"></i>Status Pesanan</h2>
                <div class="timeline">
                    <div class="timeline-step done">
                        <span class="timeline-dot"><i class="bi bi-check2"></i></span>
                        <div>
                            <div class="timeline-title">Pesanan diterima</div>
                            <div class="timeline-sub">{{ $order->created_at->format('d/m/Y H:i') }} WIB &middot; masuk ke sistem apotek</div>
                        </div>
                    </div>
                    <div class="timeline-step {{ $order->payment_status === 'pending' ? 'active' : 'done' }}">
                        <span class="timeline-dot"><i class="bi bi-hourglass-split"></i></span>
                        <div>
                            <div class="timeline-title">Konfirmasi petugas</div>
                            <div class="timeline-sub">Petugas memeriksa stok, pembayaran, dan kesiapan pesanan.</div>
                        </div>
                    </div>
                    <div class="timeline-step {{ in_array($order->payment_status, ['paid'], true) ? 'done' : '' }}">
                        <span class="timeline-dot"><i class="bi bi-box-seam"></i></span>
                        <div>
                            <div class="timeline-title">{{ $order->delivery_method === 'delivery' ? 'Disiapkan & dikirim' : 'Disiapkan & siap diambil' }}</div>
                            <div class="timeline-sub">{{ $order->delivery_method === 'delivery' ? 'Obat dikirim ke alamat tujuan.' : 'Tunjukkan nomor invoice saat mengambil obat.' }}</div>
                        </div>
                    </div>
                    <div class="timeline-step {{ $order->payment_status === 'paid' ? 'done' : '' }}">
                        <span class="timeline-dot"><i class="bi bi-check-circle"></i></span>
                        <div>
                            <div class="timeline-title">Selesai</div>
                            <div class="timeline-sub">Pesanan diterima dan pembayaran selesai.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Instruksi pembayaran --}}
            <div class="portal-card order-card mb-3">
                <h2 class="order-card-title"><i class="bi {{ $paymentMeta['icon'] }}"></i>{{ $paymentMeta['label'] }}</h2>

                @if ($order->payment_method === 'transfer')
                    <p class="checkout-note mb-3">
                        Silakan transfer sebesar
                        <strong style="color: var(--cyan-700);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                        ke salah satu rekening berikut. Cantumkan nomor invoice pada berita transfer.
                    </p>
                    <div class="d-grid gap-2">
                        @foreach ($banks as $bank)
                            <div class="bank-card">
                                <div>
                                    <div class="bank-name" style="color: {{ $bank['color'] }};">{{ $bank['bank'] }}</div>
                                    <div class="bank-number">{{ $bank['account_number'] }}</div>
                                    <div class="bank-holder">a.n. {{ $bank['account_name'] }}</div>
                                </div>
                                <button type="button" class="btn-copy" data-copy="{{ $bank['account_number'] }}">
                                    <i class="bi bi-copy me-1"></i>Salin
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <div class="alert alert-portal alert-success mt-3 mb-0" style="font-size: 11px;">
                        <i class="bi bi-whatsapp me-1"></i>
                        Kirim bukti transfer ke
                        <a href="https://wa.me/{{ config('pharmacy.whatsapp') }}?text={{ urlencode('Konfirmasi pembayaran ' . $order->invoice_number) }}"
                           target="_blank" rel="noopener" class="fw-bold text-decoration-none">WhatsApp apotek</a>
                        agar pesanan segera diproses.
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                        <a href="tg://resolve?phone={{ config('pharmacy.telegram_phone') }}"
                           class="btn btn-sm btn-portal-outline"
                           data-copy-on-click="Konfirmasi transfer {{ $order->invoice_number }} - {{ config('pharmacy.name') }}">
                            <i class="bi bi-telegram me-1"></i>Kirim Bukti via Telegram
                        </a>
                        <span class="checkout-note">Telegram: <strong>{{ config('pharmacy.telegram_contact') }}</strong></span>
                    </div>
                @elseif ($order->payment_method === 'qris')
                    <p class="checkout-note mb-3">
                        Scan QRIS berikut menggunakan m-banking atau e-wallet. Nominal:
                        <strong style="color: var(--cyan-700);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </p>
                    <div class="text-center">
                        <div class="qr-frame mb-2">
                            <img src="{{ asset(config('pharmacy.qris_path')) }}"
                                 data-fallback="{{ asset('images/qris-apotek.png') }}"
                                 alt="QRIS {{ config('pharmacy.name') }}"
                                 style="width: 190px; max-width: 100%; height: auto; border-radius: 8px;"
                                 onerror="if (this.dataset.fallback && this.getAttribute('src') !== this.dataset.fallback) { this.setAttribute('src', this.dataset.fallback); } else { this.style.display='none'; }">
                        </div>
                        <small class="text-muted" style="font-size: 10.5px;">
                            <i class="bi bi-shield-check me-1" style="color: var(--cyan-600);"></i>
                            QRIS resmi {{ config('pharmacy.name') }}
                        </small>
                    </div>
                    <div class="alert alert-portal alert-success mt-3 mb-0" style="font-size: 11px;">
                        Sudah bayar? Kirim bukti ke
                        <a href="https://wa.me/{{ config('pharmacy.whatsapp') }}?text={{ urlencode('Konfirmasi QRIS ' . $order->invoice_number) }}"
                           target="_blank" rel="noopener" class="fw-bold text-decoration-none">WhatsApp apotek</a>.
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                        <a href="tg://resolve?phone={{ config('pharmacy.telegram_phone') }}"
                           class="btn btn-sm btn-portal-outline"
                           data-copy-on-click="Konfirmasi QRIS {{ $order->invoice_number }} - {{ config('pharmacy.name') }}">
                            <i class="bi bi-telegram me-1"></i>Kirim Bukti via Telegram
                        </a>
                        <span class="checkout-note">Telegram: <strong>{{ config('pharmacy.telegram_contact') }}</strong></span>
                    </div>
                @else
                    <p class="checkout-note mb-3">
                        Bayar tunai sebesar
                        <strong style="color: var(--cyan-700);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                        saat obat diambil di toko atau saat pesanan diantar.
                    </p>
                    <div class="d-flex gap-2 align-items-start p-2 rounded-3" style="background: rgba(6, 182, 212, .06);">
                        <i class="bi bi-info-circle-fill mt-1" style="color: var(--cyan-600);"></i>
                        <div class="checkout-note">
                            Siapkan uang pas bila memungkinkan. Petugas akan memberikan struk pembayaran saat serah terima.
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                        <a href="tg://resolve?phone={{ config('pharmacy.telegram_phone') }}"
                           class="btn btn-sm btn-portal-outline"
                           data-copy-on-click="Konfirmasi pesanan COD {{ $order->invoice_number }} - {{ config('pharmacy.name') }}">
                            <i class="bi bi-telegram me-1"></i>Konfirmasi via Telegram
                        </a>
                        <span class="checkout-note">Telegram: <strong>{{ config('pharmacy.telegram_contact') }}</strong></span>
                    </div>
                @endif
            </div>

            <div class="portal-card order-card mb-3">
                <h2 class="order-card-title"><i class="bi bi-receipt"></i>Ringkasan</h2>
                <div class="order-summary-row"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="order-summary-row"><span>Ongkos kirim</span><span>{{ (float) $order->delivery_fee > 0 ? 'Rp ' . number_format($order->delivery_fee, 0, ',', '.') : 'Gratis' }}</span></div>
                <div class="order-summary-total">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('portal.track') }}" class="btn btn-portal-outline"><i class="bi bi-search me-1"></i>Lacak Pesanan</a>
                    <a href="{{ route('portal.index') }}#katalog" class="btn btn-portal"><i class="bi bi-bag-plus me-1"></i>Belanja Lagi</a>
                    <button type="button" onclick="window.print()" class="btn btn-link btn-sm text-muted text-decoration-none"><i class="bi bi-printer me-1"></i>Cetak Detail</button>
                </div>
            </div>

            <div class="portal-card p-3">
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-headset" style="color: var(--cyan-600);"></i>
                    <div class="checkout-note">
                        Ada pertanyaan? Hubungi
                        <a href="https://wa.me/{{ config('pharmacy.whatsapp') }}" target="_blank" rel="noopener"
                           class="fw-bold text-decoration-none" style="color: var(--cyan-700);">{{ config('pharmacy.phone') }}</a>
                        dengan menyebutkan invoice <strong>{{ $order->invoice_number }}</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', function (event) {
        const copyButton = event.target.closest('[data-copy]');
        if (copyButton) {
            const value = copyButton.dataset.copy;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(value).then(() => {
                    window.PortalCart.showToast('Nomor rekening berhasil disalin.');
                }).catch(() => {
                    window.PortalCart.showToast('Gagal menyalin. Salin manual: ' + value, 'warning');
                });
            } else {
                window.PortalCart.showToast('Nomor rekening: ' + value);
            }
            return;
        }

        const telegramButton = event.target.closest('[data-copy-on-click]');
        if (telegramButton) {
            const message = telegramButton.dataset.copyOnClick;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(message).catch(() => {});
            }
            window.PortalCart.showToast('Pesan konfirmasi disalin. Tempel di chat Telegram.');
        }
    });
</script>
@endpush

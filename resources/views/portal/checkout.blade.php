@extends('layouts.portal')

@section('title', 'Checkout Pesanan')

@push('styles')
<style>
    .checkout-head { margin-bottom: 20px; }
    .step-card { padding: 20px; margin-bottom: 16px; }
    .step-head { display: flex; align-items: center; gap: 11px; margin-bottom: 16px; }
    .step-num {
        width: 32px; height: 32px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, var(--cyan-500), var(--cyan-700));
        color: #fff; font-weight: 800; font-size: 12.5px; flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(6, 182, 212, .24);
    }
    .step-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 14px; color: var(--ink-900); margin: 0; }
    .step-sub { font-size: 11px; color: var(--ink-400); margin: 1px 0 0; }

    .choice-card {
        display: flex; gap: 12px; align-items: flex-start;
        width: 100%; padding: 14px;
        border: 1.5px solid rgba(148, 163, 184, 0.28);
        border-radius: 14px; background: rgba(255, 255, 255, 0.78);
        cursor: pointer; transition: all .18s ease; height: 100%;
    }
    .choice-card:hover { border-color: rgba(6, 182, 212, 0.45); background: #fff; }
    .btn-check:checked + .choice-card {
        border-color: var(--cyan-500);
        background: linear-gradient(135deg, rgba(236, 254, 255, 0.92), rgba(240, 253, 250, 0.92));
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.10);
    }
    .choice-icon {
        width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(6, 182, 212, 0.10); color: var(--cyan-700); font-size: 1.05rem;
    }
    .choice-title { font-size: 12.5px; font-weight: 800; color: var(--ink-900); line-height: 1.3; }
    .choice-text { font-size: 10.5px; color: var(--ink-500); line-height: 1.45; margin-top: 2px; }

    .address-box { background: rgba(236, 254, 255, 0.52); border: 1px dashed rgba(6, 182, 212, 0.28); border-radius: 14px; padding: 15px; }
    .payment-panel { border-radius: 14px; padding: 15px; border: 1px dashed rgba(148, 163, 184, 0.3); background: rgba(255, 255, 255, 0.62); }
    .bank-card {
        display: flex; justify-content: space-between; align-items: center; gap: 12px;
        border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 12px;
        padding: 11px 13px; background: rgba(255, 255, 255, 0.9);
    }
    .bank-name { font-size: 11px; font-weight: 800; letter-spacing: .3px; text-transform: uppercase; }
    .bank-number { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-weight: 700; font-size: 13px; color: var(--ink-900); }
    .bank-holder { font-size: 10.5px; color: var(--ink-400); }
    .btn-copy { border: 1px solid rgba(6, 182, 212, .3); background: rgba(6, 182, 212, .08); color: var(--cyan-700); border-radius: 9px; font-size: 10.5px; font-weight: 700; padding: 5px 9px; }
    .btn-copy:hover { background: rgba(6, 182, 212, .16); color: var(--cyan-700); }
    .qr-frame { display: inline-flex; align-items: center; justify-content: center; background: #fff; border: 1px solid rgba(148, 163, 184, .24); border-radius: 16px; padding: 12px; box-shadow: 0 8px 22px rgba(8, 145, 178, .09); }

    .summary-card { padding: 19px; position: sticky; top: 86px; }
    .summary-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 14.5px; color: var(--ink-900); }
    .summary-item { display: flex; justify-content: space-between; gap: 10px; padding: 9px 0; border-bottom: 1px dashed rgba(148, 163, 184, .22); }
    .summary-item:last-child { border-bottom: none; }
    .summary-item-name { font-size: 12px; font-weight: 700; color: var(--ink-900); line-height: 1.3; }
    .summary-item-meta { font-size: 10.5px; color: var(--ink-400); }
    .summary-item-price { font-size: 12px; font-weight: 800; color: var(--cyan-700); white-space: nowrap; }
    .summary-row { display: flex; justify-content: space-between; font-size: 12.5px; color: var(--ink-500); padding: 4px 0; }
    .summary-total { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148, 163, 184, .24); margin-top: 8px; padding-top: 12px; }
    .summary-total-label { font-size: 13px; font-weight: 700; color: var(--ink-900); }
    .summary-total-value { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.35rem; color: var(--cyan-700); }
    .summary-empty { text-align: center; padding: 30px 10px; color: var(--ink-400); }
    .summary-empty i { font-size: 2.4rem; opacity: .5; }
    .checkout-note { font-size: 10.5px; color: var(--ink-400); line-height: 1.55; }
    .mini-qty { display: inline-flex; align-items: center; gap: 2px; border: 1px solid rgba(148,163,184,.28); border-radius: 8px; overflow: hidden; }
    .mini-qty button { border: none; background: rgba(6,182,212,.08); color: var(--cyan-700); width: 24px; height: 24px; font-size: 11px; font-weight: 800; }
    .mini-qty span { min-width: 24px; text-align: center; font-size: 11px; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="checkout-head">
        <span class="section-eyebrow"><i class="bi bi-bag-check"></i>Checkout</span>
        <h1 class="section-title mb-1">Selesaikan Pesanan Anda</h1>
        <p class="section-sub">Isi data penerima, pilih pengiriman dan pembayaran, lalu kirim pesanan ke apotek.</p>
    </div>

    <form method="POST" action="{{ route('portal.checkout.store') }}" id="checkoutForm">
        @csrf
        <input type="hidden" name="items" id="itemsInput" value="{{ old('items') }}">

        <div class="row g-4">
            <div class="col-lg-7">
                {{-- 1. Data pemesan --}}
                <div class="portal-card step-card">
                    <div class="step-head">
                        <span class="step-num">1</span>
                        <div>
                            <h2 class="step-title">Data Pemesan</h2>
                            <p class="step-sub">Kami gunakan untuk konfirmasi pesanan.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer_name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                   id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                   placeholder="Contoh: Retno Mey Puspita" required>
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer_phone">No. HP / WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                                   id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                   placeholder="0812-3456-7890" required>
                            @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Pengiriman --}}
                <div class="portal-card step-card">
                    <div class="step-head">
                        <span class="step-num">2</span>
                        <div>
                            <h2 class="step-title">Metode Pengiriman</h2>
                            <p class="step-sub">Pilih cara Anda menerima obat.</p>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="delivery_method" id="delivery_pickup" value="pickup"
                                   {{ old('delivery_method', 'pickup') === 'pickup' ? 'checked' : '' }}>
                            <label class="choice-card" for="delivery_pickup">
                                <span class="choice-icon"><i class="bi bi-shop"></i></span>
                                <span>
                                    <span class="choice-title d-block">Ambil Sendiri di Toko</span>
                                    <span class="choice-text d-block">Pesan dulu, ambil saat sudah siap di apotek.</span>
                                    <span class="badge-soft badge-success-soft mt-2 d-inline-flex"><i class="bi bi-check2"></i>Gratis</span>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="delivery_method" id="delivery_delivery" value="delivery"
                                   {{ old('delivery_method') === 'delivery' ? 'checked' : '' }}>
                            <label class="choice-card" for="delivery_delivery">
                                <span class="choice-icon"><i class="bi bi-truck"></i></span>
                                <span>
                                    <span class="choice-title d-block">Kirim ke Alamat</span>
                                    <span class="choice-text d-block">Dikirim ke alamat lengkap Anda di area Punggur &amp; sekitar.</span>
                                    <span class="badge-soft mt-2 d-inline-flex"><i class="bi bi-geo-alt"></i>Isi alamat</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div id="deliveryAddressFields" class="address-box mt-3 {{ old('delivery_method') === 'delivery' ? '' : 'd-none' }}">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="address_street">Alamat Jalan / Nomor Rumah <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address_street') is-invalid @enderror" id="address_street"
                                          name="address_street" rows="2" data-address-required
                                          placeholder="Contoh: Jl. Raya Punggur No. 45, RT 02/RW 03">{{ old('address_street') }}</textarea>
                                @error('address_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="address_village">Desa / Kelurahan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address_village') is-invalid @enderror"
                                       id="address_village" name="address_village" value="{{ old('address_village') }}"
                                       data-address-required placeholder="Contoh: Tanggul Angin">
                                @error('address_village')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="address_district">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address_district') is-invalid @enderror"
                                       id="address_district" name="address_district" value="{{ old('address_district') }}"
                                       data-address-required placeholder="Contoh: Punggur">
                                @error('address_district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="address_city">Kabupaten / Kota <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address_city') is-invalid @enderror"
                                       id="address_city" name="address_city" value="{{ old('address_city') }}"
                                       data-address-required placeholder="Contoh: Lampung Tengah">
                                @error('address_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="address_postal">Kode Pos</label>
                                <input type="text" class="form-control @error('address_postal') is-invalid @enderror"
                                       id="address_postal" name="address_postal" value="{{ old('address_postal') }}"
                                       placeholder="34161">
                                @error('address_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="address_notes">Patokan / Catatan Alamat</label>
                                <input type="text" class="form-control" id="address_notes" name="address_notes"
                                       value="{{ old('address_notes') }}" placeholder="Contoh: Depan masjid, rumah pagar hijau">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Pembayaran --}}
                <div class="portal-card step-card">
                    <div class="step-head">
                        <span class="step-num">3</span>
                        <div>
                            <h2 class="step-title">Metode Pembayaran</h2>
                            <p class="step-sub">Pilih cara pembayaran yang paling nyaman.</p>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_cod" value="cod"
                                   {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                            <label class="choice-card" for="payment_cod">
                                <span class="choice-icon"><i class="bi bi-cash-coin"></i></span>
                                <span>
                                    <span class="choice-title d-block">COD</span>
                                    <span class="choice-text d-block">Bayar di tempat saat obat diterima/diambil.</span>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_transfer" value="transfer"
                                   {{ old('payment_method') === 'transfer' ? 'checked' : '' }}>
                            <label class="choice-card" for="payment_transfer">
                                <span class="choice-icon"><i class="bi bi-bank2"></i></span>
                                <span>
                                    <span class="choice-title d-block">Transfer Bank</span>
                                    <span class="choice-text d-block">Transfer ke rekening resmi apotek.</span>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_qris" value="qris"
                                   {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                            <label class="choice-card" for="payment_qris">
                                <span class="choice-icon"><i class="bi bi-qr-code"></i></span>
                                <span>
                                    <span class="choice-title d-block">QRIS</span>
                                    <span class="choice-text d-block">Scan dari m-banking atau e-wallet.</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Panel COD --}}
                    <div class="payment-panel mt-3 {{ old('payment_method', 'cod') === 'cod' ? '' : 'd-none' }}" data-payment-panel="cod">
                        <div class="d-flex gap-2 align-items-start">
                            <i class="bi bi-info-circle-fill" style="color: var(--cyan-600);"></i>
                            <div>
                                <div class="fw-bold" style="font-size: 12px; color: var(--ink-900);">Bayar di Tempat (COD)</div>
                                <div class="checkout-note mt-1">
                                    Siapkan uang tunai sebesar <strong id="payAmountCod">Rp 0</strong>.
                                    Pembayaran dilakukan saat obat diambil di toko atau saat kurir mengantar pesanan.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Panel Transfer --}}
                    <div class="payment-panel mt-3 {{ old('payment_method') === 'transfer' ? '' : 'd-none' }}" data-payment-panel="transfer">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold" style="font-size: 12px; color: var(--ink-900);">Transfer ke Rekening Toko</div>
                                <div class="checkout-note">Total yang harus ditransfer: <strong id="payAmountTransfer">Rp 0</strong></div>
                            </div>
                            <span class="badge-soft"><i class="bi bi-shield-check"></i>Rekening resmi</span>
                        </div>
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
                        <div class="checkout-note mt-2">
                            <i class="bi bi-whatsapp me-1" style="color: #16a34a;"></i>
                            Setelah transfer, kirim bukti pembayaran ke WhatsApp apotek agar pesanan segera diproses.
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <a href="tg://resolve?phone={{ config('pharmacy.telegram_phone') }}"
                               class="btn btn-sm btn-portal-outline" target="_blank" rel="noopener"
                               data-copy-on-click="Konfirmasi pembayaran pesanan {{ config('pharmacy.name') }}">
                                <i class="bi bi-telegram me-1"></i>Kirim Bukti via Telegram
                            </a>
                            <span class="checkout-note">Kontak Telegram: <strong>{{ config('pharmacy.telegram_contact') }}</strong></span>
                        </div>
                    </div>

                    {{-- Panel QRIS --}}
                    <div class="payment-panel mt-3 {{ old('payment_method') === 'qris' ? '' : 'd-none' }}" data-payment-panel="qris">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <div class="qr-frame">
                                    <img src="{{ asset(config('pharmacy.qris_path')) }}"
                                         data-fallback="{{ asset('images/qris-apotek.png') }}"
                                         alt="QRIS {{ config('pharmacy.name') }}"
                                         style="width: 150px; height: auto; border-radius: 8px;"
                                         onerror="if (this.dataset.fallback && this.getAttribute('src') !== this.dataset.fallback) { this.setAttribute('src', this.dataset.fallback); } else { this.style.display='none'; }">
                                </div>
                            </div>
                            <div class="col">
                                <div class="fw-bold" style="font-size: 12px; color: var(--ink-900);">Scan QRIS</div>
                                <div class="checkout-note mt-1">
                                    Nominal pembayaran: <strong id="payAmountQris" style="color: var(--cyan-700);">Rp 0</strong>.
                                    Scan menggunakan GoPay, OVO, Dana, ShopeePay, atau m-banking yang mendukung QRIS.
                                </div>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    @foreach (['GoPay', 'OVO', 'Dana', 'ShopeePay', 'BCA Mobile', 'BRImo'] as $wallet)
                                        <span class="soft-chip" style="font-size: 10px; padding: 4px 8px;">{{ $wallet }}</span>
                                    @endforeach
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                    <a href="tg://resolve?phone={{ config('pharmacy.telegram_phone') }}"
                                       class="btn btn-sm btn-portal-outline" target="_blank" rel="noopener"
                                       data-copy-on-click="Konfirmasi QRIS pesanan {{ config('pharmacy.name') }}">
                                        <i class="bi bi-telegram me-1"></i>Kirim Bukti via Telegram
                                    </a>
                                    <span class="checkout-note">Telegram: <strong>{{ config('pharmacy.telegram_contact') }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Catatan --}}
                <div class="portal-card step-card">
                    <div class="step-head">
                        <span class="step-num">4</span>
                        <div>
                            <h2 class="step-title">Catatan Pesanan <span class="text-muted fw-normal">(opsional)</span></h2>
                            <p class="step-sub">Contoh: minta obat diganti kemasan, waktu pengiriman, dll.</p>
                        </div>
                    </div>
                    <textarea class="form-control" id="order_notes" name="order_notes" rows="3"
                              placeholder="Tulis catatan untuk petugas apotek...">{{ old('order_notes') }}</textarea>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="portal-card summary-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="summary-title mb-0"><i class="bi bi-bag-check me-2 text-info"></i>Ringkasan Pesanan</h2>
                        <a href="{{ route('portal.index') }}#katalog" class="small text-decoration-none" style="color: var(--cyan-700); font-weight: 700;">+ Tambah</a>
                    </div>

                    <div id="checkoutItems">
                        <div class="summary-empty">
                            <i class="bi bi-bag-x d-block mb-2"></i>
                            <div class="fw-semibold">Keranjang masih kosong</div>
                            <small>Pilih obat dari katalog terlebih dahulu.</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="summary-row"><span>Subtotal</span><span id="checkoutSubtotal">Rp 0</span></div>
                        <div class="summary-row"><span>Ongkos kirim</span><span class="text-success fw-semibold">Gratis</span></div>
                        <div class="summary-total">
                            <span class="summary-total-label">Total Bayar</span>
                            <span class="summary-total-value" id="checkoutTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-start mt-3 mb-3 p-2 rounded-3" style="background: rgba(6, 182, 212, .06);">
                        <i class="bi bi-shield-check mt-1" style="color: var(--cyan-600);"></i>
                        <div class="checkout-note">
                            Pesanan akan diteruskan ke petugas apotek. Status awal <strong>menunggu konfirmasi</strong>
                            dan dapat dilacak dengan nomor invoice.
                        </div>
                    </div>

                    {{-- Tombol kontak cepat dengan ringkasan otomatis --}}
                    <div class="checkout-note mt-2 mb-2">
                        Atau kirim ringkasan pesanan ini langsung ke apotek:
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a id="checkoutWhatsappLink" href="https://wa.me/{{ config('pharmacy.whatsapp') }}"
                           target="_blank" rel="noopener" class="btn btn-sm btn-portal flex-fill">
                            <i class="bi bi-whatsapp me-1"></i>WhatsApp
                        </a>
                        <a id="checkoutTelegramLink" href="tg://resolve?phone={{ config('pharmacy.telegram_phone') }}"
                           target="_blank" rel="noopener" class="btn btn-sm btn-portal-outline flex-fill">
                            <i class="bi bi-telegram me-1"></i>Telegram
                        </a>
                    </div>

                    <button type="submit" class="btn btn-portal w-100" id="submitOrderBtn" disabled>
                        <i class="bi bi-send-check me-1"></i>Kirim Pesanan
                    </button>
                    <a href="{{ route('portal.index') }}#katalog" class="btn btn-link btn-sm w-100 mt-1 text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Lanjut belanja
                    </a>
                </div>

                <div class="portal-card p-3 mt-3">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-headset" style="color: var(--cyan-600);"></i>
                        <div class="checkout-note">
                            Butuh bantuan? Hubungi <a href="https://wa.me/{{ config('pharmacy.whatsapp') }}" target="_blank" rel="noopener" class="fw-bold text-decoration-none" style="color: var(--cyan-700);">{{ config('pharmacy.phone') }}</a>
                            atau datang langsung ke {{ config('pharmacy.name') }}.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const products = @json($productsPayload);
        const productMap = new Map(products.map((product) => [Number(product.id), product]));
        const form = document.getElementById('checkoutForm');
        const itemsInput = document.getElementById('itemsInput');
        const itemsWrapper = document.getElementById('checkoutItems');
        const subtotalElement = document.getElementById('checkoutSubtotal');
        const totalElement = document.getElementById('checkoutTotal');
        const submitButton = document.getElementById('submitOrderBtn');
        const addressFields = document.getElementById('deliveryAddressFields');
        const whatsappLink = document.getElementById('checkoutWhatsappLink');
        const telegramLink = document.getElementById('checkoutTelegramLink');
        const storeName = @json(config('pharmacy.name'));
        const whatsappNumber = @json(config('pharmacy.whatsapp'));
        const telegramPhone = @json(config('pharmacy.telegram_phone'));
        const formatRupiah = (value) => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        const escapeHtml = (text) => String(text ?? '').replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });

        function currentCart() {
            return window.PortalCart.getCart()
                .map((item) => {
                    const product = productMap.get(Number(item.id));
                    if (!product) return null;

                    return {
                        ...item,
                        name: product.name,
                        price: Number(product.price || 0),
                        unit: product.unit,
                        stock: Number(product.stock || 0),
                        category: product.category,
                        quantity: Math.max(1, Math.min(Number(item.quantity || 1), Math.max(Number(product.stock || 0), 1))),
                    };
                })
                .filter(Boolean);
        }

        function fieldValue(id) {
            return (document.getElementById(id)?.value || '').trim();
        }

        /**
         * Ringkasan yang sama dengan format notifikasi bot, dipakai sebagai
         * teks awal tautan WhatsApp / Telegram sehingga nomor invoice tidak
         * diperlukan saat checkout (belum dibuat).
         */
        function buildContactMessage(cart, subtotal) {
            const payment = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
            const delivery = document.querySelector('input[name="delivery_method"]:checked')?.value || 'pickup';
            const paymentLabels = { cod: 'COD (Bayar di Tempat)', transfer: 'Transfer Bank', qris: 'QRIS' };
            const deliveryLabels = { pickup: 'Ambil sendiri di toko', delivery: 'Kirim ke alamat' };

            const lines = [
                '🧾 RINGKASAN PESANAN - ' + storeName,
                '',
                'Pembeli    : ' + (fieldValue('customer_name') || '-') + ' (' + (fieldValue('customer_phone') || '-') + ')',
                'Pembayaran : ' + (paymentLabels[payment] || payment.toUpperCase()),
                'Pengiriman : ' + (deliveryLabels[delivery] || delivery.toUpperCase()),
                '',
                'RINCIAN PESANAN:',
            ];

            if (cart.length === 0) {
                lines.push('• (keranjang masih kosong)');
            }
            cart.forEach((item) => {
                lines.push(
                    '• ' + item.name + ' x' + item.quantity + ' ' + (item.unit || 'pcs') +
                    ' @ ' + formatRupiah(item.price) + ' = ' + formatRupiah(Number(item.price) * Number(item.quantity))
                );
            });

            lines.push('');
            lines.push('Subtotal : ' + formatRupiah(subtotal));
            lines.push('Ongkir   : Gratis');
            lines.push('TOTAL    : ' + formatRupiah(subtotal));

            if (delivery === 'delivery') {
                const address = ['address_street', 'address_village', 'address_district', 'address_city', 'address_postal', 'address_notes']
                    .map(fieldValue)
                    .filter(Boolean)
                    .join(', ');
                if (address) lines.push('', 'Alamat   : ' + address);
            }

            const notes = fieldValue('order_notes');
            if (notes) lines.push('Catatan  : ' + notes);

            lines.push('');
            lines.push('Mohon diproses. Terima kasih.');
            return lines.join('\n');
        }

        function refreshContactLinks() {
            const cart = currentCart();
            const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);
            const message = buildContactMessage(cart, subtotal);
            const encoded = encodeURIComponent(message);

            if (whatsappLink) {
                whatsappLink.href = 'https://wa.me/' + whatsappNumber + '?text=' + encoded;
            }
            if (telegramLink) {
                telegramLink.href = 'tg://resolve?phone=' + telegramPhone + '&text=' + encoded;
                telegramLink.dataset.copyOnClick = message;
            }
        }

        function renderSummary() {
            const cart = currentCart();
            const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);

            if (cart.length === 0) {
                itemsWrapper.innerHTML = `
                    <div class="summary-empty">
                        <i class="bi bi-bag-x d-block mb-2"></i>
                        <div class="fw-semibold">Keranjang masih kosong</div>
                        <small>Pilih obat dari katalog terlebih dahulu.</small>
                    </div>`;
            } else {
                itemsWrapper.innerHTML = cart.map((item) => `
                    <div class="summary-item">
                        <div class="min-w-0">
                            <div class="summary-item-name">${escapeHtml(item.name)}</div>
                            <div class="summary-item-meta">${formatRupiah(item.price)} / ${escapeHtml(item.unit)}</div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <div class="mini-qty">
                                    <button type="button" data-checkout-dec="${item.id}" aria-label="Kurangi">-</button>
                                    <span>${item.quantity}</span>
                                    <button type="button" data-checkout-inc="${item.id}" aria-label="Tambah">+</button>
                                </div>
                                <button type="button" class="cart-remove" data-checkout-remove="${item.id}" aria-label="Hapus">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="summary-item-price">${formatRupiah(Number(item.price) * Number(item.quantity))}</div>
                    </div>`).join('');
            }

            subtotalElement.textContent = formatRupiah(subtotal);
            totalElement.textContent = formatRupiah(subtotal);

            ['payAmountCod', 'payAmountTransfer', 'payAmountQris'].forEach((id) => {
                const element = document.getElementById(id);
                if (element) element.textContent = formatRupiah(subtotal);
            });

            itemsInput.value = JSON.stringify(cart.map((item) => ({
                product_id: Number(item.id),
                quantity: Number(item.quantity),
            })));

            submitButton.disabled = cart.length === 0;
            refreshContactLinks();
        }

        function updateDeliveryFields() {
            const selected = document.querySelector('input[name="delivery_method"]:checked')?.value || 'pickup';
            const isDelivery = selected === 'delivery';

            addressFields.classList.toggle('d-none', !isDelivery);
            addressFields.querySelectorAll('[data-address-required]').forEach((field) => {
                field.required = isDelivery;
            });
            refreshContactLinks();
        }

        function updatePaymentPanels() {
            const selected = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
            document.querySelectorAll('[data-payment-panel]').forEach((panel) => {
                panel.classList.toggle('d-none', panel.dataset.paymentPanel !== selected);
            });
            refreshContactLinks();
        }

        // Ikuti perubahan data pemesan/alamat/catatan agar pesan selalu akurat.
        form.addEventListener('input', (event) => {
            if (event.target.matches('input, textarea')) refreshContactLinks();
        });

        document.querySelectorAll('input[name="delivery_method"]').forEach((radio) => {
            radio.addEventListener('change', updateDeliveryFields);
        });

        document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
            radio.addEventListener('change', updatePaymentPanels);
        });

        document.addEventListener('click', function (event) {
            const increase = event.target.closest('[data-checkout-inc]');
            if (increase) {
                const id = increase.dataset.checkoutInc;
                const item = currentCart().find((row) => Number(row.id) === Number(id));
                if (item) {
                    if (Number(item.quantity) >= Number(item.stock || 0)) {
                        window.PortalCart.showToast('Jumlah sudah mencapai batas stok tersedia.', 'warning');
                        return;
                    }
                    window.PortalCart.updateQty(id, Number(item.quantity) + 1);
                }
                return;
            }

            const decrease = event.target.closest('[data-checkout-dec]');
            if (decrease) {
                const id = decrease.dataset.checkoutDec;
                const item = currentCart().find((row) => Number(row.id) === Number(id));
                if (item) window.PortalCart.updateQty(id, Number(item.quantity) - 1);
                return;
            }

            const remove = event.target.closest('[data-checkout-remove]');
            if (remove) {
                window.PortalCart.remove(remove.dataset.checkoutRemove);
                window.PortalCart.showToast('Item dihapus dari keranjang.', 'warning');
                return;
            }

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

        document.addEventListener('portal-cart:updated', renderSummary);

        form.addEventListener('submit', function (event) {
            const cart = currentCart();
            if (cart.length === 0) {
                event.preventDefault();
                window.PortalCart.showToast('Keranjang masih kosong. Pilih obat terlebih dahulu.', 'warning');
                return;
            }

            itemsInput.value = JSON.stringify(cart.map((item) => ({
                product_id: Number(item.id),
                quantity: Number(item.quantity),
            })));

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses pesanan...';
        });

        renderSummary();
        updateDeliveryFields();
        updatePaymentPanels();
    });
</script>
@endpush

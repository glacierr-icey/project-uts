@extends('layouts.app')

@section('title', 'Kasir / Point of Sale (POS)')

@push('styles')
<style>
    /* ==========================================================
       HALAMAN KASIR (POS) — GAYA SPESIFIK
       ========================================================== */

    /* ---------- Kartu Katalog & Keranjang ---------- */
    .catalog-card, .cart-card { padding: 18px; }
    .cart-sticky { position: sticky; top: 88px; }
    @media (max-width: 1199.98px) { .cart-sticky { position: static; } }

    .panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .panel-head .panel-title {
        display: flex; align-items: center; gap: 11px;
        font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 15.5px; color: var(--ink-900); margin: 0;
    }
    .panel-icon {
        flex-shrink: 0; width: 38px; height: 38px; border-radius: 12px;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.14), rgba(8, 145, 178, 0.18));
        color: var(--cyan-700);
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem;
        border: 1px solid rgba(6, 182, 212, 0.22);
    }
    .panel-head small.panel-sub { display: block; color: var(--ink-500); font-size: 11.5px; margin-top: 3px; }

    /* ---------- Search box ---------- */
    .search-wrap { position: relative; margin-bottom: 12px; }
    .search-wrap > .bi {
        position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
        color: var(--ink-400); font-size: 15px; pointer-events: none;
    }
    .search-wrap .form-control {
        padding: 11px 150px 11px 42px;
        border-radius: 13px;
        border: 1.5px solid rgba(148, 163, 184, 0.26);
        background: rgba(255, 255, 255, 0.82);
        font-size: 14px;
    }
    .search-wrap .form-control::placeholder { color: var(--ink-400); }
    .search-hint {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        font-size: 10px; color: var(--ink-400); background: rgba(241, 245, 249, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.22); border-radius: 7px; padding: 3px 8px;
        white-space: nowrap; pointer-events: none;
    }
    @media (max-width: 575.98px) { .search-hint { display: none; } .search-wrap .form-control { padding-right: 42px; } }

    /* ---------- Tabel katalog ---------- */
    .catalog-scroll { max-height: calc(100vh - 360px); min-height: 300px; overflow: auto; border-radius: 14px; border: 1px solid rgba(148, 163, 184, 0.16); }
    @media (max-width: 1199.98px) { .catalog-scroll { max-height: 480px; } }

    .medicine-row { cursor: pointer; }
    .med-name { font-weight: 600; color: var(--ink-900); font-size: 13.5px; line-height: 1.3; }
    .med-unit { color: var(--ink-400); font-size: 11px; }

    .batch-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        color: #92400e; border: 1px solid #fde68a;
        border-radius: 8px; padding: 3px 9px; font-size: 11px; font-weight: 700;
    }
    .batch-no-mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 10.5px; color: var(--ink-500); letter-spacing: 0.2px;
    }

    .med-price { font-weight: 700; color: var(--ink-900); white-space: nowrap; }

    .btn-add-cart {
        border-radius: 999px;
        padding: 6px 16px;
        font-size: 12.5px;
        display: inline-flex; align-items: center; gap: 6px;
    }

    /* ---------- Keranjang ---------- */
    .cart-scroll {
        max-height: 250px; min-height: 150px; overflow: auto;
        border-radius: 14px; border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(255, 255, 255, 0.45);
    }
    .cart-scroll table td { padding: 9px 10px; }
    .cart-item-name { font-weight: 600; color: var(--ink-900); font-size: 12.5px; line-height: 1.3; }
    .cart-item-price { color: var(--ink-400); font-size: 10.5px; }
    .cart-item-subtotal { font-weight: 700; color: var(--ink-900); font-size: 12.5px; white-space: nowrap; }

    .qty-stepper {
        display: inline-flex; align-items: center;
        background: rgba(248, 250, 252, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 999px; padding: 3px;
    }
    .stepper-btn {
        width: 25px; height: 25px; border-radius: 50%; border: none;
        background: #fff; color: var(--cyan-700);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
        transition: all 0.15s ease; line-height: 1;
    }
    .stepper-btn:hover { background: var(--cyan-500); color: #fff; }
    .qty-value { min-width: 34px; text-align: center; font-weight: 700; font-size: 13px; color: var(--ink-900); font-variant-numeric: tabular-nums; }

    .btn-remove {
        border: none; background: rgba(239, 68, 68, 0.08); color: #dc2626;
        width: 29px; height: 29px; border-radius: 10px; transition: all 0.15s ease;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-remove:hover { background: #dc2626; color: #fff; }

    /* ---------- Blok Total Tagihan ---------- */
    .total-block {
        background: linear-gradient(135deg, var(--cyan-500) 0%, var(--cyan-600) 55%, var(--blue-700) 100%);
        border-radius: 16px;
        padding: 14px 18px;
        color: #fff;
        box-shadow: 0 12px 28px rgba(6, 182, 212, 0.35), inset 0 1px 1px rgba(255, 255, 255, 0.35);
        margin-bottom: 14px;
    }
    .total-row-sub { font-size: 12px; color: rgba(255, 255, 255, 0.82); font-weight: 500; }
    .total-row-grand { border-top: 1px dashed rgba(255, 255, 255, 0.35); }
    .total-label { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgba(255, 255, 255, 0.85); }
    .total-amount {
        font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.45rem; line-height: 1.1;
        color: #fff; font-variant-numeric: tabular-nums;
    }

    /* ---------- Metode pembayaran (kartu pilihan) ---------- */
    .pay-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    @media (max-width: 400px) { .pay-grid { grid-template-columns: repeat(2, 1fr); } }
    .pay-option {
        position: relative;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;
        padding: 12px 6px; border-radius: 14px; cursor: pointer;
        background: rgba(255, 255, 255, 0.75);
        border: 1.5px solid rgba(148, 163, 184, 0.28);
        color: var(--ink-500); font-size: 11.5px; font-weight: 600;
        transition: all 0.2s ease;
    }
    .pay-option i { font-size: 1.25rem; }
    .pay-option .pay-note { font-size: 8.5px; font-weight: 700; letter-spacing: 0.2px; opacity: 0.72; margin-top: -3px; }
    .pay-option:hover { border-color: rgba(6, 182, 212, 0.55); color: var(--cyan-700); transform: translateY(-1px); }
    input:checked + .pay-option {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.12), rgba(8, 145, 178, 0.17));
        border-color: var(--cyan-500); color: var(--cyan-700);
        box-shadow: 0 6px 16px rgba(6, 182, 212, 0.18);
    }
    input:checked + .pay-option::after {
        content: "\2713";
        position: absolute; top: -8px; right: -8px;
        width: 20px; height: 20px; border-radius: 50%;
        background: var(--cyan-500); color: #fff; font-size: 11px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 6px rgba(6, 182, 212, 0.45);
    }

    /* ---------- Input uang tunai ---------- */
    .cash-input-wrap {
        display: flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.82);
        border: 1.5px solid rgba(148, 163, 184, 0.26);
        border-radius: 13px; padding: 6px 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .cash-input-wrap:focus-within { border-color: var(--cyan-500); box-shadow: 0 0 0 3.5px rgba(6, 182, 212, 0.15); }
    .cash-prefix { font-weight: 700; color: var(--ink-400); font-size: 15px; }
    .cash-input-wrap input {
        border: none; outline: none; background: transparent; flex: 1;
        font-weight: 700; font-size: 1.25rem; color: var(--ink-900);
        font-variant-numeric: tabular-nums; padding: 4px 0;
    }
    .cash-input-wrap input::-webkit-outer-spin-button,
    .cash-input-wrap input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .quick-cash-row { display: flex; gap: 6px; margin-top: 8px; }
    .quick-chip {
        flex: 1; border-radius: 999px; padding: 6px 4px; font-size: 11.5px; font-weight: 600;
        background: rgba(255, 255, 255, 0.75); color: var(--ink-500);
        border: 1px solid rgba(148, 163, 184, 0.3); transition: all 0.15s ease;
    }
    .quick-chip:hover { color: var(--cyan-700); border-color: rgba(6, 182, 212, 0.5); background: #fff; }

    /* ---------- Indikator kembalian / kekurangan ---------- */
    .pay-status {
        display: flex; align-items: center; justify-content: center; gap: 7px;
        border-radius: 12px; padding: 10px 12px; margin-top: 12px;
        font-size: 12.5px; font-weight: 700; text-align: center;
    }
    .pay-status-ok    { background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); }
    .pay-status-short { background: rgba(239, 68, 68, 0.10); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.3); }

    /* ---------- Tombol checkout besar ---------- */
    .btn-checkout {
        background: linear-gradient(135deg, var(--cyan-400) 0%, var(--cyan-500) 45%, var(--cyan-600) 100%);
        color: #fff; border: none; border-radius: 14px;
        padding: 14px; font-weight: 700; font-size: 14.5px; letter-spacing: 0.2px;
        box-shadow: 0 10px 26px rgba(6, 182, 212, 0.38), inset 0 1px 1px rgba(255, 255, 255, 0.35);
        transition: all 0.2s ease;
    }
    .btn-checkout:hover:not(:disabled) { color: #fff; transform: translateY(-1px); filter: brightness(1.04); }
    .btn-checkout:disabled {
        background: rgba(148, 163, 184, 0.35); color: rgba(255, 255, 255, 0.85);
        box-shadow: none; cursor: not-allowed; transform: none;
    }

    /* ---------- Label form kecil ---------- */
    .form-label-pos {
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.7px; text-transform: uppercase;
        color: var(--ink-400); margin-bottom: 6px; display: block;
    }

    /* ---------- Modal QRIS ---------- */
    .modal-glass .modal-content {
        border: 1px solid rgba(255, 255, 255, 0.85);
        border-radius: 22px; overflow: hidden;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px) saturate(150%);
        -webkit-backdrop-filter: blur(20px) saturate(150%);
        box-shadow: 0 28px 70px rgba(2, 132, 199, 0.28);
    }
    .qris-head {
        background: linear-gradient(135deg, var(--cyan-500), var(--blue-700));
        color: #fff; padding: 18px 22px;
    }
    .qris-total-badge {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(8, 145, 178, 0.14));
        border: 1px solid rgba(6, 182, 212, 0.25);
        border-radius: 16px; padding: 14px;
    }
    .qris-total-badge .amount { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.9rem; color: var(--cyan-700); }
    .qr-frame {
        display: inline-block; background: #fff; border-radius: 18px; padding: 14px;
        border: 1px solid rgba(6, 182, 212, 0.3);
        box-shadow: 0 12px 30px rgba(6, 182, 212, 0.16);
    }
    .qris-steps {
        background: linear-gradient(135deg, rgba(236, 254, 255, 0.9), rgba(240, 253, 250, 0.9));
        border: 1px solid rgba(6, 182, 212, 0.2); border-radius: 16px;
    }
</style>
@endpush

@section('content')
<!-- ================= PAGE HEADER ================= -->
<div class="page-head">
    <div>
        <h1 class="page-title">
            <span class="title-icon"><i class="bi bi-cart3"></i></span>
            Kasir / Point of Sale
        </h1>
        <p class="page-sub">Pilih obat dari katalog (klik baris atau tombol <b>Pilih</b>), kelola keranjang di panel kanan, lalu selesaikan pembayaran.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 justify-content-end">
        <span class="soft-chip"><i class="bi bi-lightning-charge-fill" style="color: var(--cyan-600);"></i> Alokasi stok otomatis FEFO</span>
    </div>
</div>

<div class="row g-4">
    <!-- ==========================================================
         KOLOM KIRI: KATALOG & PENCARIAN OBAT
         ========================================================== -->
    <div class="col-xl-7">
        <div class="glass-card catalog-card h-100 d-flex flex-column">

            <!-- Header panel katalog -->
            <div class="panel-head">
                <div>
                    <h6 class="panel-title">
                        <span class="panel-icon"><i class="bi bi-capsule"></i></span>
                        Katalog Obat Tersedia
                    </h6>
                    <small class="panel-sub">{{ $products->count() }} produk aktif &middot; batch ditampilkan mengikuti urutan FEFO (kadaluarsa terdekat)</small>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                    <button type="button" id="btnToggleSound" class="btn btn-sm btn-soft" title="Aktifkan/Nonaktifkan Suara Scanner">
                        <i class="bi bi-volume-up-fill me-1" id="soundIcon"></i><span id="soundLabel">Suara: ON</span>
                    </button>
                    <span class="soft-chip d-none d-sm-inline-flex"><i class="bi bi-info-circle" style="color: var(--cyan-600);"></i> Tekan <b>Enter</b> = pilih cepat</span>
                </div>
            </div>

            <!-- Input pencarian cepat obat / barcode scanner -->
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="searchMedicine" class="form-control"
                       placeholder="Ketik nama / kategori obat atau scan barcode...">
                <span class="search-hint"><i class="bi bi-upc-scan me-1"></i>Barcode ready</span>
            </div>

            <!-- Tabel daftar obat -->
            <div class="table-responsive scroll-area catalog-scroll flex-grow-1">
                <table class="table table-modern table-hover align-middle" id="medicineTable">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok Riil</th>
                            <th>Batch &amp; Expired (FEFO)</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="medicine-row" data-name="{{ strtolower($product->name) }}" data-category="{{ strtolower($product->category?->name ?? '') }}">
                                <td>
                                    <div class="med-name">{{ $product->name }}</div>
                                    <small class="med-unit">Satuan: {{ $product->unit }}</small>
                                </td>
                                <td>
                                    <span class="badge-cat">{{ $product->category?->name ?? 'Umum' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($product->total_stock <= $product->min_stock)
                                        <span class="pill pill-danger">
                                            <i class="bi bi-exclamation-triangle-fill"></i>{{ $product->total_stock }} {{ $product->unit }}
                                        </span>
                                        <div class="text-muted" style="font-size: 10px; margin-top: 3px;">Kritis &middot; min. {{ $product->min_stock }}</div>
                                    @else
                                        <span class="pill pill-success">
                                            <i class="bi bi-check-circle-fill"></i>{{ $product->total_stock }} {{ $product->unit }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->nearest_batch_number)
                                        <span class="batch-chip">
                                            <i class="bi bi-calendar-check"></i>{{ $product->nearest_expired_date }}
                                        </span>
                                        <div class="batch-no-mono mt-1">
                                            <i class="bi bi-boxes me-1"></i>No. Batch: {{ $product->nearest_batch_number }}
                                        </div>
                                    @else
                                        <span class="pill pill-danger"><i class="bi bi-slash-circle"></i>Stok Habis</span>
                                    @endif
                                </td>
                                <td class="text-end med-price">
                                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-cyan btn-add-cart"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-price="{{ $product->sell_price }}"
                                            data-stock="{{ $product->total_stock }}"
                                            data-unit="{{ $product->unit }}">
                                        <i class="bi bi-plus-lg"></i>Pilih
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-search" style="font-size: 2rem; color: var(--ink-400); opacity: .5;"></i>
                                    <div class="text-muted mt-2">Belum ada data obat yang aktif.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==========================================================
         KOLOM KANAN: KERANJANG BELANJA & PEMBAYARAN KASIR
         ========================================================== -->
    <div class="col-xl-5">
        <div class="glass-card cart-card cart-sticky d-flex flex-column">

            <!-- Header panel keranjang -->
            <div class="panel-head">
                <div>
                    <h6 class="panel-title">
                        <span class="panel-icon"><i class="bi bi-receipt-cutoff"></i></span>
                        Transaksi Kasir Aktif
                    </h6>
                    <small class="panel-sub">Keranjang pembelian pelanggan yang sedang berjalan</small>
                </div>
                <span class="soft-chip" id="cartCountChip">
                    <i class="bi bi-bucket" style="color: var(--cyan-600);"></i> Kosong
                </span>
            </div>

            <!-- Pilihan pelanggan -->
            <div class="mb-3">
                <label class="form-label-pos" for="customerId"><i class="bi bi-person me-1"></i>Data Pelanggan / Pasien</label>
                <select id="customerId" class="form-select">
                    <option value="">Pembeli Umum (Walk-in Customer)</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'Tanpa No. HP' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Tabel item keranjang -->
            <div class="table-responsive scroll-area cart-scroll mb-3">
                <table class="table table-modern table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Obat</th>
                            <th width="110" class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th width="40"></th>
                        </tr>
                    </thead>
                    <tbody id="cartItemsContainer">
                        <tr id="emptyCartRow">
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-cart-x" style="font-size: 1.9rem; color: var(--ink-400); opacity: .55;"></i>
                                <div class="text-muted small mt-2">Keranjang masih kosong.<br>Silakan pilih obat dari katalog di sebelah kiri.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Blok kalkulasi total tagihan -->
            <div class="total-block">
                <div class="d-flex justify-content-between align-items-center total-row-sub mb-2">
                    <span><i class="bi bi-receipt me-1"></i>Subtotal Tagihan</span>
                    <span id="subtotalText">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center total-row-grand pt-2">
                    <span class="total-label"><i class="bi bi-cash-stack me-1"></i>Total Tagihan</span>
                    <span id="grandTotalText" class="total-amount">Rp 0</span>
                </div>
            </div>

            <!-- Metode pembayaran (kartu pilihan) -->
            <div class="mb-3">
                <label class="form-label-pos"><i class="bi bi-wallet2 me-1"></i>Metode Pembayaran</label>
                <div class="pay-grid" role="group" aria-label="Pilih metode pembayaran">
                    <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="cash" checked>
                    <label class="pay-option" for="pay_cash"><i class="bi bi-cash-coin"></i><span>Tunai</span></label>

                    <input type="radio" class="btn-check" name="payment_method" id="pay_qris" value="qris">
                    <label class="pay-option" for="pay_qris">
                        <i class="bi bi-qr-code"></i>
                        <span>QRIS</span>
                        <small class="pay-note">Scan &amp; Bayar</small>
                    </label>

                    <input type="radio" class="btn-check" name="payment_method" id="pay_debit" value="debit">
                    <label class="pay-option" for="pay_debit"><i class="bi bi-credit-card-2-front"></i><span>Debit</span></label>

                    <input type="radio" class="btn-check" name="payment_method" id="pay_transfer" value="transfer">
                    <label class="pay-option" for="pay_transfer"><i class="bi bi-bank2"></i><span>Transfer</span></label>
                </div>
            </div>

            <!-- Input uang diterima & validasi anti-minus -->
            <div class="mb-3" id="cashInputSection">
                <label class="form-label-pos" for="paidAmount"><i class="bi bi-cash me-1"></i>Uang Diterima (Tunai)</label>
                <div class="cash-input-wrap">
                    <span class="cash-prefix">Rp</span>
                    <input type="number" id="paidAmount" placeholder="0" min="0">
                </div>

                <!-- Tombol nominal cepat -->
                <div class="quick-cash-row">
                    <button type="button" class="btn btn-sm quick-chip btn-quick-cash" data-amount="pas"><i class="bi bi-check2-square me-1"></i>Pas</button>
                    <button type="button" class="btn btn-sm quick-chip btn-quick-cash" data-amount="20000">20rb</button>
                    <button type="button" class="btn btn-sm quick-chip btn-quick-cash" data-amount="50000">50rb</button>
                    <button type="button" class="btn btn-sm quick-chip btn-quick-cash" data-amount="100000">100rb</button>
                </div>

                <!-- Indikator kembalian / peringatan uang kurang -->
                <div id="changeAlertBox" class="pay-status d-none">
                    <span id="changeAlertText"></span>
                </div>
            </div>

            <!-- Tombol checkout -->
            <button id="btnCheckout" class="btn btn-checkout w-100 mt-auto" disabled>
                <i class="bi bi-printer me-2"></i>Selesaikan Transaksi &amp; Cetak Struk
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL PEMBAYARAN QRIS                                        --}}
{{-- Gambar QRIS dapat diganti di: assets/qris.png                --}}
{{-- Salinan yang dibaca web: public/assets/qris.png              --}}
{{-- ============================================================ --}}
<div class="modal fade modal-glass" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-modal="true" role="dialog"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Header Modal QRIS --}}
            <div class="qris-head d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="qrisModalLabel">
                        <i class="bi bi-qr-code me-2"></i>Pembayaran QRIS
                    </h5>
                    <small class="opacity-90">Scan QR Code menggunakan m-banking / e-wallet</small>
                </div>
                <button type="button" class="btn-close btn-close-white" id="btnCloseQrisModal"
                        aria-label="Tutup Modal QRIS"></button>
            </div>

            {{-- Body Modal QRIS --}}
            <div class="modal-body text-center p-4">

                {{-- Tampilan Total Tagihan --}}
                <div class="qris-total-badge mb-3">
                    <small class="text-muted d-block mb-1">Total Tagihan yang Harus Dibayar</small>
                    <strong class="amount" id="qrisTotalDisplay">Rp 0</strong>
                </div>

                {{-- Gambar QRIS (ganti file di public/assets/qris.png dengan QRIS asli Anda) --}}
                <div class="qr-frame mb-3">
                    <img id="qrisImage"
                         src="{{ asset('assets/qris.png') }}"
                         data-fallback="{{ asset('images/qris-apotek.png') }}"
                         alt="QRIS Apotek Sehat Sehati"
                         class="img-fluid"
                         style="max-width: 270px; max-height: 330px; object-fit: contain;"
                         onerror="if (this.dataset.fallback && this.getAttribute('src') !== this.dataset.fallback) { this.setAttribute('src', this.dataset.fallback); } else { this.style.display='none'; document.getElementById('qrisImgFallback').style.display='flex'; }">
                    {{-- Fallback jika gambar tidak ditemukan --}}
                    <div id="qrisImgFallback" style="display:none; width:210px; height:210px;"
                         class="d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-qr-code" style="font-size: 4.5rem; color: var(--cyan-500);"></i>
                        <small class="text-muted mt-2">QRIS belum dikonfigurasi</small>
                        <small class="text-muted">Letakkan gambar QRIS di:</small>
                        <code class="small">public/assets/qris.png</code>
                    </div>
                </div>
                <small class="text-muted d-block" style="font-size: 10.5px; margin-top: -8px; margin-bottom: 14px;">
                    <i class="bi bi-shield-check me-1" style="color: var(--cyan-600);"></i>
                    QRIS resmi Apotek Sehat Sehati &middot; Nominal otomatis sesuai total tagihan
                </small>

                {{-- Petunjuk Pembayaran --}}
                <div class="text-start qris-steps p-3 mb-3">
                    <p class="fw-semibold mb-2 small" style="color: var(--cyan-700);">
                        <i class="bi bi-info-circle-fill me-1"></i>Petunjuk Pembayaran QRIS:
                    </p>
                    <ol class="mb-0 small text-muted ps-3">
                        <li>Buka aplikasi m-banking / GoPay / OVO / Dana / ShopeePay di HP</li>
                        <li>Pilih menu <strong>"Bayar"</strong> atau <strong>"Scan QR"</strong></li>
                        <li>Arahkan kamera ke kode QR di atas</li>
                        <li>Pastikan nominal sesuai: <strong id="qrisAmountHint" style="color: var(--cyan-700);">Rp 0</strong></li>
                        <li>Konfirmasi pembayaran di aplikasi Anda</li>
                        <li>Tunjukkan bukti berhasil kepada kasir, lalu klik tombol di bawah</li>
                    </ol>
                </div>

                {{-- Label daftar e-wallet yang diterima --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-1">
                    @foreach(['GoPay','OVO','Dana','ShopeePay','BCA Mobile','BRImo','Livin\' Mandiri'] as $wallet)
                        <span class="soft-chip" style="font-size: 10.5px; padding: 4px 10px;">{{ $wallet }}</span>
                    @endforeach
                </div>

            </div>

            {{-- Footer Modal QRIS --}}
            <div class="modal-footer border-top justify-content-between">
                <button type="button" class="btn btn-soft" id="btnCloseQrisModal2">
                    <i class="bi bi-arrow-left me-1"></i>Batal / Ganti Metode
                </button>
                <button type="button" class="btn btn-cyan btn-lg px-4" id="btnConfirmQris">
                    <i class="bi bi-check2-circle me-2"></i>Konfirmasi Pembayaran QRIS Berhasil
                </button>
            </div>

        </div>
    </div>
</div>
{{-- ============================================================ --}}
{{-- AKHIR MODAL QRIS                                             --}}
{{-- ============================================================ --}}

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let cart = [];
        let grandTotal = 0;

        // ==========================================
        // AUDIO FEEDBACK ENGINE (WEB AUDIO API)
        // ==========================================
        let audioEnabled = localStorage.getItem('pos_sound_enabled') !== 'false';
        let audioCtx = null;

        function getAudioContext() {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        // Suara 'Beep' Barcode Scanner (1150 Hz, 55ms)
        function playScannerBeep() {
            if (!audioEnabled) return;
            try {
                const ctx = getAudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1150, ctx.currentTime);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.055);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.06);
            } catch (e) {
                console.log('Audio feedback error:', e);
            }
        }

        // Suara Harmonis Sukses Transaksi Selesai
        function playSuccessChime() {
            if (!audioEnabled) return;
            try {
                const ctx = getAudioContext();
                const playNote = (freq, start, duration) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + start);
                    gain.gain.setValueAtTime(0.25, ctx.currentTime + start);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + duration);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(ctx.currentTime + start);
                    osc.stop(ctx.currentTime + start + duration);
                };
                playNote(587.33, 0.0, 0.15); // D5
                playNote(880.00, 0.1, 0.25); // A5
            } catch (e) {}
        }

        // Suara Peringatan Error / Stok Kurang
        function playErrorBuzz() {
            if (!audioEnabled) return;
            try {
                const ctx = getAudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.16);
            } catch (e) {}
        }

        // Tombol Toggle Mute / Unmute Suara
        const btnToggleSound = document.getElementById('btnToggleSound');
        const soundIcon = document.getElementById('soundIcon');
        const soundLabel = document.getElementById('soundLabel');

        function updateSoundUi() {
            if (audioEnabled) {
                btnToggleSound.className = 'btn btn-sm btn-soft';
                btnToggleSound.style.color = 'var(--cyan-700)';
                soundIcon.className = 'bi bi-volume-up-fill me-1';
                soundLabel.innerText = 'Suara: ON';
            } else {
                btnToggleSound.className = 'btn btn-sm btn-soft';
                btnToggleSound.style.color = '';
                soundIcon.className = 'bi bi-volume-mute-fill me-1';
                soundLabel.innerText = 'Suara: OFF';
            }
        }
        updateSoundUi();

        btnToggleSound.addEventListener('click', function () {
            audioEnabled = !audioEnabled;
            localStorage.setItem('pos_sound_enabled', audioEnabled);
            updateSoundUi();
            if (audioEnabled) {
                playScannerBeep();
            }
        });

        // ==========================================
        // LOGIKA KERANJANG & KASIR POS
        // ==========================================
        const cartContainer = document.getElementById('cartItemsContainer');
        const emptyCartRow = document.getElementById('emptyCartRow');
        const subtotalText = document.getElementById('subtotalText');
        const grandTotalText = document.getElementById('grandTotalText');
        const paidAmountInput = document.getElementById('paidAmount');
        const changeAlertBox = document.getElementById('changeAlertBox');
        const changeAlertText = document.getElementById('changeAlertText');
        const btnCheckout = document.getElementById('btnCheckout');
        const cashInputSection = document.getElementById('cashInputSection');
        const searchInput = document.getElementById('searchMedicine');
        const cartCountChip = document.getElementById('cartCountChip');

        // 1. Live Filter Pencarian Obat & Scan Barcode (Tekan Enter Langsung Pilih)
        searchInput.addEventListener('input', function (e) {
            const keyword = e.target.value.toLowerCase();
            document.querySelectorAll('.medicine-row').forEach(row => {
                const name = row.getAttribute('data-name');
                const category = row.getAttribute('data-category');
                if (name.includes(keyword) || category.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Fitur Otomatis Scanner Barcode: Jika kasir scan barcode fisik / tekan Enter pada input
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstVisibleRow = Array.from(document.querySelectorAll('.medicine-row')).find(row => row.style.display !== 'none');
                if (firstVisibleRow) {
                    const btn = firstVisibleRow.querySelector('.btn-add-cart');
                    if (btn) {
                        btn.click();
                        searchInput.value = '';
                        document.querySelectorAll('.medicine-row').forEach(row => row.style.display = '');
                    }
                } else {
                    playErrorBuzz();
                }
            }
        });

        // 2. Tambah Obat ke Keranjang dengan Efek Suara Beep
        document.querySelectorAll('.btn-add-cart').forEach(button => {
            button.addEventListener('click', function () {
                const id = parseInt(this.dataset.id);
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                const stock = parseInt(this.dataset.stock);
                const unit = this.dataset.unit;

                const existingItem = cart.find(item => item.product_id === id);

                if (existingItem) {
                    if (existingItem.quantity + 1 > stock) {
                        playErrorBuzz();
                        Swal.fire('Stok Terbatas', `Stok maksimum obat ${name} adalah ${stock} ${unit}`, 'warning');
                        return;
                    }
                    existingItem.quantity += 1;
                } else {
                    if (stock < 1) {
                        playErrorBuzz();
                        Swal.fire('Stok Kosong', `Obat ${name} tidak memiliki batch aktif yang tersedia.`, 'error');
                        return;
                    }
                    cart.push({
                        product_id: id,
                        name: name,
                        price: price,
                        quantity: 1,
                        max_stock: stock,
                        unit: unit
                    });
                }

                // Bunyikan Suara Scanner Beep!
                playScannerBeep();
                renderCart();
            });
        });

        // 2b. Pintasan intuitif: klik di mana pun pada baris obat = pilih obat
        document.querySelectorAll('.medicine-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.closest('.btn-add-cart')) return; // biarkan tombol menangani sendiri
                const btn = this.querySelector('.btn-add-cart');
                if (btn) btn.click();
            });
        });

        // 3. Render Tampilan Keranjang
        function renderCart() {
            if (cart.length === 0) {
                cartContainer.innerHTML = '';
                cartContainer.appendChild(emptyCartRow);
                grandTotal = 0;
                subtotalText.innerText = 'Rp 0';
                grandTotalText.innerText = 'Rp 0';
                cartCountChip.innerHTML = '<i class="bi bi-bucket" style="color: var(--cyan-600);"></i> Kosong';
                validatePayment();
                return;
            }

            cartContainer.innerHTML = '';
            grandTotal = 0;
            let totalQty = 0;

            cart.forEach((item, index) => {
                const itemSubtotal = item.quantity * item.price;
                grandTotal += itemSubtotal;
                totalQty += item.quantity;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="cart-item-name">${item.name}</div>
                        <small class="cart-item-price">@ Rp ${item.price.toLocaleString('id-ID')}</small>
                    </td>
                    <td class="text-center">
                        <div class="qty-stepper">
                            <button type="button" class="stepper-btn btn-decrease" data-index="${index}" aria-label="Kurangi jumlah">
                                <i class="bi bi-dash-lg"></i>
                            </button>
                            <span class="qty-value">${item.quantity}</span>
                            <button type="button" class="stepper-btn btn-increase" data-index="${index}" aria-label="Tambah jumlah">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-end">
                        <span class="cart-item-subtotal">Rp ${itemSubtotal.toLocaleString('id-ID')}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn-remove" data-index="${index}" aria-label="Hapus item">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                `;
                cartContainer.appendChild(tr);
            });

            subtotalText.innerText = `Rp ${grandTotal.toLocaleString('id-ID')}`;
            grandTotalText.innerText = `Rp ${grandTotal.toLocaleString('id-ID')}`;
            cartCountChip.innerHTML = `<i class="bi bi-bucket" style="color: var(--cyan-600);"></i> ${cart.length} produk &middot; ${totalQty} item`;

            // Event listener kuantitas dan hapus item
            document.querySelectorAll('.btn-decrease').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.dataset.index);
                    if (cart[idx].quantity > 1) {
                        cart[idx].quantity -= 1;
                    } else {
                        cart.splice(idx, 1);
                    }
                    playScannerBeep();
                    renderCart();
                });
            });

            document.querySelectorAll('.btn-increase').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.dataset.index);
                    if (cart[idx].quantity + 1 > cart[idx].max_stock) {
                        playErrorBuzz();
                        Swal.fire('Batas Stok', `Maksimum stok ${cart[idx].max_stock} ${cart[idx].unit}`, 'warning');
                        return;
                    }
                    cart[idx].quantity += 1;
                    playScannerBeep();
                    renderCart();
                });
            });

            document.querySelectorAll('.btn-remove').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.dataset.index);
                    cart.splice(idx, 1);
                    renderCart();
                });
            });

            validatePayment();
        }

        // 4. Perhitungan Nominal Cepat
        document.querySelectorAll('.btn-quick-cash').forEach(btn => {
            btn.addEventListener('click', function () {
                const amount = this.dataset.amount;
                if (amount === 'pas') {
                    paidAmountInput.value = grandTotal;
                } else {
                    paidAmountInput.value = parseFloat(amount);
                }
                playScannerBeep();
                validatePayment();
            });
        });

        paidAmountInput.addEventListener('input', validatePayment);

        // Pergantian Metode Pembayaran
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'cash') {
                    cashInputSection.classList.remove('d-none');
                } else {
                    cashInputSection.classList.add('d-none');
                    paidAmountInput.value = grandTotal;
                }

                // QRIS interaktif: langsung tampilkan modal scan saat dipilih.
                if (this.value === 'qris') {
                    if (cart.length === 0) {
                        playErrorBuzz();
                        document.getElementById('pay_cash').checked = true;
                        cashInputSection.classList.remove('d-none');
                        validatePayment();
                        Swal.fire({
                            icon: 'info',
                            title: 'Keranjang Masih Kosong',
                            text: 'Tambahkan obat terlebih dahulu, lalu pilih QRIS untuk menampilkan kode pembayaran.',
                            confirmButtonText: 'Mengerti'
                        });
                        return;
                    }

                    updateQrisModalTotal();
                    playScannerBeep();
                    qrisModalInstance.show();
                    return;
                }

                playScannerBeep();
                validatePayment();
            });
        });

        // 5. Validasi Finansial Anti-Minus (Celah Bug Skripsi 2023 Ditutup)
        function validatePayment() {
            if (cart.length === 0 || grandTotal === 0) {
                btnCheckout.disabled = true;
                changeAlertBox.classList.add('d-none');
                return;
            }

            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (selectedMethod !== 'cash') {
                btnCheckout.disabled = false;
                changeAlertBox.classList.add('d-none');
                return;
            }

            const paid = parseFloat(paidAmountInput.value) || 0;

            if (paid >= grandTotal) {
                const change = paid - grandTotal;
                changeAlertBox.className = 'pay-status pay-status-ok d-block';
                changeAlertText.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Kembalian: Rp ${change.toLocaleString('id-ID')}`;
                btnCheckout.disabled = false;
            } else {
                const shortfall = grandTotal - paid;
                changeAlertBox.className = 'pay-status pay-status-short d-block';
                changeAlertText.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i> Uang Kurang: Rp ${shortfall.toLocaleString('id-ID')} (Pembayaran Tidak Cukup)`;
                btnCheckout.disabled = true; // KUNCI ANTI-MINUS: Tombol tidak bisa ditekan
            }
        }

        // ==========================================
        // QRIS MODAL LOGIC
        // ==========================================
        const qrisModalEl = document.getElementById('qrisModal');
        const qrisModalInstance = new bootstrap.Modal(qrisModalEl);

        // Fungsi update tampilan total pada modal QRIS
        function updateQrisModalTotal() {
            const formatted = `Rp ${grandTotal.toLocaleString('id-ID')}`;
            document.getElementById('qrisTotalDisplay').innerText = formatted;
            document.getElementById('qrisAmountHint').innerText = formatted;
        }

        // Tombol Tutup Modal QRIS (header X & tombol Batal)
        document.getElementById('btnCloseQrisModal').addEventListener('click', function () {
            qrisModalInstance.hide();
            // Kembalikan ke radio Cash agar input terlihat normal
            document.getElementById('pay_cash').checked = true;
            cashInputSection.classList.remove('d-none');
            paidAmountInput.value = '';
            validatePayment();
        });
        document.getElementById('btnCloseQrisModal2').addEventListener('click', function () {
            qrisModalInstance.hide();
            document.getElementById('pay_cash').checked = true;
            cashInputSection.classList.remove('d-none');
            paidAmountInput.value = '';
            validatePayment();
        });

        // Tombol Konfirmasi QRIS Berhasil
        document.getElementById('btnConfirmQris').addEventListener('click', function () {
            qrisModalInstance.hide();
            // Jalankan checkout dengan method 'qris', paid_amount = grandTotal
            processCheckout('qris', grandTotal);
        });

        // ==========================================
        // FUNGSI PROSES CHECKOUT (dipanggil oleh tombol checkout tunai/QRIS/debit/transfer)
        // ==========================================
        function processCheckout(paymentMethod, paidAmount) {
            if (cart.length === 0) return;

            const customerId = document.getElementById('customerId').value;

            btnCheckout.disabled = true;
            btnCheckout.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses FEFO...';

            fetch("{{ route('pos.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer_id: customerId ? parseInt(customerId) : null,
                    payment_method: paymentMethod,
                    paid_amount: paidAmount,
                    items: cart.map(item => ({
                        product_id: item.product_id,
                        quantity: item.quantity
                    }))
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Transaksi gagal.'));
                }
                return data;
            })
            .then(result => {
                const trx = result.data.transaction;
                const summary = result.data.summary;
                const details = result.data.details;

                // Bunyikan Suara Sukses Transaksi (termasuk setelah konfirmasi QRIS)!
                playSuccessChime();

                let detailHtml = '<ul class="list-group list-group-flush text-start small mb-3">';
                details.forEach(d => {
                    detailHtml += `
                        <li class="list-group-item d-flex justify-content-between py-1" style="background: transparent;">
                            <div>
                                <strong>${d.product_name}</strong> (${d.quantity}x)
                                <div class="text-muted font-monospace" style="font-size: 11px;">Batch: ${d.batch_number} (Exp: ${d.expired_date})</div>
                            </div>
                            <span class="fw-semibold">Rp ${d.subtotal.toLocaleString('id-ID')}</span>
                        </li>
                    `;
                });
                detailHtml += '</ul>';

                // Badge metode pembayaran pada struk
                const methodBadgeMap = {
                    cash: '<span class="badge bg-success">Tunai</span>',
                    qris: '<span class="badge bg-info text-dark"><i class="bi bi-qr-code me-1"></i>QRIS</span>',
                    debit: '<span class="badge bg-primary">Debit</span>',
                    transfer: '<span class="badge bg-secondary">Transfer</span>'
                };
                const methodBadge = methodBadgeMap[paymentMethod] || `<span class="badge bg-secondary">${paymentMethod}</span>`;

                Swal.fire({
                    title: 'Transaksi Berhasil!',
                    icon: 'success',
                    html: `
                        <div class="alert alert-light border text-center py-2 mb-2">
                            <small class="text-muted d-block">Nomor Faktur / Invoice</small>
                            <strong class="fs-5 font-monospace" style="color:#0891b2;">${trx.invoice_number}</strong>
                            <div class="mt-1">${methodBadge}</div>
                        </div>
                        ${detailHtml}
                        <div class="d-flex justify-content-between fw-bold text-dark border-top pt-2">
                            <span>Total:</span>
                            <span>Rp ${summary.total_amount.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>Bayar:</span>
                            <span>Rp ${summary.paid_amount.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold text-success fs-6 border-top mt-1 pt-1">
                            <span>Kembalian:</span>
                            <span>Rp ${summary.change_amount.toLocaleString('id-ID')}</span>
                        </div>
                    `,
                    confirmButtonText: '<i class="bi bi-printer me-1"></i> Selesai & Cetak Struk',
                    showCancelButton: false
                }).then(() => {
                    cart = [];
                    paidAmountInput.value = '';
                    renderCart();
                    window.location.reload();
                });
            })
            .catch(err => {
                playErrorBuzz();
                Swal.fire('Gagal Memproses', err.message, 'error');
                btnCheckout.disabled = false;
                btnCheckout.innerHTML = '<i class="bi bi-printer me-2"></i>Selesaikan Transaksi & Cetak Struk';
            });
        }

        // 6. Eksekusi Checkout via AJAX — Intercept QRIS untuk tampilkan modal terlebih dahulu
        btnCheckout.addEventListener('click', function () {
            if (cart.length === 0) return;

            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Jika metode QRIS → tampilkan modal QRIS sebelum proses checkout
            if (paymentMethod === 'qris') {
                updateQrisModalTotal();   // Perbarui total di modal
                playScannerBeep();        // Efek suara saat modal terbuka
                qrisModalInstance.show(); // Tampilkan modal QRIS
                return;                   // Hentikan proses, tunggu konfirmasi dari kasir
            }

            // Metode tunai / debit / transfer: langsung proses checkout
            const paidAmount = parseFloat(paidAmountInput.value) || 0;
            processCheckout(paymentMethod, paidAmount);
        });
    });
</script>
@endpush

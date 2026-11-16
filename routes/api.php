<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosTransactionController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes - Sistem Informasi Apotek Sehat Sehati
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // API Pencarian Cepat Obat untuk Kasir / Barcode Scanner
    Route::get('/products/search', [ProductController::class, 'searchApi']);

    // API Transaksi Kasir POS (Atomic FEFO & Validasi Finansial)
    Route::post('/pos/checkout', [PosTransactionController::class, 'checkout']);
});


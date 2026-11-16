<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\OnlineOrderController;
use App\Http\Controllers\PosTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Apotek Sehat Sehati Punggur
|--------------------------------------------------------------------------
*/

// =========================================================================
// PORTAL PELANGGAN (PUBLIK / TANPA LOGIN)
// =========================================================================
Route::get('/', [CustomerPortalController::class, 'home'])->name('home');

Route::prefix('pelanggan')->name('portal.')->group(function () {
    Route::get('/', [CustomerPortalController::class, 'index'])->name('index');
    Route::get('/tentang', [CustomerPortalController::class, 'about'])->name('about');
    Route::get('/alamat', [CustomerPortalController::class, 'address'])->name('address');
    Route::get('/checkout', [CustomerPortalController::class, 'checkoutForm'])->name('checkout');
    Route::post('/checkout', [CustomerPortalController::class, 'storeOrder'])->name('checkout.store');
    Route::get('/pesanan/{invoice}', [CustomerPortalController::class, 'showOrder'])->name('order.show');
    Route::get('/lacak', [CustomerPortalController::class, 'trackForm'])->name('track');
    Route::post('/lacak', [CustomerPortalController::class, 'track'])->name('track.search');
});

// =========================================================================
// RUTE TAMU (GUEST ONLY) - HALAMAN LOGIN PETUGAS
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// =========================================================================
// RUTE TERPROTEKSI AUTENTIKASI (WAJIB LOGIN)
// =========================================================================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 1. Modul Kasir / POS (Bisa diakses oleh Kasir dan Admin)
    Route::middleware('role:kasir,admin')->prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosTransactionController::class, 'index'])->name('index');
        Route::post('/checkout', [PosTransactionController::class, 'checkout'])->name('checkout');
    });

    // 2. Modul Pesanan Online dari Portal Pelanggan (Kasir dan Admin)
    Route::middleware('role:kasir,admin')->prefix('pesanan-online')->name('orders.')->group(function () {
        Route::get('/', [OnlineOrderController::class, 'index'])->name('index');
        Route::post('/{order}/konfirmasi-lunas', [OnlineOrderController::class, 'markPaid'])->name('mark-paid');
        Route::post('/{order}/batalkan', [OnlineOrderController::class, 'cancel'])->name('cancel');
    });

    // 3. Modul Manajemen Stok Obat & Batch FEFO (Bisa diakses oleh Apoteker dan Admin)
    Route::middleware('role:apoteker,admin')->prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
    });

    // 4. Modul Laporan Penjualan & Keuangan (Bisa diakses oleh Apoteker dan Admin)
    Route::middleware('role:apoteker,admin')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    });
});

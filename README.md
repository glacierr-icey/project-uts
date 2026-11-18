# Apotek Sehat Sehati Punggur

Sistem Informasi Penjualan Apotek berbasis Laravel 12 + SQLite, dilengkapi **portal pelanggan publik** untuk belanja obat online.

## Fitur Utama

### Portal Pelanggan (publik, tanpa login)
- Katalog obat lengkap dengan **harga & stok real-time** dari batch aktif (FEFO).
- Pencarian dan filter kategori.
- Keranjang belanja (disimpan di `localStorage`).
- Checkout dengan dua metode pengiriman:
  - **Ambil Sendiri di Toko (Pick-up)**
  - **Kirim ke Alamat (Delivery)** + form alamat lengkap.
- Tiga metode pembayaran:
  - **COD (Bayar di Tempat)**
  - **Transfer Bank** (nomor rekening toko)
  - **QRIS** (`public/assets/qris.png`)
- Halaman **Tentang Toko**, **Alamat Toko** (dengan peta), dan **Lacak Pesanan**.
- Halaman konfirmasi pesanan + nomor invoice.

### Sistem Internal (login petugas)
- **Kasir / POS** untuk transaksi langsung di toko.
- **Pesanan Online** untuk melihat antrean pesanan dari portal, mengonfirmasi pembayaran, atau membatalkan pesanan (stok otomatis dikembalikan).
- **Manajemen Stok Obat & Batch FEFO**.
- **Laporan Penjualan** yang mencakup transaksi POS, pesanan online, status pembayaran, dan metode pengiriman.

## Akun Demo

| Role     | Email               | Password    |
|----------|---------------------|-------------|
| Admin    | admin@apotek.com    | password123 |
| Kasir    | kasir@apotek.com    | password123 |
| Apoteker | apoteker@apotek.com | password123 |

## Rute Penting

| Metode | URI | Keterangan |
|--------|-----|------------|
| GET | `/` | Halaman depan (portal untuk tamu, redirect role untuk petugas) |
| GET | `/pelanggan` | Katalog obat |
| GET | `/pelanggan/tentang` | Tentang toko |
| GET | `/pelanggan/alamat` | Alamat & peta toko |
| GET/POST | `/pelanggan/checkout` | Form & simpan pesanan |
| GET | `/pelanggan/pesanan/{invoice}` | Detail pesanan |
| GET/POST | `/pelanggan/lacak` | Lacak pesanan |
| GET | `/pesanan-online` | Antrean pesanan online (kasir/admin) |
| POST | `/pesanan-online/{order}/konfirmasi-lunas` | Konfirmasi pembayaran |
| POST | `/pesanan-online/{order}/batalkan` | Batalkan & kembalikan stok |
| GET | `/reports/sales` | Laporan penjualan |

## Konfigurasi Toko

Profil toko, jam operasional, rekening bank, dan path QRIS berada di:

```
config/pharmacy.php
```

> Nomor telepon, email, dan rekening bank pada file tersebut masih berupa **data demo**. Ganti dengan data resmi Apotek Sehat Sehati sebelum digunakan.

## Menjalankan Aplikasi

```bash
cd apotek-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Deploy ke Vercel (vercel-php)

Aplikasi sudah disiapkan untuk runtime `vercel-php@0.7.4` (PHP 8.3):

- `api/index.php` adalah entry point. Entry point ini membuat direktori writable di `/tmp`,
  mengalihkan cache/compiled view/session ke `/tmp`, lalu menyalin
  `database/vercel-seed.sqlite` ( database hasil `migrate --seed`) ke
  `/tmp/database.sqlite` saat cold start. **Tidak ada migrasi yang dijalankan di dalam
  request**, sehingga menghindari 500/timeout. Jika file seed tidak ikut ter-deploy,
  entry point fallback ke `migrate` + `db:seed`.
- Jika masih gagal, buka `/?__vercel_diag=1` untuk mengecek autoload, seed DB, dan izin
  `/tmp`. Pesan fatal juga ditampilkan di halaman dan di Vercel Function Logs.
- `vercel.json` meneruskan aset statik (`/build`, `/assets`, `/images`, `/favicon.ico`,
  `/robots.txt`) langsung dari folder `public`, sisanya ke `api/index.php`.
- File `.env` **tidak** ikut ter-deploy. Set variabel di Vercel Dashboard
  (rujuk `.env.vercel.example`). Paling penting: `APP_KEY`, `APP_URL`, dan
  `LOG_CHANNEL=stderr`.
- `DB_DATABASE=/tmp/database.sqlite` bersifat **ephemeral**: data kembali kosong saat
  cold start/redeploy. Untuk data persisten, pakai MySQL/Postgres eksternal dan set
  `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` di Vercel.

## Pengujian

```bash
php artisan test
```

Mencakup pengujian portal publik, pembuatan pesanan tamu, serta konfirmasi/pembatalan pesanan online.

## Catatan Implementasi

- Pesanan online disimpan pada tabel `sales_transactions` (`order_channel = 'online'`) dan `sales_details`, sehingga otomatis masuk ke laporan penjualan.
- Pesanan tamu dihubungkan ke `customers` berdasarkan nomor HP dan dicatat atas nama akun admin sebagai petugas sistem.
- Stok langsung dialokasikan dengan metode **FEFO** saat pesanan dibuat; pembatalan mengembalikan stok ke batch asal.
- Status awal pesanan online adalah `pending` hingga dikonfirmasi petugas menjadi `paid`.

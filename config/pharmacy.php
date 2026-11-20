<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profil Toko / Apotek
    |--------------------------------------------------------------------------
    | Data ini dipakai oleh portal pelanggan (halaman tentang, alamat, checkout,
    | dan instruksi pembayaran). Silakan sesuaikan nomor rekening & kontak
    | dengan data resmi Apotek Sehat Sehati.
    */
    'name'      => 'Apotek Sehat Sehati',
    'legal_name'=> 'Apotek Sehat Sehati Punggur',
    'tagline'   => 'Sehat dimulai dari langkah kecil. Belanja obat lebih mudah, kapan saja.',
    'address'   => 'Jl. Metro - Kotagajah, Dusun Tanggul Angin, Kec. Punggur, Kab. Lampung Tengah, Lampung',
    'phone'     => '0877-7384-5477',
    'whatsapp'  => '6287773845477',
    'email'     => 'halo@apoteksehatsehati.com',

    /*
    |--------------------------------------------------------------------------
    | Kontak Konfirmasi Pembayaran
    |--------------------------------------------------------------------------
    | phone / whatsapp = nomor WhatsApp utama (0877-7384-5477 -> 6287773845477)
    | yang dipakai semua tautan wa.me di checkout, detail pesanan, dan halaman lain.
    | telegram_* = kontak fallback Telegram (nomor lama) + URL bot HTTPS.
    | notification_contact = nomor WhatsApp yang ditampilkan pada tombol cepat.
    */
    'telegram_phone'   => '6281928310718',
    'telegram_contact' => '0819-2831-0718',
    'telegram_bot_url' => 'https://t.me/pharmacy_payments_bot',
    'notification_contact' => '0877-7384-5477',

    'hours' => [
        ['day' => 'Senin - Jumat', 'time' => '07.30 - 21.00 WIB'],
        ['day' => 'Sabtu',         'time' => '07.30 - 21.00 WIB'],
        ['day' => 'Minggu',        'time' => '08.00 - 20.00 WIB'],
    ],

    'maps_query' => 'Apotek Sehat Sehati Punggur, Lampung Tengah',

    /*
    |--------------------------------------------------------------------------
    | Rekening Bank Toko
    |--------------------------------------------------------------------------
    */
    'banks' => [
        [
            'bank'           => 'Bank Mandiri',
            'account_number' => '137-00-1234567-8',
            'account_name'   => 'Apotek Sehat Sehati',
            'color'          => '#003d79',
        ],
        [
            'bank'           => 'Bank BRI',
            'account_number' => '0341-01-987654-53-2',
            'account_name'   => 'Apotek Sehat Sehati',
            'color'          => '#00529c',
        ],
        [
            'bank'           => 'Bank Syariah Indonesia',
            'account_number' => '7123-4567-89',
            'account_name'   => 'Apotek Sehat Sehati',
            'color'          => '#00a39d',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pembayaran QRIS
    |--------------------------------------------------------------------------
    */
    'qris_path' => 'assets/qris.png',
];

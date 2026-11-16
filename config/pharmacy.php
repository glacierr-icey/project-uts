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
    'phone'     => '0812-3456-7890',
    'whatsapp'  => '6281234567890',
    'email'     => 'halo@apoteksehatsehati.com',

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

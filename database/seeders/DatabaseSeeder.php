<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data akun multi-role terenkripsi bcrypt,
     * master pelanggan, katalog obat lengkap, dan riwayat penjualan dummy.
     */
    public function run(): void
    {
        // 1. Akun Multi-Role dengan Password Bcrypt
        // Role 1: Admin / Owner (Akses penuh ke semua modul)
        User::firstOrCreate(
            ['email' => 'admin@apotek.com'],
            [
                'name'     => 'dr. Aulia Anggun D.K (Owner & Admin)',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Role 2: Kasir (Akses khusus kasir transaksi POS)
        User::firstOrCreate(
            ['email' => 'kasir@apotek.com'],
            [
                'name'     => 'Rina Agustina (Kasir)',
                'password' => Hash::make('password123'),
                'role'     => 'kasir',
            ]
        );

        // Role 3: Apoteker / Manajer (Akses manajemen stok, batch ED, dan laporan)
        User::firstOrCreate(
            ['email' => 'apoteker@apotek.com'],
            [
                'name'     => 'Apt. Hendra Pratama, S.Farm (Apoteker)',
                'password' => Hash::make('password123'),
                'role'     => 'apoteker',
            ]
        );

        // 2. Data Pelanggan Contoh (untuk variasi riwayat transaksi)
        $customers = [
            [
                'name'    => 'Rendi Pratama',
                'phone'   => '081234567890',
                'address' => 'Jl. Jenderal Sudirman No. 12, Kota Metro',
            ],
            [
                'name'    => 'Ibu Sumarni',
                'phone'   => '085712345678',
                'address' => 'Dusun Tanggul Angin, Punggur',
            ],
            [
                'name'    => 'Bapak Sugeng Riyadi',
                'phone'   => '081377889900',
                'address' => 'Jl. Raya Punggur No. 45, Lampung Tengah',
            ],
            [
                'name'    => 'Siti Aminah',
                'phone'   => '082211334455',
                'address' => 'Dusun Sri Sawahan, Punggur',
            ],
            [
                'name'    => 'Dewi Lestari',
                'phone'   => '089612345670',
                'address' => 'Perumahan Metro Garden Blok C-7, Kota Metro',
            ],
            [
                'name'    => 'Agus Setiawan',
                'phone'   => '081255667788',
                'address' => 'Jl. KH. Ahmad Dahlan No. 8, Punggur',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
        }

        // 3. Katalog obat lengkap dengan batch FEFO
        // 4. Riwayat transaksi penjualan dummy (agar laporan tidak nol)
        $this->call([
            PharmacyCatalogSeeder::class,
            SalesHistorySeeder::class,
        ]);
    }
}

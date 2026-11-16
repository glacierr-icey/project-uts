<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Database\Seeder;

class PharmacyCatalogSeeder extends Seeder
{
    /**
     * Seed katalog obat apotek yang lengkap dan realistis.
     *
     * Setiap obat memiliki satu atau beberapa batch dengan nomor batch,
     * harga beli/jual, stok riil, dan tanggal kadaluarsa berbeda sehingga
     * simulasi FEFO (First Expired, First Out) terlihat lebih nyata.
     */
    public function run(): void
    {
        // -----------------------------------------------------------------
        // 1. Kategori obat
        // -----------------------------------------------------------------
        $categoryNames = [
            'Analgesik & Antipiretik',
            'Obat Batuk & Flu',
            'Vitamin & Suplemen',
            'Obat Luar & Salep',
            'Antibiotik',
            'Antihistamin & Alergi',
            'Saluran Cerna & Pencernaan',
            'Obat Kardiovaskular',
            'Cairan & Elektrolit',
        ];

        $categories = [];
        foreach ($categoryNames as $categoryName) {
            $categories[$categoryName] = Category::firstOrCreate(['name' => $categoryName]);
        }

        // -----------------------------------------------------------------
        // 2. Katalog obat + batch FEFO
        // -----------------------------------------------------------------
        $catalog = [
            // ================= ANALGESIK & ANTIPIRETIK =================
            [
                'category' => 'Analgesik & Antipiretik',
                'name' => 'Sanmol Paracetamol 500mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-SNM-01', 'buy_price' => 2500, 'sell_price' => 3500, 'stock' => 18, 'expired_months' => 2],
                    ['batch_number' => 'BATCH-SNM-02', 'buy_price' => 2500, 'sell_price' => 3500, 'stock' => 60, 'expired_months' => 14],
                ],
            ],
            [
                'category' => 'Analgesik & Antipiretik',
                'name' => 'Ibuprofen 400mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-IBU-01', 'buy_price' => 4200, 'sell_price' => 6000, 'stock' => 40, 'expired_months' => 5],
                    ['batch_number' => 'BATCH-IBU-02', 'buy_price' => 4200, 'sell_price' => 6000, 'stock' => 55, 'expired_months' => 16],
                ],
            ],
            [
                'category' => 'Analgesik & Antipiretik',
                'name' => 'Bodrex Migrain',
                'unit' => 'Strip',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-BDX-01', 'buy_price' => 3500, 'sell_price' => 5000, 'stock' => 30, 'expired_months' => 7],
                ],
            ],
            [
                'category' => 'Analgesik & Antipiretik',
                'name' => 'Paracetamol Sirup 60ml',
                'unit' => 'Botol',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-PCS-01', 'buy_price' => 8500, 'sell_price' => 12000, 'stock' => 24, 'expired_months' => 4],
                    ['batch_number' => 'BATCH-PCS-02', 'buy_price' => 8500, 'sell_price' => 12000, 'stock' => 20, 'expired_months' => 11],
                ],
            ],
            [
                'category' => 'Analgesik & Antipiretik',
                'name' => 'Asam Mefenamat 500mg',
                'unit' => 'Strip',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-MEF-01', 'buy_price' => 3800, 'sell_price' => 5500, 'stock' => 35, 'expired_months' => 9],
                ],
            ],

            // ================= OBAT BATUK & FLU =================
            [
                'category' => 'Obat Batuk & Flu',
                'name' => 'Laserin Sirup 60ml',
                'unit' => 'Botol',
                'min_stock' => 5,
                'batches' => [
                    ['batch_number' => 'BATCH-LSR-01', 'buy_price' => 10000, 'sell_price' => 13000, 'stock' => 25, 'expired_months' => 8],
                    ['batch_number' => 'BATCH-LSR-02', 'buy_price' => 10000, 'sell_price' => 13000, 'stock' => 18, 'expired_months' => 17],
                ],
            ],
            [
                'category' => 'Obat Batuk & Flu',
                'name' => 'OBH Combi Batuk Flu 60ml',
                'unit' => 'Botol',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-OBH-01', 'buy_price' => 12000, 'sell_price' => 16000, 'stock' => 30, 'expired_months' => 6],
                ],
            ],
            [
                'category' => 'Obat Batuk & Flu',
                'name' => 'Bisolvon Sirup 60ml',
                'unit' => 'Botol',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-BSV-01', 'buy_price' => 22000, 'sell_price' => 28000, 'stock' => 22, 'expired_months' => 10],
                ],
            ],
            [
                'category' => 'Obat Batuk & Flu',
                'name' => 'Vicks Formula 44 Sirup 60ml',
                'unit' => 'Botol',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-VKS-01', 'buy_price' => 18000, 'sell_price' => 24000, 'stock' => 20, 'expired_months' => 12],
                ],
            ],

            // ================= VITAMIN & SUPLEMEN =================
            [
                'category' => 'Vitamin & Suplemen',
                'name' => 'Madurasa Jeruk Nipis',
                'unit' => 'Sachet',
                'min_stock' => 20,
                'batches' => [
                    ['batch_number' => 'BATCH-MDR-01', 'buy_price' => 1200, 'sell_price' => 2000, 'stock' => 100, 'expired_months' => 12],
                ],
            ],
            [
                'category' => 'Vitamin & Suplemen',
                'name' => 'Vitamin C 500mg (Enervon C)',
                'unit' => 'Strip',
                'min_stock' => 12,
                'batches' => [
                    ['batch_number' => 'BATCH-VTC-01', 'buy_price' => 5500, 'sell_price' => 8000, 'stock' => 55, 'expired_months' => 5],
                    ['batch_number' => 'BATCH-VTC-02', 'buy_price' => 5500, 'sell_price' => 8000, 'stock' => 70, 'expired_months' => 15],
                ],
            ],
            [
                'category' => 'Vitamin & Suplemen',
                'name' => 'Becom-Zet',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-BCZ-01', 'buy_price' => 4500, 'sell_price' => 6500, 'stock' => 45, 'expired_months' => 8],
                ],
            ],
            [
                'category' => 'Vitamin & Suplemen',
                'name' => 'Tolak Angin Cair',
                'unit' => 'Sachet',
                'min_stock' => 24,
                'batches' => [
                    ['batch_number' => 'BATCH-TLA-01', 'buy_price' => 2500, 'sell_price' => 4000, 'stock' => 120, 'expired_months' => 10],
                ],
            ],
            [
                'category' => 'Vitamin & Suplemen',
                'name' => 'Sangobion',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-SGB-01', 'buy_price' => 7000, 'sell_price' => 9500, 'stock' => 40, 'expired_months' => 14],
                ],
            ],
            [
                'category' => 'Vitamin & Suplemen',
                'name' => 'Vitamin B Complex',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-VTB-01', 'buy_price' => 3500, 'sell_price' => 5000, 'stock' => 50, 'expired_months' => 11],
                ],
            ],

            // ================= OBAT LUAR & SALEP =================
            [
                'category' => 'Obat Luar & Salep',
                'name' => 'Counterpain Cream 15gr',
                'unit' => 'Tube',
                'min_stock' => 5,
                'batches' => [
                    ['batch_number' => 'BATCH-CPN-01', 'buy_price' => 24000, 'sell_price' => 30000, 'stock' => 15, 'expired_months' => 18],
                ],
            ],
            [
                'category' => 'Obat Luar & Salep',
                'name' => 'Salep 88',
                'unit' => 'Tube',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-SL88-01', 'buy_price' => 8000, 'sell_price' => 11000, 'stock' => 28, 'expired_months' => 9],
                ],
            ],
            [
                'category' => 'Obat Luar & Salep',
                'name' => 'Betadine Solution 30ml',
                'unit' => 'Botol',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-BTD-01', 'buy_price' => 15000, 'sell_price' => 19000, 'stock' => 34, 'expired_months' => 20],
                ],
            ],
            [
                'category' => 'Obat Luar & Salep',
                'name' => 'Alkohol Swab 70%',
                'unit' => 'Box',
                'min_stock' => 5,
                'batches' => [
                    ['batch_number' => 'BATCH-ALC-01', 'buy_price' => 9000, 'sell_price' => 13000, 'stock' => 40, 'expired_months' => 24],
                ],
            ],
            [
                'category' => 'Obat Luar & Salep',
                'name' => 'Minyak Kayu Putih 60ml',
                'unit' => 'Botol',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-MKP-01', 'buy_price' => 18000, 'sell_price' => 23000, 'stock' => 32, 'expired_months' => 22],
                ],
            ],

            // ================= ANTIBIOTIK =================
            [
                'category' => 'Antibiotik',
                'name' => 'Amoxicillin 500mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-AMX-01', 'buy_price' => 9000, 'sell_price' => 14000, 'stock' => 50, 'expired_months' => 6],
                    ['batch_number' => 'BATCH-AMX-02', 'buy_price' => 9000, 'sell_price' => 14000, 'stock' => 65, 'expired_months' => 18],
                ],
            ],
            [
                'category' => 'Antibiotik',
                'name' => 'Azithromycin 500mg',
                'unit' => 'Strip',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-AZM-01', 'buy_price' => 28000, 'sell_price' => 36000, 'stock' => 24, 'expired_months' => 8],
                ],
            ],
            [
                'category' => 'Antibiotik',
                'name' => 'Ciprofloxacin 500mg',
                'unit' => 'Strip',
                'min_stock' => 6,
                'batches' => [
                    ['batch_number' => 'BATCH-CPF-01', 'buy_price' => 11000, 'sell_price' => 16000, 'stock' => 30, 'expired_months' => 12],
                ],
            ],

            // ================= ANTIHISTAMIN & ALERGI =================
            [
                'category' => 'Antihistamin & Alergi',
                'name' => 'Cetirizine 10mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-CTZ-01', 'buy_price' => 3500, 'sell_price' => 5500, 'stock' => 48, 'expired_months' => 7],
                    ['batch_number' => 'BATCH-CTZ-02', 'buy_price' => 3500, 'sell_price' => 5500, 'stock' => 60, 'expired_months' => 19],
                ],
            ],
            [
                'category' => 'Antihistamin & Alergi',
                'name' => 'Loratadine 10mg',
                'unit' => 'Strip',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-LRT-01', 'buy_price' => 4000, 'sell_price' => 6000, 'stock' => 36, 'expired_months' => 13],
                ],
            ],
            [
                'category' => 'Antihistamin & Alergi',
                'name' => 'CTM (Chlorpheniramine 4mg)',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-CTM-01', 'buy_price' => 2000, 'sell_price' => 3500, 'stock' => 55, 'expired_months' => 10],
                ],
            ],

            // ================= SALURAN CERNA & PENCERNAAN =================
            [
                'category' => 'Saluran Cerna & Pencernaan',
                'name' => 'Antasida Doen',
                'unit' => 'Strip',
                'min_stock' => 12,
                'batches' => [
                    ['batch_number' => 'BATCH-ANT-01', 'buy_price' => 2800, 'sell_price' => 4500, 'stock' => 60, 'expired_months' => 5],
                    ['batch_number' => 'BATCH-ANT-02', 'buy_price' => 2800, 'sell_price' => 4500, 'stock' => 75, 'expired_months' => 15],
                ],
            ],
            [
                'category' => 'Saluran Cerna & Pencernaan',
                'name' => 'Promag Tablet',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-PRM-01', 'buy_price' => 6500, 'sell_price' => 9000, 'stock' => 42, 'expired_months' => 8],
                ],
            ],
            [
                'category' => 'Saluran Cerna & Pencernaan',
                'name' => 'Omeprazole 20mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-OMP-01', 'buy_price' => 6500, 'sell_price' => 9500, 'stock' => 38, 'expired_months' => 11],
                ],
            ],
            [
                'category' => 'Saluran Cerna & Pencernaan',
                'name' => 'Domperidone 10mg',
                'unit' => 'Strip',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-DMP-01', 'buy_price' => 5500, 'sell_price' => 8000, 'stock' => 30, 'expired_months' => 9],
                ],
            ],
            [
                'category' => 'Saluran Cerna & Pencernaan',
                'name' => 'Lacto-B',
                'unit' => 'Sachet',
                'min_stock' => 20,
                'batches' => [
                    ['batch_number' => 'BATCH-LCB-01', 'buy_price' => 3500, 'sell_price' => 5000, 'stock' => 90, 'expired_months' => 10],
                ],
            ],

            // ================= CAIRAN & ELEKTROLIT =================
            [
                'category' => 'Cairan & Elektrolit',
                'name' => 'Oralit 200ml',
                'unit' => 'Sachet',
                'min_stock' => 24,
                'batches' => [
                    ['batch_number' => 'BATCH-ORL-01', 'buy_price' => 1500, 'sell_price' => 2500, 'stock' => 100, 'expired_months' => 12],
                ],
            ],

            // ================= OBAT KARDIOVASKULAR =================
            [
                'category' => 'Obat Kardiovaskular',
                'name' => 'Amlodipine 5mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-AML-01', 'buy_price' => 4500, 'sell_price' => 7000, 'stock' => 45, 'expired_months' => 13],
                ],
            ],
            [
                'category' => 'Obat Kardiovaskular',
                'name' => 'Captopril 25mg',
                'unit' => 'Strip',
                'min_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BATCH-CPT-01', 'buy_price' => 3800, 'sell_price' => 6000, 'stock' => 40, 'expired_months' => 16],
                ],
            ],
            [
                'category' => 'Obat Kardiovaskular',
                'name' => 'Simvastatin 20mg',
                'unit' => 'Strip',
                'min_stock' => 8,
                'batches' => [
                    ['batch_number' => 'BATCH-SMV-01', 'buy_price' => 5000, 'sell_price' => 7500, 'stock' => 35, 'expired_months' => 14],
                ],
            ],
        ];

        // -----------------------------------------------------------------
        // 3. Simpan produk & batch (idempotent, aman dijalankan berulang)
        // -----------------------------------------------------------------
        foreach ($catalog as $item) {
            $product = Product::firstOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $categories[$item['category']]->id,
                    'unit' => $item['unit'],
                    'min_stock' => $item['min_stock'],
                ]
            );

            foreach ($item['batches'] as $batch) {
                ProductBatch::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'batch_number' => $batch['batch_number'],
                    ],
                    [
                        'buy_price' => $batch['buy_price'],
                        'sell_price' => $batch['sell_price'],
                        'stock' => $batch['stock'],
                        'expired_date' => now()->addMonths($batch['expired_months'])->toDateString(),
                    ]
                );
            }
        }
    }
}

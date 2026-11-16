<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ProductBatch;
use App\Models\SalesDetail;
use App\Models\SalesTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesHistorySeeder extends Seeder
{
    /**
     * Membuat riwayat transaksi penjualan tiruan (dummy) yang tersebar
     * dalam 30 hari terakhir agar laporan penjualan tidak bernilai nol.
     *
     * Seeder ini hanya berjalan jika tabel sales_transactions masih kosong,
     * sehingga aman dipanggil berulang tanpa menggandakan data.
     */
    public function run(): void
    {
        if (SalesTransaction::query()->exists()) {
            if ($this->command) {
                $this->command->warn('Riwayat penjualan sudah tersedia — seeder riwayat dilewati.');
            }
            return;
        }

        // Seed acak yang deterministik agar hasil dummy konsisten.
        mt_srand(20261113);

        $userIds = User::query()
            ->whereIn('role', ['kasir', 'admin'])
            ->pluck('id')
            ->all();

        if (empty($userIds)) {
            $userIds = [User::query()->value('id') ?? 1];
        }

        $customerIds = Customer::query()->pluck('id')->all();

        $batches = ProductBatch::query()
            ->where('stock', '>', 0)
            ->where('expired_date', '>=', now()->toDateString())
            ->orderBy('expired_date')
            ->get();

        if ($batches->isEmpty()) {
            if ($this->command) {
                $this->command->warn('Tidak ada batch obat aktif — riwayat penjualan tidak dibuat.');
            }
            return;
        }

        // Rencana sebaran transaksi: [jumlah hari lalu, jumlah transaksi].
        $plan = [
            [0, 6],   // hari ini
            [1, 4],
            [2, 5],
            [3, 3],
            [4, 4],
            [5, 2],
            [6, 3],
            [7, 2],
            [8, 3],
            [10, 2],
            [12, 2],
            [14, 2],
            [17, 1],
            [20, 2],
            [24, 1],
            [28, 1],
        ];

        $paymentMethods = ['cash', 'qris', 'debit', 'cash', 'transfer', 'qris'];
        $sequence = 1;
        $created = 0;

        DB::transaction(function () use (
            $plan,
            $batches,
            $userIds,
            $customerIds,
            $paymentMethods,
            &$sequence,
            &$created
        ) {
            foreach ($plan as [$daysAgo, $transactionCount]) {
                for ($i = 0; $i < $transactionCount; $i++) {
                    // Pastikan transaksi "hari ini" tetap berada di hari yang sama
                    // meskipun seeder dijalankan dini hari.
                    if ($daysAgo === 0) {
                        $minutesNow = (int) now()->format('H') * 60 + (int) now()->format('i');
                        $createdAt = now()->subMinutes(min(10 + ($i * 25), max(0, $minutesNow - 1)));
                    } else {
                        $createdAt = now()
                            ->subDays($daysAgo)
                            ->setTime(8 + (($i * 3) % 10), ($i * 17) % 60, 0);
                    }

                    $itemCount = mt_rand(1, 3);
                    $chosenProductIds = [];
                    $details = [];
                    $totalAmount = 0.0;

                    foreach ($batches->shuffle() as $batch) {
                        if (count($details) >= $itemCount) {
                            break;
                        }

                        if (in_array($batch->product_id, $chosenProductIds, true)) {
                            continue;
                        }

                        if ((int) $batch->stock < 1) {
                            continue;
                        }

                        $quantity = mt_rand(1, min(3, (int) $batch->stock));
                        $price = (float) $batch->sell_price;
                        $subtotal = $quantity * $price;

                        $details[] = [
                            'product_batch_id' => $batch->id,
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $subtotal,
                        ];

                        $chosenProductIds[] = $batch->product_id;
                        $totalAmount += $subtotal;
                    }

                    if (empty($details)) {
                        continue;
                    }

                    $invoiceNumber = 'INV-' . $createdAt->format('Ymd') . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                    $paymentMethod = $paymentMethods[($sequence - 1) % count($paymentMethods)];
                    $customerId = (! empty($customerIds) && mt_rand(1, 100) <= 65)
                        ? $customerIds[array_rand($customerIds)]
                        : null;

                    $transaction = SalesTransaction::create([
                        'user_id' => $userIds[array_rand($userIds)],
                        'customer_id' => $customerId,
                        'invoice_number' => $invoiceNumber,
                        'total_amount' => $totalAmount,
                        'payment_method' => $paymentMethod,
                        'payment_status' => 'paid',
                    ]);

                    // Samakan waktu transaksi dengan tanggal riwayat yang diinginkan.
                    DB::table('sales_transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);

                    foreach ($details as $detail) {
                        SalesDetail::create([
                            'sales_transaction_id' => $transaction->id,
                            'product_batch_id' => $detail['product_batch_id'],
                            'quantity' => $detail['quantity'],
                            'price' => $detail['price'],
                            'subtotal' => $detail['subtotal'],
                        ]);

                        // Kurangi stok batch agar riwayat konsisten dengan alokasi FEFO.
                        $batch = $batches->firstWhere('id', $detail['product_batch_id']);
                        if ($batch) {
                            $batch->stock = (int) $batch->stock - (int) $detail['quantity'];
                            $batch->save();
                        }
                    }

                    $sequence++;
                    $created++;
                }
            }
        });

        if ($this->command) {
            $this->command->info("Riwayat penjualan dummy berhasil dibuat: {$created} transaksi.");
        }
    }
}

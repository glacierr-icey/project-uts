<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan dukungan pesanan online (portal pelanggan) pada tabel transaksi:
     * - Metode pembayaran COD
     * - Kanal transaksi (POS vs online)
     * - Informasi pengiriman / pengambilan di toko
     * - Catatan pesanan dan biaya kirim
     */
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            // Perluas enum pembayaran agar mendukung COD dari portal pelanggan.
            $table->enum('payment_method', ['cash', 'qris', 'debit', 'transfer', 'cod'])
                ->default('cash')
                ->change();

            // Pastikan skema status pembayaran tetap terkontrol setelah perubahan kolom.
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])
                ->default('paid')
                ->change();

            $table->string('order_channel', 20)->default('pos')->after('invoice_number');
            $table->decimal('delivery_fee', 15, 2)->default(0)->after('total_amount');
            $table->string('delivery_method', 20)->nullable()->after('payment_status');
            $table->string('recipient_name', 100)->nullable()->after('delivery_method');
            $table->string('recipient_phone', 30)->nullable()->after('recipient_name');
            $table->text('delivery_address')->nullable()->after('recipient_phone');
            $table->text('order_notes')->nullable()->after('delivery_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'order_channel',
                'delivery_fee',
                'delivery_method',
                'recipient_name',
                'recipient_phone',
                'delivery_address',
                'order_notes',
            ]);

            $table->enum('payment_method', ['cash', 'qris', 'debit', 'transfer'])
                ->default('cash')
                ->change();
        });
    }
};

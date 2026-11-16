<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('invoice_number', 50)->unique();
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_method', ['cash', 'qris', 'debit', 'transfer'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('paid');
            $table->timestamps();

            // Index pencarian riwayat invoice dan transaksi
            $table->index('invoice_number');
            $table->index(['created_at', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
    }
};


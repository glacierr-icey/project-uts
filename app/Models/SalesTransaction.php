<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'order_channel',
        'total_amount',
        'delivery_fee',
        'payment_method',
        'payment_status',
        'delivery_method',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'order_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    /**
     * Cek apakah transaksi berasal dari portal pelanggan (online).
     */
    public function isOnlineOrder(): bool
    {
        return $this->order_channel === 'online';
    }

    /**
     * Relasi ke Kasir / User yang memproses transaksi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Pelanggan (opsional/nullable jika pembeli walk-in non-member).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relasi ke rincian item obat yang dibeli (Master-Detail Pattern).
     */
    public function details(): HasMany
    {
        return $this->hasMany(SalesDetail::class, 'sales_transaction_id');
    }
}


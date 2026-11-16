<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'buy_price',
        'sell_price',
        'stock',
        'expired_date',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'stock' => 'integer',
        'expired_date' => 'date',
    ];

    /**
     * Relasi ke Produk Induk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relasi ke riwayat detail transaksi yang mengambil stok dari batch ini.
     */
    public function salesDetails(): HasMany
    {
        return $this->hasMany(SalesDetail::class, 'product_batch_id');
    }

    /**
     * Scope untuk menyaring batch yang masih tersedia dan belum kadaluarsa.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0)
                     ->where('expired_date', '>=', now()->toDateString());
    }

    /**
     * Scope untuk mengurutkan batch berdasarkan prinsip FEFO.
     */
    public function scopeFefo(Builder $query): Builder
    {
        return $query->orderBy('expired_date', 'asc');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'unit',
        'min_stock',
    ];

    protected $casts = [
        'min_stock' => 'integer',
    ];

    /**
     * Relasi ke Kategori Obat.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke seluruh batch obat yang dimiliki produk ini.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'product_id');
    }

    /**
     * Relasi khusus batch aktif yang masih memiliki stok dan belum kadaluarsa,
     * diurutkan berdasarkan metode FEFO (First Expired, First Out).
     */
    public function activeFefoBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'product_id')
            ->where('stock', '>', 0)
            ->where('expired_date', '>=', now()->toDateString())
            ->orderBy('expired_date', 'asc');
    }

    /**
     * Accessor untuk menghitung total stok riil dari seluruh batch aktif.
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->batches()
            ->where('expired_date', '>=', now()->toDateString())
            ->sum('stock');
    }
}


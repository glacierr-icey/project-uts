<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    /**
     * Relasi ke seluruh riwayat transaksi penjualan yang dilakukan customer ini.
     */
    public function salesTransactions(): HasMany
    {
        return $this->hasMany(SalesTransaction::class, 'customer_id');
    }
}


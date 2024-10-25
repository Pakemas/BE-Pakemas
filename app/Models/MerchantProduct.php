<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'merchant_id',
        'product_id',
        'stock',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

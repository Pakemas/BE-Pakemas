<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'total_amount',
        'qr_code',
        'purchase_date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi dengan Purchase_Item
    public function items()
    {
        return $this->hasMany(PurchaseItems::class);
    }
}

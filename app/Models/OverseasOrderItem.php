<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverseasOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'overseas_order_id',
        'product_id',
        'variant',
        'quantity',
        'unit_price',
        'total_price'
    ];

    public function overseasOrder()
    {
        return $this->belongsTo(OverseasOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

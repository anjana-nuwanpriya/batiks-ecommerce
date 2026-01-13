<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant',
        'cost',
        'quantity',
        'unit_price',
        'coupon_discount',
        'total_price',
        'weight',
        'weight_cost',
    ];

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

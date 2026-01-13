<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashDealItem extends Model
{
    use HasFactory;

    protected $table = 'flash_deals_item';

    protected $fillable = [
        'flash_deal_id',
        'product_id',
        'discount',
        'discount_type',
        'quantity',
        'status',
    ];

    public function flashDeal()
    {
        return $this->belongsTo(FlashDeal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

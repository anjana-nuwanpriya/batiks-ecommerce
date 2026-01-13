<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'inqiry_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inqiry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductStock::class, 'variant_id');
    }
}

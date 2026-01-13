<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverseasOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'shipping_cost',
        'status',
        'shipping_address',
        'notes'
    ];

    protected $casts = [
        'shipping_address' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OverseasOrderItem::class);
    }

    public function getTotalWithShippingAttribute()
    {
        return $this->total_amount + $this->shipping_cost;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'OV-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
        });

        static::updating(function ($order) {
            if ($order->isDirty('status') && $order->status === 'shipped') {
                // Reduce stock when status changes to shipped
                foreach ($order->items as $item) {
                    $productStock = ProductStock::where('product_id', $item->product_id)
                        ->where('variant', $item->variant)
                        ->first();

                    if ($productStock && $productStock->quantity >= $item->quantity) {
                        $productStock->decrement('quantity', $item->quantity);
                    }
                }
            }
        });
    }
}

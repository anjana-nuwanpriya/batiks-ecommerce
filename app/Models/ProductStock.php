<?php

namespace App\Models;

use App\Traits\LogsAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductStock extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsAdminActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'qty',
        'purchase_price',
        'selling_price',
        'weight',
        'is_standard',
    ];

    /**
     * Get the product that the stock belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getThumbnailAttribute()
    {
        return $this->getFirstMediaUrl('product_stock_image');
    }

    /**
     * Override the activity identifier for better logging.
     */
    protected function getActivityIdentifier(): ?string
    {
        $productName = $this->product->name ?? 'Unknown Product';
        $variant = $this->variant ? " ({$this->variant})" : '';
        return "{$productName}{$variant} (SKU: {$this->sku})";
    }

    /**
     * Override to log only specific fields for stock management.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['qty', 'selling_price', 'purchase_price'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                $modelName = class_basename($this);
                $identifier = $this->getActivityIdentifier();
                return ucfirst($eventName) . " {$modelName}" . ($identifier ? " ({$identifier})" : '');
            })
            ->useLogName('product_stock_management');
    }
}

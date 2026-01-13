<?php

namespace App\Models;

use App\Traits\LogsAdminActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsAdminActivity;

    protected $fillable = [
        'name',
        'sinhala_name',
        'short_description',
        'description',
        'how_to_use',
        'price',
        'special_price',
        'selling_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'meta_title',
        'meta_description',
        'is_free_shipping',
        'weight',
        'is_active',
        'allow_inquiries',
        'is_featured',
        'track_stock',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * The categories that belong to the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function relatedProducts()
    {
        return $this->hasMany(RelatedProduct::class);
    }

    /**
     * Get the stocks for the product.
     */
    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Get the thumbnail attribute.
     */
    public function getThumbnailAttribute()
    {
        return $this->getFirstMediaUrl('product_thumbnail');
    }

    /**
     * Product Reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the wishlist for the product.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Check if the product is in the wishlist.
     */
    public function isWishlist()
    {
        if (Auth::check()) {
            $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $this->id)->exists();
            return $wishlist;
        }
        return false;
    }

    /**
     * Get the product's price after applying any discounts.
     *
     * @param int $variantId
     * @param int $quantity
     * @return float
     */
    public function cartPrice($variantId, $quantity = 1)
    {
        $today = Carbon::today();

        $variant = $this->stocks()->find($variantId);
        if (!$variant) {
            return 0;
        }

        $basePrice = $variant->selling_price;
        $discountedPrice = $basePrice;

        $flashDealItem = $this->getActiveFlashDealItem();
        if ($flashDealItem) {
            if ($flashDealItem->discount_type === 'fixed') {
                $discountedPrice -= $flashDealItem->discount;
            } else {
                $discountedPrice -= ($basePrice * $flashDealItem->discount / 100);
            }
        }elseif (
            $this->special_price > 0 &&
            $this->special_price_start <= $today &&
            $this->special_price_end >= $today
        ) {
            if ($this->special_price_type === 'fixed') {
                $discountedPrice -= $this->special_price;
            } else {
                $discountedPrice -= ($basePrice * $this->special_price / 100);
            }
        }

        $finalPrice = max(0, $discountedPrice);
        return $finalPrice * $quantity;
    }


    public function orderInfos()
    {
        return $this->hasMany(OrderInfo::class);
    }


    public function getActiveFlashDealItem()
    {
        $today = Carbon::today();

        return FlashDealItem::where('product_id', $this->id)
            ->where('status', true)
            ->whereHas('flashDeal', function ($query) use ($today) {
                $query->where('status', true)
                    ->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
            })
            ->with('flashDeal') // optional, only if you need deal title/date
            ->first();
    }

    public function flash_deals_item()
    {
        return $this->hasMany(FlashDealItem::class);
    }



    /**
     * Get available stock for a variant.
     */
    public function getAvailableStock($variantId = null)
    {
        if (!$this->track_stock) {
            return 999; // Unlimited for non-tracked products
        }

        if ($variantId) {
            $variant = $this->stocks()->find($variantId);
            return $variant ? $variant->qty : 0;
        }

        return $this->stocks()->sum('qty');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Add global scope for ordering
        static::addGlobalScope('ordered', function ($builder) {
            $builder->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        });

        static::creating(function ($product) {
            $product->slug = str()->slug($product->name);

            // Set sort_order for new products
            $maxOrder = static::withoutGlobalScope('ordered')->max('sort_order') ?? 0;
            $product->sort_order = $maxOrder + 1;

            // Check if slug exists and append number if needed
            $count = 1;
            $originalSlug = $product->slug;

            while (static::where('slug', $product->slug)->exists()) {
                $product->slug = $originalSlug . '-' . $count++;
            }
        });

        static::updating(function ($product) {
            // Only update slug if name has changed
            if ($product->isDirty('name')) {
                $product->slug = str()->slug($product->name);

                $count = 1;
                $originalSlug = $product->slug;

                while (static::where('slug', $product->slug)
                        ->where('id', '!=', $product->id)
                        ->exists()) {
                    $product->slug = $originalSlug . '-' . $count++;
                }
            }
        });

        static::deleting(function ($product) {
            // $product->relatedProducts()->delete();
            // RelatedProduct::where('related_product_id', $product->id)->delete(); // Delete where this product is listed as a related product to others
            $product->wishlist()->delete();
            $product->orderInfos()->delete();
            $product->stocks()->delete();
        });
    }
}

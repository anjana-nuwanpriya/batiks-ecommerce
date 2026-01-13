<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'link_text',
        'link',
        'apply_shade',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'apply_shade' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Default ordering by sort_order
     */
    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($builder) {
            $builder->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        });
    }

    public function getThumbnailAttribute()
    {
        return $this->getFirstMediaUrl('banner_image');
    }

    public function getMobileBannerAttribute()
    {
        return $this->getFirstMediaUrl('mobile_banner_image');
    }
}

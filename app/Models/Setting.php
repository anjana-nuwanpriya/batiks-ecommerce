<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['key', 'value', 'status'];



    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($setting) {
            $setting->key = str_replace(' ', '_', strtolower($setting->key));
        });

        static::updating(function ($setting) {
            $setting->key = str_replace(' ', '_', strtolower($setting->key));
        });
    }
}

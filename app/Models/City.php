<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'name_en',
        'name_si',
        'name_ta',
        'sub_name_en',
        'sub_name_si',
        'sub_name_ta',
        'postcode',
        'latitude',
        'longitude'
    ];

    /**
     * Search cities by name with optimized query
     */
    public static function search($query, $limit = 15)
    {
        $query = trim($query);

        if (empty($query)) {
            return collect([]);
        }

        // Cache key for this search
        $cacheKey = 'city_search_' . md5(strtolower($query)) . '_' . $limit;

        return cache()->remember($cacheKey, 300, function () use ($query, $limit) {
            // Use a more efficient query with proper indexing
            return self::select(['id', 'name_en', 'name_si', 'name_ta', 'sub_name_en', 'sub_name_si', 'sub_name_ta', 'postcode'])
                ->where(function ($q) use ($query) {
                    $q->where('name_en', 'LIKE', "{$query}%")
                      ->orWhere('name_si', 'LIKE', "{$query}%")
                      ->orWhere('name_ta', 'LIKE', "{$query}%")
                      ->orWhere('sub_name_en', 'LIKE', "{$query}%")
                      ->orWhere('sub_name_si', 'LIKE', "{$query}%")
                      ->orWhere('sub_name_ta', 'LIKE', "{$query}%");
                })
                ->orderByRaw("
                    CASE
                        WHEN name_en LIKE '{$query}%' THEN 1
                        WHEN name_si LIKE '{$query}%' THEN 2
                        WHEN name_ta LIKE '{$query}%' THEN 3
                        WHEN sub_name_en LIKE '{$query}%' THEN 4
                        WHEN sub_name_si LIKE '{$query}%' THEN 5
                        WHEN sub_name_ta LIKE '{$query}%' THEN 6
                        ELSE 7
                    END
                ")
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Fast search for autocomplete (starts with query only)
     */
    public static function autocompleteSearch($query, $limit = 10)
    {
        $query = trim($query);

        if (empty($query) || strlen($query) < 2) {
            return collect([]);
        }

        $cacheKey = 'city_autocomplete_' . md5(strtolower($query)) . '_' . $limit;

        return cache()->remember($cacheKey, 600, function () use ($query, $limit) {
            return self::select(['name_en', 'name_si', 'name_ta', 'sub_name_en', 'sub_name_si', 'sub_name_ta', 'postcode'])
                ->where('name_en', 'LIKE', "{$query}%")
                ->orderBy('name_en')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get display name for the city
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->name_en ?: $this->name_si ?: $this->name_ta;
        $subName = $this->sub_name_en ?: $this->sub_name_si ?: $this->sub_name_ta;

        if ($subName && $subName !== $name) {
            return $name . ' - ' . $subName;
        }

        return $name;
    }
}

<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CityService
{
    /**
     * Search cities with caching and performance optimization
     */
    public static function searchCities(string $query, int $limit = 10): array
    {
        $query = trim($query);

        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        try {
            $cities = City::autocompleteSearch($query, $limit);

            return $cities->map(function ($city) {
                $displayName = $city->name_en ?: $city->name_si ?: $city->name_ta ?: 'Unknown City';
                $subName = $city->sub_name_en ?: $city->sub_name_si ?: $city->sub_name_ta;

                if ($subName && $subName !== $displayName) {
                    $displayName .= ' - ' . $subName;
                }

                return [
                    'value' => $city->name_en ?: $city->name_si ?: $city->name_ta,
                    'label' => $displayName . ($city->postcode ? ' (' . $city->postcode . ')' : ''),
                    'postcode' => $city->postcode,
                    'display_name' => $displayName
                ];
            })->values()->toArray();

        } catch (\Exception $e) {
            Log::error('City search error: ' . $e->getMessage(), [
                'query' => $query,
                'limit' => $limit
            ]);
            return [];
        }
    }

    /**
     * Get popular cities for quick access
     */
    public static function getPopularCities(int $limit = 20): array
    {
        return Cache::remember('popular_cities', 3600, function () use ($limit) {
            return City::select(['name_en', 'name_si', 'name_ta', 'sub_name_en', 'sub_name_si', 'sub_name_ta', 'postcode'])
                ->whereIn('name_en', [
                    'Colombo', 'Kandy', 'Galle', 'Jaffna', 'Negombo', 'Anuradhapura',
                    'Batticaloa', 'Matara', 'Kurunegala', 'Ratnapura', 'Badulla',
                    'Kalutara', 'Gampaha', 'Kegalle', 'Hambantota', 'Trincomalee',
                    'Chilaw', 'Puttalam', 'Vavuniya', 'Mannar'
                ])
                ->orderBy('name_en')
                ->limit($limit)
                ->get()
                ->map(function ($city) {
                    $displayName = $city->name_en ?: $city->name_si ?: $city->name_ta;
                    $subName = $city->sub_name_en ?: $city->sub_name_si ?: $city->sub_name_ta;

                    if ($subName && $subName !== $displayName) {
                        $displayName .= ' - ' . $subName;
                    }

                    return [
                        'value' => $city->name_en ?: $city->name_si ?: $city->name_ta,
                        'label' => $displayName . ($city->postcode ? ' (' . $city->postcode . ')' : ''),
                        'postcode' => $city->postcode
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Clear city cache
     */
    public static function clearCache(): void
    {
        Cache::forget('popular_cities');

        // Clear search cache patterns
        $cacheKeys = Cache::getRedis()->keys('*city_search_*');
        if (!empty($cacheKeys)) {
            Cache::getRedis()->del($cacheKeys);
        }

        $autocompleteKeys = Cache::getRedis()->keys('*city_autocomplete_*');
        if (!empty($autocompleteKeys)) {
            Cache::getRedis()->del($autocompleteKeys);
        }
    }
}

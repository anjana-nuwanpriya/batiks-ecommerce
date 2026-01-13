<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;

class WarmCityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up the city search cache with common searches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Warming up city cache...');

        // Common city prefixes in Sri Lanka
        $commonPrefixes = [
            'Co',
            'Ka',
            'Ga',
            'Ma',
            'Ba',
            'An',
            'De',
            'Ha',
            'Ku',
            'Ne',
            'Ra',
            'Wa',
            'Pa',
            'Ta',
            'Sa',
            'La',
            'Ja',
            'Ni',
            'Ke',
            'Ar'
        ];

        $warmedCount = 0;

        foreach ($commonPrefixes as $prefix) {
            try {
                City::autocompleteSearch($prefix, 10);
                $warmedCount++;
                $this->line("Warmed cache for prefix: {$prefix}");
            } catch (\Exception $e) {
                $this->error("Failed to warm cache for prefix {$prefix}: " . $e->getMessage());
            }
        }

        // Also warm up some full city names
        $popularCities = [
            'Colombo',
            'Kandy',
            'Galle',
            'Jaffna',
            'Negombo',
            'Anuradhapura',
            'Batticaloa',
            'Matara',
            'Kurunegala',
            'Ratnapura',
            'Badulla',
            'Kalutara',
            'Gampaha',
            'Kegalle',
            'Hambantota',
            'Trincomalee'
        ];

        foreach ($popularCities as $city) {
            try {
                City::autocompleteSearch($city, 10);
                $warmedCount++;
                $this->line("Warmed cache for city: {$city}");
            } catch (\Exception $e) {
                $this->error("Failed to warm cache for city {$city}: " . $e->getMessage());
            }
        }

        $this->info("City cache warming completed! Warmed {$warmedCount} cache entries.");

        return Command::SUCCESS;
    }
}

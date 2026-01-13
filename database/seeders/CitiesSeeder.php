<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\City;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for faster insertion
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table to start fresh
        City::truncate();

        // Include the cities data from external file
        $cities = include(database_path('seeders/data/cities_data.php'));

        // Insert cities in chunks for better performance
        $chunkSize = 100;
        $totalCities = count($cities);

        $this->command->info("Seeding {$totalCities} cities...");

        for ($i = 0; $i < $totalCities; $i += $chunkSize) {
            $chunk = array_slice($cities, $i, $chunkSize);
            $insertData = [];

            foreach ($chunk as $city) {
                $insertData[] = [
                    'id' => $city[0],
                    'district_id' => $city[1],
                    'name_en' => $city[2],
                    'name_si' => $city[3],
                    'name_ta' => $city[4],
                    'sub_name_en' => $city[5],
                    'sub_name_si' => $city[6],
                    'sub_name_ta' => $city[7],
                    'postcode' => $city[8],
                    'latitude' => $city[9],
                    'longitude' => $city[10],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('cities')->insert($insertData);

            $this->command->info("Inserted " . ($i + count($chunk)) . " cities...");
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Cities seeded successfully!');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Drop the existing name column if it exists
            if (Schema::hasColumn('cities', 'name')) {
                $table->dropColumn('name');
            }

            // Add new columns to match the SQL structure
            $table->unsignedBigInteger('district_id')->after('id');
            $table->string('name_en', 45)->nullable()->after('district_id');
            $table->string('name_si', 45)->nullable()->after('name_en');
            $table->string('name_ta', 45)->nullable()->after('name_si');
            $table->string('sub_name_en', 45)->nullable()->after('name_ta');
            $table->string('sub_name_si', 45)->nullable()->after('sub_name_en');
            $table->string('sub_name_ta', 45)->nullable()->after('sub_name_si');
            $table->string('postcode', 15)->nullable()->after('sub_name_ta');
            $table->double('latitude')->nullable()->after('postcode');
            $table->double('longitude')->nullable()->after('latitude');

            // Add index for district_id
            $table->index('district_id', 'fk_cities_districts1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Drop the new columns
            $table->dropIndex('fk_cities_districts1_idx');
            $table->dropColumn([
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
            ]);

            // Add back the original name column
            $table->string('name')->after('id');
        });
    }
};

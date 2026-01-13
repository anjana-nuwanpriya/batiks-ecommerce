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
            // Add indexes for better search performance
            $table->index(['name_en'], 'cities_name_en_index');
            $table->index(['name_si'], 'cities_name_si_index');
            $table->index(['name_ta'], 'cities_name_ta_index');
            $table->index(['sub_name_en'], 'cities_sub_name_en_index');
            $table->index(['sub_name_si'], 'cities_sub_name_si_index');
            $table->index(['sub_name_ta'], 'cities_sub_name_ta_index');
            $table->index(['postcode'], 'cities_postcode_index');

            // Composite index for common search patterns
            $table->index(['name_en', 'sub_name_en'], 'cities_name_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex('cities_name_en_index');
            $table->dropIndex('cities_name_si_index');
            $table->dropIndex('cities_name_ta_index');
            $table->dropIndex('cities_sub_name_en_index');
            $table->dropIndex('cities_sub_name_si_index');
            $table->dropIndex('cities_sub_name_ta_index');
            $table->dropIndex('cities_postcode_index');
            $table->dropIndex('cities_name_composite_index');
        });
    }
};

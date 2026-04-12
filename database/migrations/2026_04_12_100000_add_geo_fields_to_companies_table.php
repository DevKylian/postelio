<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('city_lat', 10, 7)->nullable()->after('city_context');
            $table->decimal('city_lng', 10, 7)->nullable()->after('city_lat');
            // JSON array of nearby commune names (within ~30km), fetched from geo.api.gouv.fr
            $table->json('nearby_cities')->nullable()->after('city_lng');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['city_lat', 'city_lng', 'nearby_cities']);
        });
    }
};

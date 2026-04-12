<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('postal_code', 10)->nullable()->after('city');
            $table->string('city_context', 255)->nullable()->after('postal_code'); // e.g. "22, Côtes-d'Armor, Bretagne"
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'city_context']);
        });
    }
};

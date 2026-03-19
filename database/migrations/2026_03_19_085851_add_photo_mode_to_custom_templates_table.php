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
        Schema::table('custom_templates', function (Blueprint $table) {
            // none | single | carousel
            $table->string('photo_mode')->default('none')->after('visual_style');
        });
    }

    public function down(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->dropColumn('photo_mode');
        });
    }
};

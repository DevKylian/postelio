<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instagram_prospects', function (Blueprint $table) {
            $table->tinyInteger('script_version')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('instagram_prospects', function (Blueprint $table) {
            $table->dropColumn('script_version');
        });
    }
};

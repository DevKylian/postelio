<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instagram_prospects', function (Blueprint $table) {
            $table->string('current_node_id')->nullable()->after('script_version');
        });
    }

    public function down(): void
    {
        Schema::table('instagram_prospects', function (Blueprint $table) {
            $table->dropColumn('current_node_id');
        });
    }
};

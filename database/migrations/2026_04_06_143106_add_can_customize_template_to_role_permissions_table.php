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
        Schema::table('role_permissions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->boolean('can_customize_template')->default(true)->after('can_view_statistics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_permissions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('can_customize_template');
        });
    }
};

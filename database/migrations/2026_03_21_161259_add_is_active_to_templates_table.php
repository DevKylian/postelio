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
        // Add is_active to custom_templates
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('visual_style');
        });

        // Create native_template_settings
        Schema::create('native_template_settings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::dropIfExists('native_template_settings');
    }
};

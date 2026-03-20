<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('platforms')->nullable()->after('template_options');
            $table->string('facebook_image_path')->nullable()->after('image_path');
            $table->json('facebook_slides')->nullable()->after('slides');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['platforms', 'facebook_image_path', 'facebook_slides']);
        });
    }
};

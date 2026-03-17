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
        // slides and extra_images already added in create_posts_table migration
        if (!Schema::hasColumn('posts', 'slides')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('slides')->nullable();
                $table->json('extra_images')->nullable();
            });
        }
    }

    public function down(): void
    {
    }
};

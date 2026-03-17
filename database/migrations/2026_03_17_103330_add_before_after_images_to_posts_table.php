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
        // image_before_path and image_after_path already added in create_posts_table migration
        if (!Schema::hasColumn('posts', 'image_before_path')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('image_before_path')->nullable();
                $table->string('image_after_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        //
    }
};

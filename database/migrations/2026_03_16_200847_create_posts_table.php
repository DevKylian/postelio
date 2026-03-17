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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            $table->text('transcription')->nullable();
            $table->text('refined_content')->nullable();
            $table->string('template')->default('review');
            $table->string('status')->default('pending'); // pending|processing|completed|failed|review_pending
            $table->string('image_before_path')->nullable();
            $table->string('image_after_path')->nullable();
            $table->json('slides')->nullable();
            $table->json('extra_images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

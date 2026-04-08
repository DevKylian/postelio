<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_guide_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('free_guide_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->timestamps();

            $table->unique(['free_guide_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_guide_leads');
    }
};

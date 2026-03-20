<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('facebook_access_token')->nullable()->after('instagram_token_expires_at');
            $table->string('facebook_page_id')->nullable()->after('facebook_access_token');
            $table->string('facebook_page_name')->nullable()->after('facebook_page_id');
            $table->string('facebook_page_picture_url', 1024)->nullable()->after('facebook_page_name');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_access_token',
                'facebook_page_id',
                'facebook_page_name',
                'facebook_page_picture_url',
            ]);
        });
    }
};

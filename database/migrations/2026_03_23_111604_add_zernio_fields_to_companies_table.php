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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('zernio_profile_id')->nullable()->after('weekly_posts_goal');
            $table->string('zernio_facebook_account_id')->nullable()->after('zernio_profile_id');
            $table->string('zernio_instagram_account_id')->nullable()->after('zernio_facebook_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['zernio_profile_id', 'zernio_facebook_account_id', 'zernio_instagram_account_id']);
        });
    }
};

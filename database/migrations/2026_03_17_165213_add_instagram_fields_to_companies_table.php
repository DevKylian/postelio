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
            $table->text('instagram_access_token')->nullable()->after('logo_path');
            $table->string('instagram_business_account_id')->nullable()->after('instagram_access_token');
            $table->string('instagram_username')->nullable()->after('instagram_business_account_id');
            $table->timestamp('instagram_token_expires_at')->nullable()->after('instagram_username');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_access_token',
                'instagram_business_account_id',
                'instagram_username',
                'instagram_token_expires_at',
            ]);
        });
    }
};

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
        Schema::create('role_permissions', function (Blueprint $role) {
            $role->id();
            $role->string('role_key')->unique(); // free, tester, basic, pro, business, admin
            
            // Quotas
            $role->integer('monthly_quota')->default(3);
            
            // Capabilities (Boolean toggles)
            $role->boolean('can_generate_posts')->default(true);
            $role->boolean('can_connect_social')->default(false);
            $role->boolean('has_premium_templates')->default(false);
            $role->boolean('can_view_calendar')->default(false);
            $role->boolean('can_view_template_previews')->default(false);
            $role->boolean('can_view_statistics')->default(false);
            
            $role->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};

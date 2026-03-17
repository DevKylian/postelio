<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // status column already includes review_pending (handled in create_posts_table migration)
    }

    public function down(): void
    {
        //
    }
};

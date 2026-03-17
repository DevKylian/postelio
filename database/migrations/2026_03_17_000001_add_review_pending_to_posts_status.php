<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On a fresh install create_posts_table already uses string(status).
        // On an existing MySQL DB the column is ENUM — we alter it.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('pending','processing','completed','failed','review_pending') NOT NULL DEFAULT 'pending'");
        }
        // SQLite stores enums as varchar — no-op needed.
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('pending','processing','completed','failed') NOT NULL DEFAULT 'pending'");
        }
    }
};

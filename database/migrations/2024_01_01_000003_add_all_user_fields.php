<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add username column
        if (!Schema::hasColumn('users', 'username')) {
            DB::statement('ALTER TABLE users ADD COLUMN username VARCHAR(255) NULL');
            DB::statement('CREATE UNIQUE INDEX users_username_unique ON users (username)');
        }

        // Add phone column
        if (!Schema::hasColumn('users', 'phone')) {
            DB::statement('ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL');
        }

        // Add address column
        if (!Schema::hasColumn('users', 'address')) {
            DB::statement('ALTER TABLE users ADD COLUMN address TEXT NULL');
        }

        // Add role column (SQLite enum support is limited, use VARCHAR)
        if (!Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(255) NOT NULL DEFAULT 'user'");
        }

        // Add is_active column
        if (!Schema::hasColumn('users', 'is_active')) {
            DB::statement('ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1');
        }

        // Add last_login_at column
        if (!Schema::hasColumn('users', 'last_login_at')) {
            DB::statement('ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL');
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'address', 'role', 'is_active', 'last_login_at']);
        });
    }
};

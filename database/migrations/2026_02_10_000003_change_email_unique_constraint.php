<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite directly uses DROP INDEX outside of ALTER TABLE
        \Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS allowed_emails_email_unique');
        
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->unique(['email', 'platform_id'], 'allowed_emails_email_platform_unique');
        });
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS allowed_emails_email_platform_unique');
        
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->unique('email', 'allowed_emails_email_unique');
        });
    }
};

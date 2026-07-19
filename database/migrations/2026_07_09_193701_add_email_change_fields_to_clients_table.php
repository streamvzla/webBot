<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'pending_email')) {
                $table->string('pending_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('clients', 'email_change_token')) {
                $table->string('email_change_token')->nullable()->after('pending_email');
            }
            if (!Schema::hasColumn('clients', 'email_change_token_expires_at')) {
                $table->timestamp('email_change_token_expires_at')->nullable()->after('email_change_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['pending_email', 'email_change_token', 'email_change_token_expires_at']);
        });
    }
};

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
        Schema::table('queries', function (Blueprint $table) {
            // Only add client_id if it doesn't exist
            if (!Schema::hasColumn('queries', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('user_id')->constrained('clients')->nullOnDelete();
            }

            // Only add email_account_id if it doesn't exist
            if (!Schema::hasColumn('queries', 'email_account_id')) {
                $table->foreignId('email_account_id')->nullable()->after('client_id')->constrained('email_accounts')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            if (Schema::hasColumn('queries', 'client_id')) {
                $table->dropForeign(['client_id']);
            }
            if (Schema::hasColumn('queries', 'email_account_id')) {
                $table->dropForeign(['email_account_id']);
            }
            $table->dropColumn(['client_id', 'email_account_id']);
        });
    }
};

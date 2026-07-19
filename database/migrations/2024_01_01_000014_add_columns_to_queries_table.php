<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            // Add email_account_id column if it doesn't exist
            if (!Schema::hasColumn('queries', 'email_account_id')) {
                $table->foreignId('email_account_id')->nullable()->after('client_id')->constrained()->onDelete('set null');
            }

            // Add result column if it doesn't exist (to replace status)
            if (!Schema::hasColumn('queries', 'result')) {
                $table->string('result')->default('pending')->after('email')->comment('success, pending, no_code, error');
            }

            // Add code_status column if it doesn't exist
            if (!Schema::hasColumn('queries', 'code_status')) {
                $table->string('code_status')->nullable()->after('result')->comment('found, not_found, error');
            }

            // Add code_hash column if it doesn't exist (for security - hash of the code, never store full code)
            if (!Schema::hasColumn('queries', 'code_hash')) {
                $table->string('code_hash', 64)->nullable()->after('code_status');
            }

            // Add response_time column if it doesn't exist
            if (!Schema::hasColumn('queries', 'response_time')) {
                $table->decimal('response_time', 8, 3)->nullable()->after('code_hash')->comment('Response time in seconds');
            }

            // Drop old columns if they exist
            if (Schema::hasColumn('queries', 'code_found')) {
                $table->dropColumn('code_found');
            }

            if (Schema::hasColumn('queries', 'status')) {
                try {
                    $table->dropIndex('queries_status_created_at_index');
                } catch (\Exception $e) {
                    // Ignore if index doesn't exist
                }
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('queries', 'error_message')) {
                $table->dropColumn('error_message');
            }

            if (Schema::hasColumn('queries', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            // Drop new columns
            if (Schema::hasColumn('queries', 'email_account_id')) {
                $table->dropForeign(['email_account_id']);
                $table->dropColumn('email_account_id');
            }

            if (Schema::hasColumn('queries', 'result')) {
                $table->dropColumn('result');
            }

            if (Schema::hasColumn('queries', 'code_status')) {
                $table->dropColumn('code_status');
            }

            if (Schema::hasColumn('queries', 'code_hash')) {
                $table->dropColumn('code_hash');
            }

            if (Schema::hasColumn('queries', 'response_time')) {
                $table->dropColumn('response_time');
            }

            // Restore old columns
            $table->text('code_found')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->foreignId('email_account_id')->nullable()->after('email')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->dropForeign(['email_account_id']);
            $table->dropColumn('email_account_id');
        });
    }
};

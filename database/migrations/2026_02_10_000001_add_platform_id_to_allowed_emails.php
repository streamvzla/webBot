<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->foreignId('platform_id')->nullable()->after('email_account_id')->constrained('platforms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropColumn(['platform_id']);
        });
    }
};

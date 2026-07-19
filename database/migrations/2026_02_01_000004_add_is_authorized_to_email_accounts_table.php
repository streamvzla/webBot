<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_accounts', function (Blueprint $table) {
            $table->boolean('is_authorized')->default(false)->after('is_active');
            $table->text('authorization_notes')->nullable()->after('is_authorized');
        });
    }

    public function down(): void
    {
        Schema::table('email_accounts', function (Blueprint $table) {
            $table->dropColumn(['is_authorized', 'authorization_notes']);
        });
    }
};

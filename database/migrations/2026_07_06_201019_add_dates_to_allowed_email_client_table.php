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
        Schema::table('allowed_email_client', function (Blueprint $table) {
            $table->date('assigned_at')->nullable()->after('client_id');
            $table->date('expires_at')->nullable()->after('assigned_at');
            $table->decimal('price', 8, 2)->default(0)->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowed_email_client', function (Blueprint $table) {
            $table->dropColumn(['assigned_at', 'expires_at', 'price']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('is_active')->comment('Permitir consultas públicas sin autenticación');
        });
    }

    public function down(): void
    {
        Schema::table('allowed_emails', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};

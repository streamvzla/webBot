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
        Schema::create('client_allowed_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('allowed_email_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['client_id', 'allowed_email_id']);
        });

        // Agregar campo access_mode a clients si no existe
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'access_mode')) {
                $table->enum('access_mode', ['all', 'selective'])->default('all')->after('max_queries_per_day');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_allowed_email');

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'access_mode')) {
                $table->dropColumn('access_mode');
            }
        });
    }
};

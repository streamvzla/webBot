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
            // Make user_id nullable since queries can be made by clients (using client_id) instead of users
            if (Schema::hasColumn('queries', 'user_id')) {
                $table->foreignId('user_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            if (Schema::hasColumn('queries', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade')->change();
            }
        });
    }
};

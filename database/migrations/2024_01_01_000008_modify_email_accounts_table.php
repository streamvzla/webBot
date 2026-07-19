<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make user_id nullable
        Schema::table('email_accounts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        // Create pivot table for many-to-many relationship
        Schema::create('email_account_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['email_account_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_account_users');

        Schema::table('email_accounts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->change();
        });
    }
};

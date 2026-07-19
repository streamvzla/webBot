<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allowed_email_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('allowed_email_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['client_id', 'allowed_email_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allowed_email_client');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('imap_host');
            $table->integer('imap_port')->default(993);
            $table->string('imap_encryption')->default('ssl');
            $table->string('username');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['email', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_accounts');
    }
};

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
        Schema::create('extracted_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('recipient_email')->index();
            $table->string('code')->index();
            $table->text('body')->nullable();
            $table->string('uid')->nullable()->comment('IMAP UID del correo procesado para no repetirlo');
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracted_codes');
    }
};

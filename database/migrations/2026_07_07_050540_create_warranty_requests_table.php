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
        Schema::create('warranty_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('old_email');
            $table->string('new_email')->nullable();
            $table->foreignId('platform_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['replacement', 'minor_issue'])->default('replacement');
            $table->string('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'resolved'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_requests');
    }
};

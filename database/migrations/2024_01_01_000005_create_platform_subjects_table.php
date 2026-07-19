<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('pattern')->nullable()->comment('Regex pattern to extract code from email body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['platform_id', 'subject']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_subjects');
    }
};

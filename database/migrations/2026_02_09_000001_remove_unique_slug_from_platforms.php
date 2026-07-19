<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar el índice único del campo slug para permitir duplicados por usuario
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropUnique('platforms_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->unique('slug');
        });
    }
};

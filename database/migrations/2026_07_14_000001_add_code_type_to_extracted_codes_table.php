<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extracted_codes', function (Blueprint $table) {
            // Tipo de resultado: 'code' (número), 'link' (botón/URL), 'html' (fallback)
            $table->string('code_type')->default('code')->after('code');
            // Asunto del correo (para referencia en el panel)
            $table->string('subject')->nullable()->after('uid');
        });
    }

    public function down(): void
    {
        Schema::table('extracted_codes', function (Blueprint $table) {
            $table->dropColumn(['code_type', 'subject']);
        });
    }
};

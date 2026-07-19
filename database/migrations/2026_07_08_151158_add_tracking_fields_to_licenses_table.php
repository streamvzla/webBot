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
        Schema::table('licenses', function (Blueprint $table) {
            // Cambiar notas a TEXT para permitir notas largas
            $table->text('notes')->nullable()->change();

            // Fecha de primera activación (cuando se instaló por primera vez)
            $table->timestamp('activated_at')->nullable()->after('notes');

            // Último ping exitoso de verificación
            $table->timestamp('last_verified_at')->nullable()->after('activated_at');

            // Límites opcionales por licencia
            $table->unsignedInteger('max_clients')->nullable()->after('last_verified_at')
                  ->comment('Máximo de clientes permitidos. NULL = ilimitado');
            $table->unsignedInteger('max_queries_day')->nullable()->after('max_clients')
                  ->comment('Límite global de consultas/día. NULL = ilimitado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn([
                'activated_at',
                'last_verified_at',
                'max_clients',
                'max_queries_day',
            ]);
            $table->string('notes')->nullable()->change();
        });
    }
};

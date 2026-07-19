<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migramos plataformas globales al Súper Admin (ID 1)
        DB::table('platforms')->whereNull('user_id')->update(['user_id' => 1]);
        
        // Migramos cuentas de email globales al Súper Admin (ID 1)
        DB::table('email_accounts')->whereNull('user_id')->update(['user_id' => 1]);
        
        // Migramos correos permitidos globales al Súper Admin (ID 1)
        DB::table('allowed_emails')->whereNull('user_id')->update(['user_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En una migración de datos, revertir esto no es trivial sin saber
        // cuáles eran explícitamente globales antes. 
        // Lo dejamos vacío o comentamos que no se puede revertir de forma segura.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar user_id a clients si no existe
        if (!Schema::hasColumn('clients', 'user_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('is_active')->constrained('users')->onDelete('set null');
            });
        }

        // Agregar is_authorized a email_accounts si no existe
        if (!Schema::hasColumn('email_accounts', 'is_authorized')) {
            Schema::table('email_accounts', function (Blueprint $table) {
                $table->boolean('is_authorized')->default(false)->after('is_active');
                $table->text('authorization_notes')->nullable()->after('is_authorized');
            });
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id']);
        });

        Schema::table('email_accounts', function (Blueprint $table) {
            $table->dropColumn(['is_authorized', 'authorization_notes']);
        });
    }
};

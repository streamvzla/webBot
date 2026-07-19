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
        Schema::table('email_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('email_accounts', 'password')) {
                $table->renameColumn('password', 'imap_password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('email_accounts', 'imap_password')) {
                $table->renameColumn('imap_password', 'password');
            }
        });
    }
};

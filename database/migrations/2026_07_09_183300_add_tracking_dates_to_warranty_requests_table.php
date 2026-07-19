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
        Schema::table('warranty_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('warranty_requests', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('warranty_requests', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('resolved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_requests', function (Blueprint $table) {
            $table->dropColumn(['resolved_at', 'cancelled_at']);
        });
    }
};

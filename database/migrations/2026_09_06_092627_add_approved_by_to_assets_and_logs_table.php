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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('approved_by')->nullable()->after('borrowed_by'); // e.g. "Alex Pratama"
        });

        Schema::table('asset_logs', function (Blueprint $table) {
            $table->string('admin_name')->nullable()->after('actor_name'); // e.g. "Alex" (approver) or "Anex" (acceptor)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('approved_by');
        });

        Schema::table('asset_logs', function (Blueprint $table) {
            $table->dropColumn('admin_name');
        });
    }
};

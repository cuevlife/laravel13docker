<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Final Deep Optimization for smartbill (April 2026)
     */
    public function up(): void
    {
        // 1. Clean up Users table
        Schema::table('users', function (Blueprint $table) {
            $columns = ['settings', 'email_verified_at', 'remember_token'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 2. Clean up Merchants table (Folders)
        Schema::table('merchants', function (Blueprint $table) {
            $columns = ['subdomain', 'config'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('merchants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 3. Clean up Slips table
        Schema::table('slips', function (Blueprint $table) {
            if (Schema::hasColumn('slips', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No looking back. Forward to performance!
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix ownership and remove unused columns from merchants (Folders)
     */
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            // 1. Add Ownership
            if (!Schema::hasColumn('merchants', 'user_id')) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            }

            // 2. Remove Unused Columns (Internal Tool Optimization)
            $unusedColumns = ['tax_id', 'address', 'phone'];
            foreach ($unusedColumns as $column) {
                if (Schema::hasColumn('merchants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No looking back.
    }
};

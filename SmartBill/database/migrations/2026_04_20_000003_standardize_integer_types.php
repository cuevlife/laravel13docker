<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize integer types: convert tinyint, smallint to standard int.
     */
    public function up(): void
    {
        // 1. Standardize Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role')->unsigned()->default(1)->change();
            $table->integer('max_folders')->unsigned()->default(3)->change();
            $table->integer('tokens')->default(0)->change();
        });

        // 2. Standardize Merchants Table
        Schema::table('merchants', function (Blueprint $table) {
            if (Schema::hasColumn('merchants', 'max_slips')) {
                $table->integer('max_slips')->unsigned()->default(10000)->change();
            }
        });

        // 3. Standardize Token Logs Table
        Schema::table('token_logs', function (Blueprint $table) {
            $table->integer('delta')->change();
            $table->integer('balance_after')->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rolling back. Standard is standard.
    }
};

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
        // 1. Users Table Optimization
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('username', 50)->change();
            $table->string('email', 150)->change();
            $table->tinyInteger('role')->unsigned()->default(1)->change();
            $table->string('status', 20)->default('active')->change();
            $table->unsignedInteger('tokens')->default(0)->change();
            $table->unsignedSmallInteger('max_folders')->default(3)->change();
        });

        // 2. Merchants / Folders Table Optimization
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('name', 150)->change();
            $table->string('subdomain', 100)->change();
            $table->string('status', 20)->default('active')->change();
            $table->string('tax_id', 30)->nullable()->change();
            $table->string('phone', 50)->nullable()->change();
            $table->unsignedInteger('max_slips')->default(10000)->change();
        });

        // 3. Slips Table Optimization
        Schema::table('slips', function (Blueprint $table) {
            $table->string('uid', 50)->change();
            $table->string('workflow_status', 30)->default('pending')->change();
            $table->string('image_hash', 64)->nullable()->change();
        });

        // 4. Slip Batches Table Optimization
        Schema::table('slip_batches', function (Blueprint $table) {
            $table->string('name', 150)->change();
            $table->string('status', 20)->default('open')->change();
        });

        // 5. Slip Templates Table Optimization
        Schema::table('slip_templates', function (Blueprint $table) {
            $table->string('name', 100)->change();
        });

        // 6. Token Logs Table Optimization
        Schema::table('token_logs', function (Blueprint $table) {
            $table->string('type', 40)->change();
        });

        // 7. Audit Logs Table Optimization
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('event', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No going back from extreme optimization
    }
};

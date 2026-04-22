<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shrink VARCHAR columns to appropriate sizes for better performance.
     */
    public function up(): void
    {
        // 1. Optimize Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->change();
            $table->string('password', 100)->change(); // Fits Bcrypt (60) and Argon2 (95)
            $table->string('status', 20)->default('active')->change();
        });

        // 2. Optimize Merchants Table
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->change();
        });

        // 3. Optimize Slips Table
        Schema::table('slips', function (Blueprint $table) {
            $table->string('uid', 30)->change();
            $table->string('image_hash', 32)->change();
            $table->string('workflow_status', 20)->change();
        });

        // 4. Optimize Audit Logs Table
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('event', 50)->change();
            $table->string('ip_address', 45)->nullable()->change();
        });

        // 5. Optimize Token Logs Table
        Schema::table('token_logs', function (Blueprint $table) {
            $table->string('type', 30)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to grow back.
    }
};

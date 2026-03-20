<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Merchants Table
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('config')->nullable(); // Stores Mapping Rules
            $table->timestamps();
        });

        // 3. Slips Table (Link between User and Merchant)
        Schema::create('slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->json('extracted_data')->nullable(); // Stores AI Result
            $table->string('status')->default('completed');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Optional: Sessions for login stability
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('slips');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('users');
    }
};

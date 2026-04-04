<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedInteger('monthly_tokens')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('THB');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->unsignedInteger('tokens_allocated')->default(0);
            $table->unsignedInteger('tokens_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamps();
        });

        Schema::create('token_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slip_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('delta');
            $table->unsignedInteger('balance_after')->default(0);
            $table->string('type')->default('usage');
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        DB::table('plans')->insert([
            [
                'name' => 'Free',
                'code' => 'free',
                'monthly_tokens' => 50,
                'price' => 0,
                'currency' => 'THB',
                'is_active' => true,
                'description' => 'Starter access for trying SmartBill Intelligence.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'code' => 'pro',
                'monthly_tokens' => 500,
                'price' => 990,
                'currency' => 'THB',
                'is_active' => true,
                'description' => 'For teams processing slips every day.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('token_logs');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};

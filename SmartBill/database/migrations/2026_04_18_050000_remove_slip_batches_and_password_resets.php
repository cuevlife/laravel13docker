<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Add merchant_id to slips if not present (it was dropped in a previous migration)
        if (!Schema::hasColumn('slips', 'merchant_id')) {
            Schema::table('slips', function (Blueprint $table) {
                $table->unsignedInteger('merchant_id')->nullable()->after('user_id');
                $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            });
        }

        // 2. Data Migration: Populate slips.merchant_id from slip_batches.merchant_id
        if (Schema::hasTable('slip_batches')) {
            DB::statement("
                UPDATE slips 
                JOIN slip_batches ON slips.slip_batch_id = slip_batches.id 
                SET slips.merchant_id = slip_batches.merchant_id 
                WHERE slips.merchant_id IS NULL
            ");
        }

        // 3. Drop slip_batch_id foreign key and column
        Schema::table('slips', function (Blueprint $table) {
            if (Schema::hasColumn('slips', 'slip_batch_id')) {
                $table->dropForeign(['slip_batch_id']);
                $table->dropColumn('slip_batch_id');
            }
        });

        // 4. Drop slip_batches table
        Schema::dropIfExists('slip_batches');

        // 5. Drop password_reset_tokens table
        Schema::dropIfExists('password_reset_tokens');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rolling back from Concise Automation
    }
};

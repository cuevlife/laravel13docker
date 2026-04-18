<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Step 1: Drop all foreign keys that might cause issues
        Schema::table('merchants', fn(Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('slip_batches', function(Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropForeign(['created_by']);
        });
        Schema::table('slip_templates', function(Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::table('slips', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['slip_template_id']);
            $table->dropForeign(['slip_batch_id']);
        });
        Schema::table('token_logs', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['slip_id']);
        });
        Schema::table('token_topup_requests', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reviewed_by']);
        });
        Schema::table('audit_logs', fn(Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('slip_exports', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['merchant_id']);
        });

        // Step 2: Change types to INT UNSIGNED
        Schema::table('users', fn(Blueprint $table) => $table->unsignedInteger('id', true)->change());
        Schema::table('merchants', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('user_id')->nullable()->change();
        });
        Schema::table('slip_batches', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('merchant_id')->change();
            $table->unsignedInteger('created_by')->nullable()->change();
        });
        Schema::table('slip_templates', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('merchant_id')->change();
            $table->unsignedInteger('user_id')->nullable()->change();
        });
        Schema::table('slips', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->unsignedInteger('slip_template_id')->nullable()->change();
            $table->unsignedInteger('slip_batch_id')->nullable()->change();
        });
        Schema::table('token_logs', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->unsignedInteger('slip_id')->nullable()->change();
        });
        Schema::table('token_topup_requests', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('user_id')->change();
            $table->unsignedInteger('reviewed_by')->nullable()->change();
        });
        Schema::table('audit_logs', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->unsignedInteger('auditable_id')->nullable()->change();
        });
        Schema::table('slip_exports', function(Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->unsignedInteger('merchant_id')->nullable()->change();
        });

        // Step 3: Re-create foreign keys
        Schema::table('merchants', fn(Blueprint $table) => $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'));
        Schema::table('slip_batches', function(Blueprint $table) {
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
        Schema::table('slip_templates', function(Blueprint $table) {
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::table('slips', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('slip_template_id')->references('id')->on('slip_templates')->onDelete('set null');
            $table->foreign('slip_batch_id')->references('id')->on('slip_batches')->onDelete('set null');
        });
        Schema::table('token_logs', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('slip_id')->references('id')->on('slips')->onDelete('cascade');
        });
        Schema::table('token_topup_requests', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
        Schema::table('audit_logs', fn(Blueprint $table) => $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'));
        Schema::table('slip_exports', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void {}
};

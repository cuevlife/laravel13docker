<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Refine Merchants Table
        Schema::table('merchants', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            $table->text('address')->after('name')->nullable();
            $table->string('tax_id')->after('address')->nullable();
            $table->string('phone')->after('tax_id')->nullable();
        });

        // 2. Link Slip Templates to Merchants
        Schema::table('slip_templates', function (Blueprint $table) {
            $table->foreignId('merchant_id')->after('user_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('slip_templates', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropColumn('merchant_id');
        });

        Schema::table('merchants', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'address', 'tax_id', 'phone']);
        });
    }
};

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
        Schema::table('merchants', function (Blueprint $table) {
            if (!Schema::hasColumn('merchants', 'logo_path')) {
                $table->string('logo_path', 255)->nullable()->after('name');
            }
            if (!Schema::hasColumn('merchants', 'config')) {
                $table->json('config')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            if (Schema::hasColumn('merchants', 'logo_path')) {
                $table->dropColumn('logo_path');
            }
            if (Schema::hasColumn('merchants', 'config')) {
                $table->dropColumn('config');
            }
        });
    }
};

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
        Schema::table('slip_exports', function (Blueprint $table) {
            // Drop unused/redundant columns
            $table->dropColumn(['template_ids', 'search', 'date_from', 'date_to']);

            // Optimize string columns
            $table->string('file_name', 150)->change();
            $table->string('file_format', 10)->default('xlsx')->change();
            $table->string('export_mode', 30)->default('summary')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rolling back from extreme optimization
    }
};

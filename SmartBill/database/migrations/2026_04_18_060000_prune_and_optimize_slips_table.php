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
        Schema::table('slips', function (Blueprint $table) {
            // Drop unused columns
            if (Schema::hasColumn('slips', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('slips', 'slip_template_id')) {
                $table->dropForeign(['slip_template_id']);
                $table->dropColumn('slip_template_id');
            }
            if (Schema::hasColumn('slips', 'labels')) {
                $table->dropColumn('labels');
            }
            if (Schema::hasColumn('slips', 'archived_at')) {
                $table->dropColumn('archived_at');
            }

            // Optimize existing columns
            $table->string('image_path', 150)->change();
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

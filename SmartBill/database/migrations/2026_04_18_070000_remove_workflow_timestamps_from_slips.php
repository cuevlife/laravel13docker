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
            $table->dropColumn(['reviewed_at', 'approved_at', 'exported_at']);
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

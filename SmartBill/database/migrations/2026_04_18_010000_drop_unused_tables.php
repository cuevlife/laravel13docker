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
        // Drop pivot table for teams
        Schema::dropIfExists('merchant_user');
        
        // Drop SaaS billing tables
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No going back from Concise Automation
    }
};

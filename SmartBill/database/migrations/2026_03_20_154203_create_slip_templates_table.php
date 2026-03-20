<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Slip Templates (User Defined Structures)
        Schema::create('slip_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., Grocery, Medical, Travel
            $table->text('main_instruction'); // The AI Mission
            $table->json('ai_fields'); // { "date": true, "total": true, "items": false }
            $table->timestamps();
        });

        // 2. Adjust Slips Table to link to Templates
        Schema::table('slips', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropColumn('merchant_id');
            $table->foreignId('slip_template_id')->after('user_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slip_templates');
    }
};

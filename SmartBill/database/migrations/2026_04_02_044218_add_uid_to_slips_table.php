<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slips', function (Blueprint $blueprint) {
            $blueprint->string('uid', 32)->unique()->after('id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('slips', function (Blueprint $blueprint) {
            $blueprint->dropColumn('uid');
        });
    }
};

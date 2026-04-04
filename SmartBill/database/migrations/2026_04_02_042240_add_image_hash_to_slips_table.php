<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slips', function (Blueprint $blueprint) {
            $blueprint->string('image_hash', 64)->nullable()->after('image_path')->index();
        });
    }

    public function down(): void
    {
        Schema::table('slips', function (Blueprint $blueprint) {
            $blueprint->dropColumn('image_hash');
        });
    }
};

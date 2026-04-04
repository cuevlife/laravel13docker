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
            $table->string('subdomain')->after('name')->unique()->nullable();
        });

        Schema::create('merchant_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('employee'); // owner, admin, employee
            $table->timestamps();

            $table->unique(['merchant_id', 'user_id']); // A user should only be associated once per store
        });

        // Migrate existing owner linkages
        \Illuminate\Support\Facades\DB::table('merchants')->whereNotNull('user_id')->orderBy('id')->each(function ($merchant) {
            \Illuminate\Support\Facades\DB::table('merchant_user')->insert([
                'merchant_id' => $merchant->id,
                'user_id' => $merchant->user_id,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $baseSubdomain = \Illuminate\Support\Str::slug($merchant->name) ?: 'store' . $merchant->id;
            $subdomain = $baseSubdomain;
            $counter = 1;

            while (\Illuminate\Support\Facades\DB::table('merchants')->where('subdomain', $subdomain)->exists()) {
                $subdomain = $baseSubdomain . '-' . $counter;
                $counter++;
            }

            \Illuminate\Support\Facades\DB::table('merchants')->where('id', $merchant->id)->update([
                'subdomain' => $subdomain
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_user');

        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('subdomain');
        });
    }
};

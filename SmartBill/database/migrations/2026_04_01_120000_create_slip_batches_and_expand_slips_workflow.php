<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slip_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('open');
            $table->text('note')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index(['merchant_id', 'archived_at']);
        });

        Schema::table('slips', function (Blueprint $table) {
            $table->foreignId('slip_batch_id')->nullable()->after('slip_template_id')->constrained('slip_batches')->nullOnDelete();
            $table->string('workflow_status')->default('reviewed')->after('status');
            $table->json('labels')->nullable()->after('workflow_status');
            $table->timestamp('reviewed_at')->nullable()->after('processed_at');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->timestamp('exported_at')->nullable()->after('approved_at');
            $table->timestamp('archived_at')->nullable()->after('exported_at');

            $table->index('slip_batch_id');
            $table->index('workflow_status');
            $table->index('archived_at');
            $table->index('processed_at');
            $table->index(['slip_template_id', 'workflow_status']);
        });

        DB::table('slips')
            ->whereNull('reviewed_at')
            ->update([
                'workflow_status' => DB::raw("CASE WHEN status = 'completed' THEN 'reviewed' ELSE COALESCE(status, 'reviewed') END"),
                'reviewed_at' => DB::raw('COALESCE(processed_at, created_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('slips', function (Blueprint $table) {
            $table->dropIndex(['slip_template_id', 'workflow_status']);
            $table->dropIndex(['processed_at']);
            $table->dropIndex(['archived_at']);
            $table->dropIndex(['workflow_status']);
            $table->dropIndex(['slip_batch_id']);

            $table->dropConstrainedForeignId('slip_batch_id');
            $table->dropColumn([
                'workflow_status',
                'labels',
                'reviewed_at',
                'approved_at',
                'exported_at',
                'archived_at',
            ]);
        });

        Schema::dropIfExists('slip_batches');
    }
};

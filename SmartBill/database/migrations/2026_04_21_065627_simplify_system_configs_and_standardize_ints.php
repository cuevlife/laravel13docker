<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Simplify system_configs table
        if (Schema::hasColumn('system_configs', 'is_encrypted')) {
            Schema::table('system_configs', function (Blueprint $table) {
                $table->dropColumn('is_encrypted');
            });
        }

        // 2. Standardize Integer Types (BigInt -> Int, TinyInt -> Int)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // --- STEP 1: Drop Known Foreign Keys ---
        $this->dropFK('merchants', 'merchants_user_id_foreign');
        $this->dropFK('slips', 'slips_user_id_foreign');
        $this->dropFK('slips', 'slips_merchant_id_foreign');
        $this->dropFK('slip_templates', 'slip_templates_user_id_foreign');
        $this->dropFK('audit_logs', 'audit_logs_user_id_foreign');
        $this->dropFK('token_logs', 'token_logs_user_id_foreign');
        $this->dropFK('token_logs', 'token_logs_slip_id_foreign');
        $this->dropFK('slip_templates', 'slip_templates_user_id_foreign');
        $this->dropFK('slip_templates', 'slip_templates_merchant_id_foreign');
        $this->dropFK('audit_logs', 'audit_logs_user_id_foreign');

        // --- STEP 2: Change Foreign Key Column Types ---
        DB::statement('ALTER TABLE merchants MODIFY user_id INT');
        DB::statement('ALTER TABLE slips MODIFY user_id INT');
        DB::statement('ALTER TABLE slips MODIFY merchant_id INT');
        DB::statement('ALTER TABLE slip_templates MODIFY user_id INT');
        DB::statement('ALTER TABLE slip_templates MODIFY merchant_id INT');
        DB::statement('ALTER TABLE audit_logs MODIFY user_id INT');
        DB::statement('ALTER TABLE audit_logs MODIFY auditable_id INT NULL');
        DB::statement('ALTER TABLE token_logs MODIFY user_id INT');
        DB::statement('ALTER TABLE token_logs MODIFY slip_id INT NULL');
        DB::statement('ALTER TABLE slip_exports MODIFY user_id INT');
        DB::statement('ALTER TABLE slip_exports MODIFY merchant_id INT');

        // --- STEP 3: Change Primary Key Column Types ---
        DB::statement('ALTER TABLE system_configs MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE users MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE users MODIFY role INT DEFAULT 0');
        DB::statement('ALTER TABLE merchants MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE slips MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE slip_templates MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE audit_logs MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE token_logs MODIFY id INT AUTO_INCREMENT');
        DB::statement('ALTER TABLE slip_exports MODIFY id INT AUTO_INCREMENT');

        // --- STEP 4: Recreate Foreign Keys ---
        DB::statement('ALTER TABLE merchants ADD CONSTRAINT merchants_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE slips ADD CONSTRAINT slips_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE slips ADD CONSTRAINT slips_merchant_id_foreign FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function dropFK($table, $name)
    {
        try {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
        } catch (\Exception $e) {
            // Ignore if not exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rolling back from extreme optimization
    }
};

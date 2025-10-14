<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add optimized indexes for dashboard queries on jurnal_details table.
     * Dashboard typically shows:
     * - Recent transactions (ORDER BY created_at DESC)
     * - Total debit/credit per period
     * - Statistics by user and periode
     */
    public function up(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            // Composite index for dashboard: created_by + created_at (DESC) + is_deleted
            // Supports: SELECT * FROM jurnal_details WHERE created_by = ? AND YEAR(created_at) = ? AND is_deleted IS NULL ORDER BY created_at DESC LIMIT 10
            // Using DB::raw for descending index if supported
            if (config('database.default') === 'mysql') {
                // MySQL supports descending indexes in 8.0+
                DB::statement('CREATE INDEX idx_jurnal_details_dashboard ON jurnal_details (created_by, created_at DESC, is_deleted)');
            } else {
                // Fallback for other databases
                $table->index(['created_by', 'created_at', 'is_deleted'], 'idx_jurnal_details_dashboard');
            }

            // Index for aggregation queries (SUM debit, SUM credit)
            // Supports: SELECT SUM(debit), SUM(credit) FROM jurnal_details WHERE created_by = ? AND created_at BETWEEN ? AND ?
            $table->index(['created_by', 'created_at', 'debit', 'credit'], 'idx_jurnal_details_aggregation');

            // Index for jurnal_id + created_at (for detail queries with date sorting)
            // Supports: SELECT * FROM jurnal_details WHERE jurnal_id = ? ORDER BY created_at
            $table->index(['jurnal_id', 'created_at'], 'idx_jurnal_details_jurnal_created');

            // Covering index for dashboard summary (includes commonly selected columns)
            // Supports: SELECT id, jurnal_id, coa_akun, debit, credit, created_at FROM jurnal_details WHERE created_by = ?
            $table->index(['created_by', 'is_deleted', 'created_at', 'debit', 'credit'], 'idx_jurnal_details_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            // Drop indexes
            if (config('database.default') === 'mysql') {
                DB::statement('DROP INDEX idx_jurnal_details_dashboard ON jurnal_details');
            } else {
                $table->dropIndex('idx_jurnal_details_dashboard');
            }

            $table->dropIndex('idx_jurnal_details_aggregation');
            $table->dropIndex('idx_jurnal_details_jurnal_created');
            $table->dropIndex('idx_jurnal_details_summary');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add indexes to jurnal_details table for query optimization.
     * These indexes support common query patterns:
     * - Filtering by created_by + periode
     * - Filtering by coa_akun for ledger queries
     * - Filtering by jurnal_id for detail lookups
     */
    public function up(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            // Index for jurnal_id (FK lookup, very common)
            // Supports: WHERE jurnal_id = ?
            $table->index('jurnal_id', 'idx_jurnal_details_jurnal_id');

            // Index for coa_akun (used for buku besar, ledger queries)
            // Supports: WHERE coa_akun = ?
            $table->index('coa_akun', 'idx_jurnal_details_coa_akun');

            // Index for created_by + created_at (used for periode filtering via global scope)
            // Supports: WHERE created_by = ? AND YEAR(created_at) = ?
            $table->index(['created_by', 'created_at'], 'idx_jurnal_details_created_by_created_at');

            // Index for created_by + is_deleted
            // Supports: WHERE created_by = ? AND (is_deleted IS NULL OR is_deleted = 0)
            $table->index(['created_by', 'is_deleted'], 'idx_jurnal_details_created_by_is_deleted');

            // Index for created_by + coa_akun (used for account-specific queries per user)
            // Supports: WHERE created_by = ? AND coa_akun = ?
            $table->index(['created_by', 'coa_akun'], 'idx_jurnal_details_created_by_coa_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            $table->dropIndex('idx_jurnal_details_jurnal_id');
            $table->dropIndex('idx_jurnal_details_coa_akun');
            $table->dropIndex('idx_jurnal_details_created_by_created_at');
            $table->dropIndex('idx_jurnal_details_created_by_is_deleted');
            $table->dropIndex('idx_jurnal_details_created_by_coa_akun');
        });
    }
};

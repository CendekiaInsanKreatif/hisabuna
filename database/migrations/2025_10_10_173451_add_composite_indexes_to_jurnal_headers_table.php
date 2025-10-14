<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add composite indexes to jurnal_headers table for query optimization.
     * These indexes improve performance for common query patterns:
     * - Filtering by created_by + periode (year from created_at)
     * - Filtering by created_by + is_deleted
     * - Filtering by created_by + jurnal_tgl
     */
    public function up(): void
    {
        Schema::table('jurnal_headers', function (Blueprint $table) {
            // Index for created_by + created_at (used for periode filtering via global scope)
            // Supports: WHERE created_by = ? AND YEAR(created_at) = ?
            $table->index(['created_by', 'created_at'], 'idx_jurnal_headers_created_by_created_at');

            // Index for created_by + is_deleted (common filter in queries)
            // Supports: WHERE created_by = ? AND (is_deleted IS NULL OR is_deleted = 0)
            $table->index(['created_by', 'is_deleted'], 'idx_jurnal_headers_created_by_is_deleted');

            // Index for created_by + jurnal_tgl (used for date range queries)
            // Supports: WHERE created_by = ? AND jurnal_tgl BETWEEN ? AND ?
            $table->index(['created_by', 'jurnal_tgl'], 'idx_jurnal_headers_created_by_jurnal_tgl');

            // Index for created_by + created_at + is_deleted (optimal for global scope + soft delete)
            // Supports: WHERE created_by = ? AND YEAR(created_at) = ? AND is_deleted IS NULL
            $table->index(['created_by', 'created_at', 'is_deleted'], 'idx_jurnal_headers_created_periode_deleted');

            // Index for no_transaksi (used for unique checks and lookups)
            $table->index('no_transaksi', 'idx_jurnal_headers_no_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_headers', function (Blueprint $table) {
            $table->dropIndex('idx_jurnal_headers_created_by_created_at');
            $table->dropIndex('idx_jurnal_headers_created_by_is_deleted');
            $table->dropIndex('idx_jurnal_headers_created_by_jurnal_tgl');
            $table->dropIndex('idx_jurnal_headers_created_periode_deleted');
            $table->dropIndex('idx_jurnal_headers_no_transaksi');
        });
    }
};

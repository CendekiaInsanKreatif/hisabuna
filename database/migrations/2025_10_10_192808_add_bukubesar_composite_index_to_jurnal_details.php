<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add composite index optimized for Buku Besar (General Ledger) queries.
     * This index specifically supports the common query pattern used in buku besar reports:
     * WHERE created_by = ? AND coa_akun = ? AND tanggal_bukti BETWEEN ? AND ? AND is_deleted IS NULL
     */
    public function up(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            // Check if tanggal_bukti column exists, if not we'll use created_at
            // Composite index for Buku Besar queries
            // Optimal column order: created_by (equality) -> coa_akun (equality) -> tanggal_bukti/created_at (range) -> is_deleted (filter)

            // First, check if tanggal_bukti column exists
            $hasColumn = Schema::hasColumn('jurnal_details', 'tanggal_bukti');

            if ($hasColumn) {
                // Index with tanggal_bukti if column exists
                $table->index(
                    ['created_by', 'coa_akun', 'tanggal_bukti', 'is_deleted'],
                    'idx_jurnal_details_bukubesar'
                );
            } else {
                // Fallback to created_at if tanggal_bukti doesn't exist
                $table->index(
                    ['created_by', 'coa_akun', 'created_at', 'is_deleted'],
                    'idx_jurnal_details_bukubesar'
                );
            }

            // Additional index for coa_akun + tanggal_bukti (supports date range queries per account)
            if ($hasColumn) {
                $table->index(['coa_akun', 'tanggal_bukti'], 'idx_jurnal_details_coa_tanggal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            $table->dropIndex('idx_jurnal_details_bukubesar');

            if (Schema::hasColumn('jurnal_details', 'tanggal_bukti')) {
                $table->dropIndex('idx_jurnal_details_coa_tanggal');
            }
        });
    }
};

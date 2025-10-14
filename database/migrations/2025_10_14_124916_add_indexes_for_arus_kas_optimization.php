<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add indexes to optimize Arus Kas (Cash Flow) report performance
     */
    public function up(): void
    {
        // Jurnal Details Indexes
        Schema::table('jurnal_details', function (Blueprint $table) {
            // Index for filtering by user and COA with deleted check
            $table->index(['created_by', 'coa_akun', 'is_deleted'], 'idx_jd_user_coa');

            // Index for JOIN with jurnal_headers
            $table->index(['jurnal_id', 'created_by', 'is_deleted'], 'idx_jd_jurnal_id');

            // Composite index for date range queries (fallback)
            $table->index(['created_by', 'tanggal_bukti', 'is_deleted'], 'idx_jd_date_range');
        });

        // Jurnal Headers Indexes
        Schema::table('jurnal_headers', function (Blueprint $table) {
            // Index for date range filtering
            $table->index(['created_by', 'jurnal_tgl', 'is_deleted'], 'idx_jh_user_date');

            // Index for primary key lookups with deleted check
            $table->index(['id', 'is_deleted'], 'idx_jh_id_deleted');
        });

        // COAs Indexes
        Schema::table('coas', function (Blueprint $table) {
            // Index for level 4 COAs with arus_kas category
            $table->index(['created_by', 'level', 'is_deleted', 'arus_kas'], 'idx_coa_lv4_arus_kas');

            // Index for level 5 COAs with saldo_normal
            $table->index(['created_by', 'level', 'is_deleted', 'saldo_normal'], 'idx_coa_lv5_normal');

            // Index for nomor_akun lookups
            $table->index(['nomor_akun', 'created_by', 'is_deleted'], 'idx_coa_nomor_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop Jurnal Details Indexes
        Schema::table('jurnal_details', function (Blueprint $table) {
            $table->dropIndex('idx_jd_user_coa');
            $table->dropIndex('idx_jd_jurnal_id');
            $table->dropIndex('idx_jd_date_range');
        });

        // Drop Jurnal Headers Indexes
        Schema::table('jurnal_headers', function (Blueprint $table) {
            $table->dropIndex('idx_jh_user_date');
            $table->dropIndex('idx_jh_id_deleted');
        });

        // Drop COAs Indexes
        Schema::table('coas', function (Blueprint $table) {
            $table->dropIndex('idx_coa_lv4_arus_kas');
            $table->dropIndex('idx_coa_lv5_normal');
            $table->dropIndex('idx_coa_nomor_akun');
        });
    }
};

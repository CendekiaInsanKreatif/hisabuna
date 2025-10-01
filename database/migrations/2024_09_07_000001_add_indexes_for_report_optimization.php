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
        Schema::table('jurnal_details', function (Blueprint $table) {
            // Composite index for frequent queries in bukuBesar report
            $table->index(['created_by', 'coa_akun', 'tanggal_bukti'], 'idx_jurnal_details_report');

            // Index for date range queries
            $table->index(['tanggal_bukti', 'created_by'], 'idx_jurnal_details_date_user');

            // Index for account filtering
            $table->index(['coa_akun', 'created_by'], 'idx_jurnal_details_account_user');
        });

        Schema::table('jurnal_headers', function (Blueprint $table) {
            // Index for joins with jurnal_details
            $table->index(['created_by', 'is_deleted'], 'idx_jurnal_headers_user_deleted');
        });

        Schema::table('coas', function (Blueprint $table) {
            // Index for COA lookups
            $table->index(['created_by', 'nomor_akun'], 'idx_coas_user_nomor');

            // Index for level filtering
            $table->index(['created_by', 'level', 'is_deleted'], 'idx_coas_level_filter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_details', function (Blueprint $table) {
            $table->dropIndex('idx_jurnal_details_report');
            $table->dropIndex('idx_jurnal_details_date_user');
            $table->dropIndex('idx_jurnal_details_account_user');
        });

        Schema::table('jurnal_headers', function (Blueprint $table) {
            $table->dropIndex('idx_jurnal_headers_user_deleted');
        });

        Schema::table('coas', function (Blueprint $table) {
            $table->dropIndex('idx_coas_user_nomor');
            $table->dropIndex('idx_coas_level_filter');
        });
    }
};

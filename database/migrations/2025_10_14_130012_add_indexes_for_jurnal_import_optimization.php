<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Optimizes jurnal import performance by adding composite indexes
     * for COA lookups during batch import operations.
     */
    public function up(): void
    {
        Schema::table('coas', function (Blueprint $table) {
            // Composite index for COA lookup during import (nomor_akun + created_by)
            // This significantly improves batch whereIn queries
            $table->index(['nomor_akun', 'created_by'], 'idx_coa_import_lookup');

            // Index for created_by alone (for overall tenant isolation)
            $table->index('created_by', 'idx_coa_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->dropIndex('idx_coa_import_lookup');
            $table->dropIndex('idx_coa_created_by');
        });
    }
};

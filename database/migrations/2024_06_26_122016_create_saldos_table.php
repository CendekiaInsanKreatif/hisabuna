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
        Schema::create('saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_akun')->constrained('coas');
            $table->integer('saldo_awal_debit');
            $table->integer('saldo_awal_kredit');
            $table->integer('current_saldo_debit');
            $table->integer('current_saldo_kredit');
            $table->integer('saldo_akhir_debit');
            $table->integer('saldo_akhir_kredit');
            $table->integer('saldo_total_transaksi_debit');
            $table->integer('saldo_total_transaksi_kredit');
            $table->string('periode_saldo');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos');
    }
};

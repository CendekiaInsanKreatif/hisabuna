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
        Schema::create('jurnal_headers', function (Blueprint $table) {
            $table->id();
            $table->string('no_urut_transaksi', 255)->nullable();
            $table->string('no_transaksi', 255);
            $table->string('jenis', 255)->nullable();
            $table->dateTime('jurnal_tgl');
            $table->string('keterangan', 255);
            $table->string('lampiran', 255)->nullable();
            $table->string('subtotal', 255)->nullable();
            $table->char('created_by', 36)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('is_deleted')->nullable();
        });

        Schema::create('jurnal_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('jurnal_id', 255);
            $table->string('coa_akun', 255);
            $table->integer('debit');
            $table->integer('credit');
            $table->string('keterangan', 255)->nullable();
            $table->char('created_by', 36)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('is_deleted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_headers');
        Schema::dropIfExists('jurnal_details');
    }
};

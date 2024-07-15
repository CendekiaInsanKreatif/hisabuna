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
        Schema::create('coas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parent_id')->nullable();
            $table->string('subchild')->nullable();
            $table->string('nomor_akun')->nullable()->index();
            $table->string('nama_akun')->nullable();
            $table->string('level')->nullable();
            $table->string('saldo_normal')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coas');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $fillable = ['coa_akun','saldo_awal_debit','saldo_awal_kredit','current_saldo_debit','current_saldo_kredit','saldo_akhir_debit','saldo_akhir_kredit','saldo_total_transaksi_debit','saldo_total_transaksi_kredit','periode_saldo','created_by'];

    protected $attributes = [
        'saldo_awal_debit' => 0,
        'saldo_awal_kredit' => 0,
        'current_saldo_debit' => 0,
        'current_saldo_kredit' => 0,
        'saldo_akhir_debit' => 0,
        'saldo_akhir_kredit' => 0,
        'saldo_total_transaksi_debit' => 0,
        'saldo_total_transaksi_kredit' => 0,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->periode_saldo = date('Y');
        });
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_akun', 'nomor_akun');
    }
}

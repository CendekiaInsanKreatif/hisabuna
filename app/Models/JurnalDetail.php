<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    use HasFactory;
    protected $table = 'jurnal_details';

    protected $fillable = ['id', 'jurnal_id', 'coa_akun', 'debit', 'credit', 'keterangan','tanggal_bukti','lampiran','created_by','updated_by','deleted_by'];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id', 'id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_akun', 'nomor_akun');
    }
}


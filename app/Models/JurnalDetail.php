<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class JurnalDetail extends Model
{
    use HasFactory;
    protected $table = 'jurnal_details';

    protected $fillable = ['id', 'jurnal_id', 'coa_akun', 'debit', 'credit', 'keterangan','tanggal_bukti','lampiran','created_by','updated_by','deleted_by'];

    public $timestamps = false;

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id', 'id')->whereYear('created_at', '=', auth()->user()->periode);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_akun', 'nomor_akun')->where('created_by', auth()->user()->id)->whereYear('created_at', '=', auth()->user()->periode);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('periode', function (Builder $builder) {
            $builder->whereYear('jurnal_details.created_at', '=', auth()->user()->periode);
        });
    }
}

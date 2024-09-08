<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Jurnal extends Model
{
    use HasFactory;
    protected $table = 'jurnal_headers';

    protected $fillable = ['id','no_urut_transaksi', 'no_transaksi','jurnal_tgl', 'jenis', 'keterangan','subtotal','created_by','created_at','updated_by','updated_at','deleted_by','deleted_at','is_deleted'];

    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'jurnal_id', 'id')->whereYear('created_at', '=', auth()->user()->periode);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('periode', function (Builder $builder) {
            $builder->whereYear('created_at', '=', auth()->user()->periode);
        });
    }
}

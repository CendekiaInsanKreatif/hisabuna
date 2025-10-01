<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Coa extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        "id",
        "parent_id",
        "subchild",
        "nomor_akun",
        "nama_akun",
        "level",
        "saldo_normal",
        "golongan",
        "arus_kas",
        "saldo_awal_debit",
        "saldo_awal_credit",
        "saldo_berjalan_debit",
        "saldo_berjalan_credit",
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "is_deleted",
        "deleted_at",
        "deleted_by",
        "tgl_dibuat",
        "periode",
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')->whereYear('created_at', '=', auth()->user()->periode);
    }

    public function child()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->whereYear('created_at', '=', auth()->user()->periode);
    }

    public function multiChild($levels = 3)
    {
        $descendants = collect($this->child()->get());

        if ($levels > 1) {
            foreach ($this->child()->get() as $child) {
                $descendants = $descendants->merge($child->multiChild($levels - 1));
            }
        }

        return $descendants;
    }

    public function jurnalDetails()
    {
        return $this->hasMany(JurnalDetail::class, 'coa_akun', 'nomor_akun')->whereYear('created_at', '=', auth()->user()->periode);
    }

    public function saldo()
    {
        return $this->hasMany(Saldo::class, 'coa_akun', 'nomor_akun')->whereYear('created_at', '=', auth()->user()->periode);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('periode', function (Builder $builder) {
            $builder->whereYear('created_at', '=', auth()->user()->periode);
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "is_deleted",
        "deleted_at",
        "deleted_by",
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function jurnalDetails()
    {
        return $this->hasMany(JurnalDetail::class, 'coa_akun', 'nomor_akun');
    }

    public function saldo()
    {
        return $this->hasMany(Saldo::class, 'coa_akun', 'nomor_akun');
    }
}

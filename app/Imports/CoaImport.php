<?php

namespace App\Imports;

use App\Models\Coa;
use App\Models\Saldo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $validator = Validator::make($row, [
            'no_akun' => 'required|regex:/^[0-9\-]+$/',
            'nama_akun' => 'required|string',
        ]);

        if ($validator->fails()) {
            return null;
        }

        $nomor_akun = $row['no_akun'];
        $nama_akun = $row['nama_akun'];

        if (Coa::where('nomor_akun', $nomor_akun)->exists()) {
            return null;
        }

        $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
        $jumlah_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);

        if ($jumlah_nomor_akun == 6) {
            $level = $jumlah_nomor_akun - 1;
        } elseif ($jumlah_nomor_akun > 6) {
            $level = $jumlah_nomor_akun - 2;
        } else {
            $level = $jumlah_nomor_akun;
        }

        switch ($level) {
            case 1:
                $parent_id = null;
                break;
            case 2:
                $parent_id = substr($nomor_akun, 0, 1);
                break;
            case 3:
                $parent_id = substr($nomor_akun, 0, 2);
                break;
            case 4:
                $parent_id = substr($nomor_akun, 0, 3);
                break;
            case 5:
                $parent_id = substr($nomor_akun, 0, 4);
                break;
            default:
                $parent_id = substr($nomor_akun, 0, 5);
        }

        $saldo_normal = "debit";

        $selCoa = Coa::where('nomor_akun', $parent_id)->where('created_by', Auth::user()->id)->first();
        $data = [
            'parent_id'    => $selCoa->id ?? null,
            'subchild'     => ($selCoa->subchild ?? 0) + 1,
            'nomor_akun'   => $nomor_akun,
            'nama_akun'    => $nama_akun,
            'level'        => $level,
            'saldo_normal' => $saldo_normal,
            'created_at'   => now(),
            'created_by'   => Auth::user()->id,
        ];

        $coa = Coa::create($data);

        if ($coa) {
            Saldo::create([
                'coa_akun' => $coa->nomor_akun,
                'created_by' => Auth::user()->id,
            ]);
        }

        return $coa;
    }
}

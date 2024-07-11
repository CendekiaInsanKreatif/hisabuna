<?php

namespace App\Imports;

use App\Models\Coa;
use App\Models\Saldo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;


class CoaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // This method is required by the ToCollection interface but is not used
    }
}

// class CoaImport implements ToModel, WithHeadingRow
// {
//     public function model(array $row)
//     {
//         $validator = Validator::make($row, [
//             'no_akun' => 'required|regex:/^[0-9\-]+$/',
//             'nama_akun' => 'required|string',
//         ]);

//         $data = [
//             'no_akun' => $row['no_akun'],
//             'nama_akun' => $row['nama_akun']
//         ];

//         if ($validator->fails()) {
//             throw new \Exception('Validation Error: ' . implode(', ', $validator->errors()->all()));
//         }

//         return DB::transaction(function () use ($row) {
//             $nomor_akun = $row['no_akun'];
//             $nama_akun = $row['nama_akun'];

//             if (Coa::where('nomor_akun', $nomor_akun)->exists()) {
//                 throw new \Exception("Account number already exists");
//             }

//             $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
//             $jumlah_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);

//             $level = $this->calculateLevel($jumlah_nomor_akun);
//             $parent_id = $this->determineParentId($nomor_akun, $level);

//             $selCoa = Coa::where('nomor_akun', $parent_id)
//                 ->where('created_by', Auth::user()->id)
//                 ->first();

//             if (empty($selCoa)) {
//                 throw new \Exception('Account Level ' . $level . ' not found');
//             }

//             $data = [
//                 'parent_id' => $selCoa->id ?? null,
//                 'subchild' => ($selCoa->subchild ?? 0) + 1,
//                 'nomor_akun' => $nomor_akun,
//                 'nama_akun' => $nama_akun,
//                 'level' => $level,
//                 'saldo_normal' => 'debit',
//                 'created_at' => now(),
//                 'created_by' => Auth::user()->id,
//             ];

//             $coa = Coa::create($data);

//             if ($coa) {
//                 Saldo::create([
//                     'coa_akun' => $coa->nomor_akun,
//                     'created_by' => Auth::user()->id,
//                 ]);
//             }

//             return $coa;
//         });
//     }

//     private function calculateLevel(int $jumlah_nomor_akun): int
//     {
//         if ($jumlah_nomor_akun == 6) {
//             return $jumlah_nomor_akun - 1;
//         } elseif ($jumlah_nomor_akun > 6) {
//             return $jumlah_nomor_akun - 2;
//         } else {
//             return $jumlah_nomor_akun;
//         }
//     }

//     private function determineParentId(string $nomor_akun, int $level): ?string
//     {
//         switch ($level) {
//             case 1:
//                 return null;
//             case 2:
//                 return substr($nomor_akun, 0, 1);
//             case 3:
//                 return substr($nomor_akun, 0, 2);
//             case 4:
//                 return substr($nomor_akun, 0, 3);
//             case 5:
//                 return substr($nomor_akun, 0, 4);
//             default:
//                 return substr($nomor_akun, 0, 5);
//         }
//     }
// }

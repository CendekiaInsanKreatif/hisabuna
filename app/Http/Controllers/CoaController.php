<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Saldo;
use App\Exports\CoaExport;
use App\Imports\CoaImport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coas.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nomor_akun'    => 'required|regex:/^[0-9\-]+$/',
            'nama_akun'     => 'required|string',
        ]);

        $check = Coa::where('nomor_akun', $request->nomor_akun)->where('created_by', Auth::user()->id)->first();

        if($validator->fails()){
            return redirect()->route('coas.index')->with('message', 'Validasi Error')->with('color', 'red');
        }

        if($check){
            return redirect()->route('coas.index')->with('message', 'Nomor Akun sudah ada')->with('color', 'red');
        }

        $nomor_akun     = $request->nomor_akun;
        $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
        $jumlah_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);
        if($jumlah_nomor_akun == "6") {
            $level      = $jumlah_nomor_akun - 1;
        }else if($jumlah_nomor_akun > "6"){
            $level      = $jumlah_nomor_akun - 2;
        }else{
            $level      = $jumlah_nomor_akun + 0;
        }


        $nama_akun      = $request->nama_akun;

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

        if(empty($selCoa)){
            return redirect()->route('coas.index')->with('message', 'Akun Level '.$level.' tidak ditemukan')->with('color', 'red');
        }

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

        $save = Coa::create($data);
        if($save) {
            Saldo::create([
                'coa_akun' => $save->nomor_akun,
                'created_by' => Auth::user()->id,
            ]);
            return redirect()->route('coas.index')->with('message', 'Berhasil membuat data')->with('color', 'green');
        } else {
            return redirect()->route('coas.index')->with('message', 'Gagal membuat data')->with('color', 'red');
        }
    }

    public function update(Request $request, Coa $coa)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'nomor_akun' => 'required|regex:/^[0-9\-]+$/',
            'nama_akun'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 400,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 400);
        }

        $nomor_akun = $coa->nomor_akun = $input['nomor_akun'];
        $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
        $jumlah_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);

        if ($jumlah_nomor_akun == 6) {
            $level = $jumlah_nomor_akun - 1;
        } elseif ($jumlah_nomor_akun > 6) {
            $level = $jumlah_nomor_akun - 2;
        } else {
            $level = $jumlah_nomor_akun;
        }

        $nama_akun = $input['nama_akun'];

        switch ($level) {
            case 1:
                $golongan = "Aset";
                $parent_id = null;
                break;
            case 2:
                $golongan = "Liabilitas";
                $parent_id = substr($nomor_akun, 0, 1);
                break;
            case 3:
                $golongan = "Ekuitas";
                $parent_id = substr($nomor_akun, 0, 2);
                break;
            case 4:
                $golongan = "Pendapatan";
                $parent_id = substr($nomor_akun, 0, 3);
                break;
            case 5:
                $golongan = "Beban";
                $parent_id = substr($nomor_akun, 0, 4);
                break;
            default:
                $golongan = "Beban Umum";
                $parent_id = substr($nomor_akun, 0, 5);
        }

        $selCoa = Coa::where('nomor_akun', $parent_id)->where('created_by', Auth::user()->id)->first();
        $data = [
            'parent_id'    => $selCoa->id ?? null,
            'nomor_akun'   => $nomor_akun,
            'nama_akun'    => $nama_akun,
            'level'        => $level,
            'golongan'     => $golongan,
            'saldo_normal' => "debit",
            'updated_at'   => now(),
            'updated_by'   => Auth::user()->id,
        ];

        $update = $coa->update($data);
        if ($update) {
            Saldo::where('coa_akun', $nomor_akun)->where('created_by', Auth::user()->id)->update([
                'coa_akun' => $nomor_akun,
            ]);
            return redirect()->route('coas.index')->with('message', 'Berhasil mengubah data')->with('color', 'green');
        } else {
            return redirect()->route('coas.index')->with('message', 'Gagal mengubah data')->with('color', 'red');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $coa = Coa::find($id);

        if (!$coa) {
            return redirect()->route('coas.index')->with('message', 'Data not found')->with('color', 'red');
        }

        $data = [
            'is_deleted'    => 1,
            'deleted_at'    => now(),
            'deleted_by'    => Auth::user()->id,
        ];

        $update = $coa->update($data);

        if ($update) {
            return redirect()->route('coas.index')->with('message', 'Berhasil menghapus data')->with('color', 'green');
        } else {
            return redirect()->route('coas.index')->with('message', 'Gagal menghapus data')->with('color', 'red');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        $path = $request->file('file')->getRealPath();
        $excel = Excel::toArray(new CoaImport, $request->file('file'))[0];

        usort($excel, function ($a, $b) {
            return strlen($a['no_akun']) <=> strlen($b['no_akun']);
        });

        foreach ($excel as $key => $value) {
            $nomor_akun     = $value['no_akun'];
            $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
            $jumlah_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);
            if($jumlah_nomor_akun == "6") {
                $level      = $jumlah_nomor_akun - 1;
            }else if($jumlah_nomor_akun > "6"){
                $level      = $jumlah_nomor_akun - 2;
            }else{
                $level      = $jumlah_nomor_akun + 0;
            }


            $nama_akun      = $value['nama_akun'];

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

            $data[] = [
                'parent_id'    => $selCoa->id ?? null,
                'subchild'     => ($selCoa->subchild ?? 0) + 1,
                'nomor_akun'   => $nomor_akun,
                'nama_akun'    => $nama_akun,
                'level'        => $level,
                'saldo_normal' => $saldo_normal,
                'created_at'   => now(),
                'created_by'   => Auth::user()->id,
            ];

            $saldos[] = [
                'coa_akun' => $nomor_akun,
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'saldo_awal_debit' => 0,
                'saldo_awal_kredit' => 0,
                'current_saldo_debit' => 0,
                'current_saldo_kredit' => 0,
                'saldo_akhir_debit' => 0,
                'saldo_akhir_kredit' => 0,
                'saldo_total_transaksi_debit' => 0,
                'saldo_total_transaksi_kredit' => 0,
            ];
        }

        $coa = Coa::insert($data);
        $saldo = Saldo::insert($saldos);
        if ($coa && $saldo) {
            Saldo::where('coa_akun', $nomor_akun)->where('created_by', Auth::user()->id)->update(['coa_akun' => $nomor_akun]);
            $updateCoa = Coa::where('created_by', Auth::user()->id)->get();
            foreach ($updateCoa as $coa) {
                $parent_id = $coa->level > 1 ? Coa::where('nomor_akun', substr($coa->nomor_akun, 0, $coa->level - 1))
                                             ->where('created_by', Auth::user()->id)
                                             ->value('id') : null;
                $coa->update([
                    'parent_id' => $parent_id,
                    'subchild' => Coa::where('parent_id', $coa->id)->where('created_by', Auth::user()->id)->count()
                ]);
            }
            return redirect()->back()->with('message', 'Data Coa berhasil di import')->with('color', 'green');
        } else {
            return redirect()->back()->with('message', 'Data Coa gagal di import')->with('color', 'red');
        }
    }

    public function export()
    {
        return Excel::download(new CoaExport(), 'coas.xlsx');
    }
}

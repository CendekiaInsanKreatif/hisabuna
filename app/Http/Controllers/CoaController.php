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

        if($validator->fails()){
            return redirect()->route('coas.index')->with('message', 'Validasi Error')->with('color', 'red');
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
                // $golongan = "Aset";
                $parent_id = null;
                break;
            case 2:
                // $golongan = "Liabilitas";
                $parent_id = substr($nomor_akun, 0, 1);
                break;
            case 3:
                // $golongan = "Ekuitas";
                $parent_id = substr($nomor_akun, 0, 2);
                break;
            case 4:
                // $golongan = "Pendapatan";
                $parent_id = substr($nomor_akun, 0, 3);
                break;
            case 5:
                // $golongan = "Beban";
                $parent_id = substr($nomor_akun, 0, 4);
                break;
            default:
                // $golongan = "Beban Umum";
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
            // 'golongan'     => $golongan,
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

        $saldo_normal = "debit";

        $selCoa = Coa::where('nomor_akun', $parent_id)->where('created_by', Auth::user()->id)->first();
        $data = [
            'parent_id'    => $selCoa->id ?? null,
            'nomor_akun'   => $nomor_akun,
            'nama_akun'    => $nama_akun,
            'level'        => $level,
            'golongan'     => $golongan,
            'saldo_normal' => $saldo_normal,
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

        Excel::import(new CoaImport, $request->file('file'));

        return redirect()->back()->with('message', 'Data Coa berhasil diimpor')->with('color', 'green');
    }

    public function export()
    {
        return Excel::download(new CoaExport(), 'coas.xlsx');
    }
}

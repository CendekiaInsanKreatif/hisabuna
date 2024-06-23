<?php

namespace App\Http\Controllers;

use App\Models\Coa;
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
        if($level == "1") {
            $golongan = "Aset";
        }else if($level == "2") {
            $golongan = "Leabilitas";
        }else if($level == "3") {
            $golongan = "Ekuitas";
        }else if($level == "4") {
            $golongan = "Pendapatan";
        }else if($level == "5") {
            $golongan = "Beban";
        }else if($level == "6") {
            $golongan = "Beban Umum";
        }

        $saldo_normal   = "Debit";

        $data = [
            'nomor_akun'    => $nomor_akun,
            'nama_akun'     => $nama_akun,
            'level'         => $level,
            'saldo_normal'  => $saldo_normal,
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        $save = Coa::create($data);
        if($save) {
            $save->update(['updated_at' => null]);
            return redirect()->route('coas.index')->with('message', 'Berhasil membuat data')->with('color', 'green');
        }else{
            return redirect()->route('coas.index')->with('message', 'Gagal membuat data')->with('color', 'red');
        }
    }

    /**
     * Update the specified resource in storage.
     */
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
                break;
            case 2:
                $golongan = "Liabilitas";
                break;
            case 3:
                $golongan = "Ekuitas";
                break;
            case 4:
                $golongan = "Pendapatan";
                break;
            case 5:
                $golongan = "Beban";
                break;
            default:
                $golongan = "Beban Umum";
        }

        $saldo_normal = "Debit";

        $data = [
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
            return redirect()->route('coas.index')->with('message', 'Berhasil membuat data')->with('color', 'green');
        } else {
            return redirect()->route('coas.index')->with('message', 'Gagal membuat data')->with('color', 'red');
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
        return redirect()->route('coas.index')->with('message', 'Berhasil Import data')->with('color', 'green');
    }

    public function export(Request $request)
    {
        $coas = Coa::whereNull('is_deleted')->get();
        $filename = 'coa_export_' . date('Ymd_His') . '.csv';
        $handle = fopen($filename, 'w');
        fputcsv($handle, ['Nomor Akun', 'Nama Akun', 'Level', 'Golongan', 'Saldo Normal']);

        foreach ($coas as $coa) {
            fputcsv($handle, [
                $coa->nomor_akun,
                $coa->nama_akun,
                $coa->level,
                $coa->golongan,
                $coa->saldo_normal
            ]);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}

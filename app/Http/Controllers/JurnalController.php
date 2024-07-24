<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Coa;
use App\Models\Saldo;
use App\Imports\JurnalDetailImport;
use App\Exports\JurnalSampleExport;
use Maatwebsite\Excel\Facades\Excel;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jurnal = Jurnal::with('details')->whereNull('is_deleted')->where('created_by', auth()->user()->id)->get()->toArray();

        return view('jurnal.index', compact('jurnal'));
    }

    public function lampiran(Jurnal $jurnal)
    {   
        $lampiran = Storage::disk('public')->files('lampiran/' . $jurnal->id);

        return view('report.views.lampiran', compact('jurnal', 'lampiran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coa = Coa::whereNull('is_deleted')
                    // ->where('level', 5)
                    ->where('created_by', auth()->user()->id)
                    ->get()
                    ->toArray();
        return view('jurnal.form', compact('coa'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $debit = array_map(function($x) {
                return (int) str_replace('.', '', $x);
            }, $input['debit']);
            $kredit = array_map(function($x) {
                return (int) str_replace('.', '', $x);
            }, $input['kredit']);
            $sumDebit = array_sum($debit);
            $sumKredit = array_sum($kredit);

            if($sumDebit != $sumKredit || $sumDebit - $sumKredit != 0){
                return redirect()->route('jurnal.index')->with('message', 'Debit tidak sama dengan kredit.')->with('color', 'red');
            }

            $jurnal = Jurnal::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get();

            if ($jurnal->isNotEmpty()) {
                $countJenis = $jurnal->where('jenis', strtoupper($input['jenis']))->count();
                $input['no_transaksi'] = $countJenis + 1;
            } else {
                $input['no_transaksi'] = 1;
            }

            $dataJurnal = Jurnal::create([
                'jenis' => strtoupper($input['jenis']),
                'no_urut_transaksi' => $jurnal->count() + 1,
                'no_transaksi' => $input['no_transaksi'],
                'jurnal_tgl' => now(),
                'subtotal' => $sumDebit,
                'keterangan' => $input['keterangan_header'],
                'created_by' => Auth::user()->id,
                'created_at' => now()
            ]);

            $details = [];
            foreach ($input['no_akun'] as $index => $noAkun) {
                $db = str_replace('.', '', $input['debit'][$index]);
                $kr = str_replace('.', '', $input['kredit'][$index]);

                $debit = (int) $db;
                $kredit = (int) $kr;


                Coa::where('nomor_akun', $noAkun)->where('created_by', auth()->user()->id)->update([
                    'saldo_berjalan_debit' => $debit,
                    'saldo_berjalan_credit' => $kredit,
                ]);

                $tgl_bukti = \Carbon\Carbon::parse($input['tanggal_bukti'][$index])->format('Y-m-d H:i:s');

                $details[] = [
                    'jurnal_id' => $dataJurnal->id,
                    'coa_akun' => $noAkun,
                    'debit' => $debit,
                    'credit' => $kredit,
                    'keterangan' => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'tanggal_bukti' => $tgl_bukti,
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ];
            }

            foreach ($details as $index => $detail) {
                $da = JurnalDetail::create($detail);
        
                if ($request->hasFile('lampiran')) {
                    $lampiranFiles = $request->file('lampiran');
                    if (isset($lampiranFiles[$index])) {
                        $file = $lampiranFiles[$index];
                        $filePath = 'lampiran/' . auth()->user()->company_name . '/' . $dataJurnal->id;
                        $fileName = $da->id . '.' . $file->getClientOriginalExtension();
                        $file->storeAs($filePath, $fileName, 'public');
                        $da->lampiran = $filePath . '/' . $fileName;
                        $da->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('message', 'Jurnal berhasil dibuat.')->with('color', 'green');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('jurnal.index')->with('message', 'Gagal membuat jurnal: ' . $e->getMessage())->with('color', 'red');
        }
    }

    public function show(Jurnal $jurnal){

    }

    public function edit(Jurnal $jurnal)
    {
        $jurnal = Jurnal::with(['details.coa'])
                ->whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->orderBy('jurnal_tgl', 'desc')
                ->find($jurnal->id);

        if ($jurnal) {
            foreach ($jurnal->details as $detail) {
                $detail->tanggal_bukti = \Carbon\Carbon::parse($detail->tanggal_bukti)->format('Y-m-d');
            }
        }
        $coa = Coa::whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->where('level', 5)
                ->get()
                ->toArray();

        return view('jurnal.form', compact('jurnal', 'coa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        $debit = array_map(function($x) {
            return (int) str_replace('.', '', $x);
        }, $request['debit']);
        $kredit = array_map(function($x) {
            return (int) str_replace('.', '', $x);
        }, $request['kredit']);
        $sumDebit = array_sum($debit);
        $sumKredit = array_sum($kredit);

        if($sumDebit != $sumKredit || $sumDebit - $sumKredit != 0){
            return redirect()->route('jurnal.index')->with('message', 'Debit tidak sama dengan kredit.')->with('color', 'red');
        }

        DB::beginTransaction();
        try {
            $input = $request->all();

            // dd($input);

            $existingJournals = Jurnal::whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->where('id', '<>', $jurnal->id)
                ->get();

            if ($existingJournals->isNotEmpty()) {
                $countJenis = $existingJournals->where('jenis', strtoupper($input['jenis']))->count();
                $input['no_transaksi'] = $countJenis + 1;
            } else {
                $input['no_transaksi'] = 1;
            }

            $jurnal->jenis = strtoupper($input['jenis']);
            $jurnal->no_transaksi = $input['no_transaksi'];
            $jurnal->jurnal_tgl = now();
            $jurnal->subtotal = array_sum($input['debit']);
            $jurnal->keterangan = $input['keterangan_header'];
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->updated_at = now();
            $jurnal->save();

            if($request->has('lampiran')){
                $filePath = 'lampiran/' . auth()->user()->company_name . '/' . $jurnal->id;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->deleteDirectory($filePath);
                }
            }

            JurnalDetail::where('jurnal_id', $jurnal->id)->delete();

            $details = [];
            foreach ($input['no_akun'] as $index => $noAkun) {
                $debit = $input['debit'][$index];
                $kredit = $input['kredit'][$index];

                $tgl_bukti = \Carbon\Carbon::parse($input['tanggal_bukti'][$index])->format('Y-m-d H:i:s');

                $details[] = [
                    'jurnal_id' => $jurnal->id,
                    'coa_akun' => $noAkun,
                    'debit' => $debit,
                    'credit' => $kredit,
                    'keterangan' => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'tanggal_bukti' => $tgl_bukti,
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ];
            }

            foreach ($details as $index => $detail) {
                $da = JurnalDetail::create($detail);
        
                if ($request->hasFile('lampiran')) {
                    $lampiranFiles = $request->file('lampiran');
                    if (isset($lampiranFiles[$index])) {
                        $file = $lampiranFiles[$index];
                        $filePath = 'lampiran/' . auth()->user()->company_name . '/' . $jurnal->id;
                        $fileName = $da->id . '.' . $file->getClientOriginalExtension();
                        $file->storeAs($filePath, $fileName, 'public');
                        $da->lampiran = $filePath . '/' . $fileName;
                        $da->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('jurnal.index')->with('message', 'Jurnal berhasil diperbarui.')->with('color', 'green');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('jurnal.edit', $jurnal)->with('message', 'Gagal memperbarui jurnal: ' . $e->getMessage())->with('color', 'red');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $jurnal)
    {
        //
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $path = $request->file('file')->store('temp');

        try {
            $import = new JurnalDetailImport;
            $data = Excel::toCollection($import, $path);
            unset($data[1]);

            $detail = [];

            foreach ($data[0] as $row) {
                if ($row['akun_coa'] !== null) {
                    if (strpos($row['akun_coa'], '|') !== false){
                        $akun = explode('|', $row['akun_coa']);
                    } else {
                        $coa = Coa::where(['nomor_akun' => $row['akun_coa'], 'created_by' => auth()->user()->id])->first();
                        $akun[0] = $coa->nomor_akun;
                        $akun[1] = $coa->nama_akun;
                    }
                    // da($akun);
                    $detail[] = [
                        'no_akun' => $akun[0],
                        'nama_akun' => $akun[1],
                        'debit' => $row['debit'],
                        'kredit' => $row['kredit'],
                        'keterangan' => $row['keterangan'],
                        'tanggal_bukti' => $row['tanggal_bukti']
                    ];
                }
            }

            if ($detail) {
                return response()->json([
                    'success' => true,
                    'rows' => $detail
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import data. Please check your file format.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sampleExport()
    {
        return Excel::download(new JurnalSampleExport(), 'jurnal_sample.xlsx');
    }
}

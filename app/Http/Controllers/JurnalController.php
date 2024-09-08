<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Alert;

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
                    ->where('level', 5)
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
                return strpos($x, '.') !== false ? (int) str_replace('.', '', $x) : (int) $x;
            }, $input['debit']);
            $kredit = array_map(function($x) {
                return strpos($x, '.') !== false ? (int) str_replace('.', '', $x) : (int) $x;
            }, $input['kredit']);

            $sumDebit = array_sum($debit);
            $sumKredit = array_sum($kredit);

            if($sumDebit != $sumKredit || $sumDebit - $sumKredit != 0){
                Alert::error('Oops!', 'Debit tidak sama dengan kredit.');
                return redirect()->back();
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
                if(strpos($noAkun, '-')){
                    $coaAkun = str_replace('-', '', $noAkun);
                }else{
                    $coaAkun = $noAkun;
                }
                $db = str_replace('.', '', $input['debit'][$index]);
                $kr = str_replace('.', '', $input['kredit'][$index]);

                $debit = (int) $db;
                $kredit = (int) $kr;

                Coa::where('nomor_akun', $coaAkun)
                   ->where('created_by', auth()->user()->id)
                   ->increment('saldo_berjalan_debit', $debit);
                Coa::where('nomor_akun', $coaAkun)
                   ->where('created_by', auth()->user()->id)
                   ->increment('saldo_berjalan_credit', $kredit);

                $tgl_bukti = \Carbon\Carbon::createFromFormat('d-m-Y', $input['tanggal_bukti'][$index])->format('Y-m-d H:i:s');

                $details[] = [
                    'jurnal_id' => $dataJurnal->id,
                    'coa_akun' => $coaAkun,
                    'debit' => $debit,
                    'credit' => $kredit,
                    'keterangan' => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'tanggal_bukti' => $tgl_bukti,
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ];
            }

            foreach (array_chunk($details, 1000) as $chunk) {
                JurnalDetail::insert($chunk);
            }

            if ($request->hasFile('lampiran')) {
                $lampiranFiles = $request->file('lampiran');
                foreach ($lampiranFiles as $index => $file) {
                    $filePath = 'lampiran/' . auth()->user()->company_name . '/' . $dataJurnal->id;
                    $fileName = $details[$index]['id'] . '.' . $file->getClientOriginalExtension();
                    $file->storeAs($filePath, $fileName, 'public');
                    JurnalDetail::where('id', $details[$index]['id'])->update(['lampiran' => $filePath . '/' . $fileName]);
                }
            }

            DB::commit();
            Log::info('Jurnal berhasil dibuat.', ['jurnal_id' => $dataJurnal->id]);
            Alert::success('Sukses!', 'Jurnal berhasil dibuat.');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal membuat jurnal: ' . $e->getMessage());
            Alert::error('Oops!', 'Gagal membuat jurnal: ' . $e->getMessage());
            return redirect()->back();
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
                // ->where('level', 5)
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
            Alert::error('Oops!', 'Debit tidak sama dengan kredit.');
            return redirect()->back();
        }

        // da($request->all());

        DB::beginTransaction();
        try {
            $input = $request->all();

            // da($input);

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
            $jurnal->subtotal = $sumDebit;
            $jurnal->keterangan = $input['keterangan_header'];
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->updated_at = now();
            $jurnal->save();

            // da($input);

            if($request->has('lampiran')){
                $filePath = 'lampiran/' . auth()->user()->company_name . '/' . $jurnal->id;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->deleteDirectory($filePath);
                }
            }

            JurnalDetail::where('jurnal_id', $jurnal->id)->delete();

            $details = [];
            foreach ($input['no_akun'] as $index => $noAkun) {
                $coaAkun = str_replace('-', '', $noAkun);
                $debit = (int) str_replace('.', '', $input['debit'][$index]);
                $kredit = (int) str_replace('.', '', $input['kredit'][$index]);

                $tgl_bukti = \Carbon\Carbon::createFromFormat('d-m-Y', $input['tanggal_bukti'][$index])->format('Y-m-d H:i:s');

                $details[] = [
                    'jurnal_id' => $jurnal->id,
                    'coa_akun' => $coaAkun,
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

            Alert::success('Sukses!', 'Jurnal berhasil diperbarui.');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Alert::error('Oops!', 'Gagal memperbarui jurnal: ' . $e->getMessage());
            return redirect()->back();
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
                        $akun_coa = str_replace('-', '', $row['akun_coa']);
                        $coa = Coa::where(['nomor_akun' => $akun_coa, 'created_by' => auth()->user()->id])->first();
                        if(!$coa){
                            return response()->json([
                                'success' => false,
                                'message' => 'Akun COA: ' . $row['akun_coa'] . ' tidak ditemukan.'
                            ], 500);
                        }
                        $akun[0] = $coa->nomor_akun;
                        $akun[1] = $coa->nama_akun;
                    }
                    // da($row);
                    $detail[] = [
                        'no_akun' => $akun[0],
                        'nama_akun' => $akun[1],
                        'debit' => (int) $row['debit'],
                        'kredit' => (int) $row['kredit'],
                        'keterangan' => $row['keterangan'],
                        'tanggal_bukti' => \Carbon\Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($row['tanggal_bukti'] - 25569) * 86400))->format('Y-m-d')
                    ];
                }
            }

            return $detail;

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importHtml(Request $request)
    {
        $file = $request->file('file');
        $importedData = $this->import($request);
        if(isset($importedData['success']) && !$importedData['success']){
            return response()->json(['html' => 0, 'message' => $importedData['message']]);
        } else {
            $cek = "";
            if (count($importedData) > 1000) {
                $cek = "Hanya 1000 data pertama yang di import.";
                $importedData = array_slice($importedData, 0, 1000);
            }else{
                $cek = "Data berhasil diimport, Silahkan Tunggu.";
            }
            return response()->json(['html' => $importedData, 'message' => $cek]);
        }

    }

    public function sampleExport()
    {
        return Excel::download(new JurnalSampleExport(), 'jurnal_sample.xlsx');
    }

    public function cekTrial()
    {
        $users = DB::select("
                SELECT 
                    id, 
                    created_at
                FROM users
                WHERE
                profile = ? AND
                is_active = ? AND 
                created_at <= DATE_SUB(NOW(), INTERVAL 2 MONTH)
            ", ['trial', 1]);
        
        foreach($users as $key => $value) {
            $data = [
                'is_active' => "0"
            ];
            $update = DB::table('users')->where('id', $value->id)->update($data);
        }

        if($update){
            return response()->json([
                'status'     => 200,
                'message'    => 'Update data berhasil'
            ]);
        }
        return response()->json([
            'status'     => 400,
            'message'    => 'Update data gagal'
        ]);
        
    }
}

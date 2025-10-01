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
use App\Exports\JurnalExport;
use Maatwebsite\Excel\Facades\Excel;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optimized query with better performance
        $jurnal = Jurnal::select([
                'id',
                'no_urut_transaksi',
                'jenis',
                'keterangan',
                'jurnal_tgl',
                'subtotal',
                'created_at'
            ])
            ->whereNull('is_deleted')
            ->where('created_by', auth()->id())
            ->where('periode', auth()->user()->periode)
            ->orderBy('id', 'desc')
            ->paginate(15);
        // dd($jurnal);

        return view('jurnal.index', compact('jurnal'));
    }

    /**
     * Get jurnal data for AJAX requests (for better frontend performance)
     */
    public function getData(Request $request)
    {
        // Debug authentication
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => [],
                'total' => 0
            ], 401);
        }

        $query = Jurnal::select([
                'id',
                'no_urut_transaksi',
                'jenis',
                'keterangan',
                'jurnal_tgl',
                'subtotal',
                'created_at',
                'tgl_dibuat'
            ])
            ->whereNull('is_deleted')
            ->where('created_by', auth()->id())
            ->where('periode', auth()->user()->periode);

        // dd($request->all());

        // Apply filters
        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $query->where('jenis', strtoupper($request->jenis));
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('no_urut_transaksi', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('keterangan', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('jenis', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('jurnal_tgl', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $jurnal = $query->orderBy('id', 'desc')->get();

        // dd($jurnal);
        // Debug logging
        \Log::info('Jurnal getData response', [
            'user_id' => auth()->id(),
            'total_records' => $jurnal->count(),
            'filters' => $request->all()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $jurnal,
            'total' => $jurnal->count()
        ]);
    }

    public function show($id)
    {
        try {
            $jurnal = Jurnal::with(['details.coa'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $jurnal->id,
                    'no_bukti' => $jurnal->no_bukti,
                    'no_urut_transaksi' => $jurnal->no_urut_transaksi,
                    'tanggal' => $jurnal->jurnal_tgl ? \Carbon\Carbon::parse($jurnal->jurnal_tgl)->format('Y-m-d') : null,
                    'jenis' => $jurnal->jenis,
                    'keterangan' => $jurnal->keterangan,
                    'total_debit' => $jurnal->total_debit,
                    'total_kredit' => $jurnal->total_kredit,
                    'details' => $jurnal->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'coa_akun' => $detail->coa->nomor_akun ?? '',
                            'coa' => [
                                'nama_akun' => $detail->coa->nama_akun ?? '',
                                'nomor_akun' => $detail->coa->nomor_akun ?? ''
                            ],
                            'debit' => $detail->debit ?? 0,
                            'credit' => $detail->credit ?? 0,
                            'tanggal_bukti' => $detail->tanggal_bukti ? \Carbon\Carbon::parse($detail->tanggal_bukti)->format('Y-m-d') : ($jurnal->jurnal_tgl ? \Carbon\Carbon::parse($jurnal->jurnal_tgl)->format('Y-m-d') : null),
                            'lampiran' => $detail->lampiran ?? 'no-file.pdf',
                            'keterangan' => $detail->keterangan ?? ''
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data jurnal tidak ditemukan'
            ], 404);
        }
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
        if(auth()->user()->profile == 'trial' && auth()->user()->is_active == 0){
            Alert::error('Oops!', 'Masa trial anda sudah expired, Anda Tidak Bisa Membuat Jurnal');
            return redirect()->route('jurnal.index');
        }

        $coa = Coa::whereNull('is_deleted')
                    ->where('level', 5)
                    ->where('created_by', auth()->user()->id)
                    ->get()
                    ->toArray();
        return view('jurnal.form', compact('coa'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if(auth()->user()->profile == 'trial' && auth()->user()->is_active == 0){
            Alert::error('Oops!', 'Masa trial anda sudah expired, Anda Tidak Bisa Membuat Jurnal');
            return redirect()->route('jurnal.index');
        }

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

            if($sumDebit != $sumKredit || ($sumDebit - $sumKredit) != 0){
                Alert::error('Oops!', 'Debit tidak sama dengan kredit.');
                return redirect()->back();
            }

            #membuat angka dibelakang jenis
            $angka          = "1";
            $jenis          = $input['jenis'];
            $periode        = auth()->user()->periode;
            $tanggal_dibuat = $periode."-".date('m-d');
            $created_by     = Auth::user()->id;
            $query = DB::select("
                        SELECT COUNT(id) AS count
                        FROM jurnal_headers
                        WHERE is_deleted IS NULL
                        AND created_by = ?
                        AND jenis LIKE ?", [$created_by, $jenis. '%']);
            $count = ($query[0]->count)+$angka;

            $jurnal = Jurnal::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get();

            // da($jurnal);

            if ($jurnal->isNotEmpty()) {
                $countJenis = $jurnal->where('jenis', strtoupper($input['jenis']))->count();
                $input['no_transaksi'] = $countJenis + 1;
            } else {
                $input['no_transaksi'] = 1;
            }

            $dataJurnal = Jurnal::create([
                'jenis' => strtoupper($input['jenis']),
                'no_urut_transaksi' => $jurnal->count() + 1,
                // 'no_transaksi' => $input['no_transaksi'],
                'no_transaksi'      => $count,
                'jurnal_tgl'        => $tanggal_dibuat,
                'subtotal'          => $sumDebit,
                'keterangan'        => $input['keterangan_header'],
                'created_by'        => Auth::user()->id,
                'created_at'        => $tanggal_dibuat,
                'tgl_dibuat'        => now(),
                'periode'           => $periode
            ]);

            // dd($dataJurnal);

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
                    'jurnal_id'         => $dataJurnal->id,
                    'coa_akun'          => $coaAkun,
                    'debit'             => $debit,
                    'credit'            => $kredit,
                    'keterangan'        => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'tanggal_bukti'     => $tgl_bukti,
                    'created_by'        => Auth::user()->id,
                    'created_at'        => $tanggal_dibuat,
                    'tgl_dibuat'        => now(),
                    'periode'           => $periode
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
            // da($input);
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

    public function getByID($id)
    {
        echo "Lukman";
    }

    public function edit(Jurnal $jurnal)
    {

        $jurnal = Jurnal::with(['details.coa'])
                ->whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->where('periode', auth()->user()->periode)
                ->orderBy('tgl_dibuat', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('no_urut_transaksi', 'desc')
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


        // foreach($jurnal->details as $detail){
        //     if(substr($detail->coa_akun, 0, 1) === '1'){
        //         $detail->coa_akun = '0' . substr($detail->coa_akun, 1);
        //     }
        // }

        // da($jurnal);

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

            $periode        = auth()->user()->periode;
            $tanggal_dibuat = $periode."-".date('m-d');
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
            $periode        = auth()->user()->periode;
            $tanggal_dibuat = $periode."-".date('m-d');
            $jurnal->jenis = strtoupper($input['jenis']);
            $jurnal->no_transaksi = $input['no_transaksi'];
            $jurnal->jurnal_tgl = $tanggal_dibuat;
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
                $periode        = auth()->user()->periode;
                $tanggal_dibuat = $periode."-".date('m-d');
                $details[] = [
                    'jurnal_id' => $jurnal->id,
                    'coa_akun' => $coaAkun,
                    'debit' => $debit,
                    'credit' => $kredit,
                    'keterangan' => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'tanggal_bukti' => $tgl_bukti,
                    'created_by' => Auth::user()->id,
                    'created_at' => $tanggal_dibuat,
                    'tgl_diupdate' => $tanggal_dibuat,
                    'periode'       => $periode
                ];
            }

            foreach ($details as $index => $detail) {
                $da = JurnalDetail::insert($detail);

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
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            DB::rollback();
            Alert::error('Oops!', 'Gagal memperbarui jurnal: ' . $e->getMessage());
            return redirect();
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

        $countImport = 500;

        if(auth()->user()->profile == 'trial'){
            $countImport = 50;
        }elseif(auth()->user()->profile == 'standard'){
            $countImport = 100;
        }elseif(auth()->user()->profile == 'pro'){
            $countImport = 250;
        }else{
            $countImport = 500;
        }

        // da($importedData['success']);

        if ($importedData instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $importedData->getData(true);
            if (isset($responseData['success']) && !$responseData['success']) {
                return response()->json(['html' => 0, 'message' => $responseData['message']]);
            }
        } elseif (empty($importedData)) {
            return response()->json(['html' => 0, 'message' => 'Data impor kosong']);
        }else {
            $cek = "";
            if (count($importedData) > $countImport) {
                $cek = "Hanya " . $countImport . " data pertama yang di import.";
                $importedData = array_slice($importedData, 0, $countImport);
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

    /**
     * Export jurnal ke Excel dengan berbagai opsi filter
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportJurnal(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $jurnalType = $request->input('jurnal_type');
            $includeDetails = $request->input('include_details', true);
            $exportType = $request->input('export_type', 'excel'); // excel atau csv

            // Validasi tanggal jika disediakan
            if ($startDate && $endDate) {
                if (strtotime($startDate) > strtotime($endDate)) {
                    Alert::error('Error!', 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
                    return redirect()->back();
                }
            }

            // Buat nama file dengan timestamp
            $timestamp = now()->format('YmdHis');
            $fileName = 'jurnal_export_' . $timestamp;

            // Tambahkan info filter ke nama file
            if ($startDate && $endDate) {
                $fileName .= '_' . date('Ymd', strtotime($startDate)) . '_' . date('Ymd', strtotime($endDate));
            }
            if ($jurnalType) {
                $fileName .= '_' . strtolower($jurnalType);
            }
            $fileName .= $includeDetails ? '_detail' : '_header';

            // Buat export instance
            $export = new JurnalExport($startDate, $endDate, $jurnalType, $includeDetails);

            // Download berdasarkan tipe export
            if ($exportType === 'csv') {
                return Excel::download($export, $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
            } else {
                return Excel::download($export, $fileName . '.xlsx');
            }

        } catch (\Exception $e) {
            Log::error('Error saat export jurnal: ' . $e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat export jurnal: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Export jurnal dengan filter tanggal (method sederhana)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportJurnalByDate($startDate = null, $endDate = null)
    {
        try {
            $timestamp = now()->format('YmdHis');
            $fileName = 'jurnal_' . $timestamp . '.xlsx';

            $export = new JurnalExport($startDate, $endDate, null, true);
            return Excel::download($export, $fileName);

        } catch (\Exception $e) {
            Log::error('Error saat export jurnal by date: ' . $e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat export jurnal.');
            return redirect()->back();
        }
    }

    /**
     * Export semua jurnal (tanpa filter)
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportAllJurnal()
    {
        try {
            $timestamp = now()->format('YmdHis');
            $fileName = 'semua_jurnal_' . $timestamp . '.xlsx';

            $export = new JurnalExport(null, null, null, true);
            return Excel::download($export, $fileName);

        } catch (\Exception $e) {
            Log::error('Error saat export semua jurnal: ' . $e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat export jurnal.');
            return redirect()->back();
        }
    }

    public function totalJurnal()
    {
        $a      = auth()->user()->id;
        $users  = DB::select("
            SELECT
            COUNT(jh.id) as total
            FROM jurnal_headers jh

            WHERE
            jh.is_deleted IS NULL AND
            jh.created_by = ?
        ",[$a]);

        return response()->json([
            'status'     => 200,
            'message'    => 'Berhasil get data',
            'data'       => $users[0]->total,
        ]);
    }

    public function cekTrial()
    {
        $users = DB::select("
            SELECT id, trial_ends_at
            FROM users
            WHERE profile = ? AND is_active = ? AND trial_ends_at <= NOW()
        ", ['trial', 1]);

        $successCount = 0;

        foreach ($users as $value) {
            $data = [
                'is_active' => 0
            ];
            $updated = DB::table('users')->where('id', $value->id)->update($data);
            if ($updated) {
                $successCount++;
            }
        }

        if ($successCount > 0) {
            return response()->json([
                'status'  => 200,
                'message' => "{$successCount} data berhasil diupdate"
            ]);
        }

        return response()->json([
            'status'  => 400,
            'message' => 'Tidak ada data yang diupdate'
        ]);
    }

    public function view()
    {
        echo "Lukman";
    }
}

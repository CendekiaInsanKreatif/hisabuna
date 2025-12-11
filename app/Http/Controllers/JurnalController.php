<?php

namespace App\Http\Controllers;

use Alert;
use App\Exports\JurnalExport;
use App\Exports\JurnalSampleExport;
use App\Imports\JurnalDetailImport;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
            'created_at',
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
        try {
            // Debug authentication
            if (! auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                    'data' => [],
                    'total' => 0,
                ], 401);
            }

            $userId = auth()->id();
            $periode = auth()->user()->periode;

            // Get pagination parameters
            $perPage = min((int) $request->get('per_page', 50), 100); // Max 100, default 50
            $page = (int) $request->get('page', 1);

            // Build base query with optimized selects - NO eager loading
            $query = Jurnal::select([
                'id',
                'no_urut_transaksi',
                'jenis',
                'keterangan',
                'jurnal_tgl',
                'subtotal',
                'created_at',
            ])
                ->whereNull('is_deleted')
                ->where('created_by', $userId)
                ->where('periode', $periode);

            // Apply filters
            if ($request->filled('jenis') && $request->jenis !== 'all') {
                $query->where('jenis', strtoupper($request->jenis));
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('no_urut_transaksi', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('keterangan', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('jenis', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Month filter - optimized with index
            if ($request->filled('month') && $request->month !== 'all') {
                $query->whereRaw('DATE_FORMAT(jurnal_tgl, "%Y-%m") = ?', [$request->month]);
            }

            // Date range filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('jurnal_tgl', [
                    $request->start_date,
                    $request->end_date,
                ]);
            }

            // Get total count BEFORE pagination
            $totalCount = $query->count();

            // Apply ordering and pagination
            $jurnal = $query
                ->orderBy('id', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Calculate totals efficiently using DB aggregation (for filtered results only)
            // Use same filters as above
            $totalsQuery = DB::table('jurnal_details as jd')
                ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
                ->where('jh.created_by', $userId)
                ->where('jh.periode', $periode)
                ->whereNull('jh.is_deleted')
                ->whereNull('jd.is_deleted');

            // Apply same filters to totals query
            if ($request->filled('jenis') && $request->jenis !== 'all') {
                $totalsQuery->where('jh.jenis', strtoupper($request->jenis));
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $totalsQuery->where(function ($q) use ($searchTerm) {
                    $q->where('jh.no_urut_transaksi', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('jh.keterangan', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('jh.jenis', 'LIKE', "%{$searchTerm}%");
                });
            }

            if ($request->filled('month') && $request->month !== 'all') {
                $totalsQuery->whereRaw('DATE_FORMAT(jh.jurnal_tgl, "%Y-%m") = ?', [$request->month]);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $totalsQuery->whereBetween('jh.jurnal_tgl', [
                    $request->start_date,
                    $request->end_date,
                ]);
            }

            // Execute aggregation query - MUCH faster than looping
            $totals = $totalsQuery
                ->selectRaw('SUM(COALESCE(jd.debit, 0)) as total_debit')
                ->selectRaw('SUM(COALESCE(jd.credit, 0)) as total_credit')
                ->first();

            $totalDebit = (float) ($totals->total_debit ?? 0);
            $totalCredit = (float) ($totals->total_credit ?? 0);

            return response()->json([
                'status' => 'success',
                'data' => $jurnal,
                'total' => $totalCount,
                'totals' => [
                    'debit' => $totalDebit,
                    'credit' => $totalCredit,
                    'selisih' => $totalDebit - $totalCredit,
                ],
                'meta' => [
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => ceil($totalCount / $perPage),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getData: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id() ?? null,
                'filters' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data jurnal: '.$e->getMessage(),
                'data' => [],
                'total' => 0,
            ], 500);
        }
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
                                'nomor_akun' => $detail->coa->nomor_akun ?? '',
                            ],
                            'debit' => $detail->debit ?? 0,
                            'credit' => $detail->credit ?? 0,
                            'tanggal_bukti' => $detail->tanggal_bukti ? \Carbon\Carbon::parse($detail->tanggal_bukti)->format('Y-m-d') : ($jurnal->jurnal_tgl ? \Carbon\Carbon::parse($jurnal->jurnal_tgl)->format('Y-m-d') : null),
                            'lampiran' => $detail->lampiran ?? 'no-file.pdf',
                            'keterangan' => $detail->keterangan ?? '',
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data jurnal tidak ditemukan',
            ], 404);
        }
    }

    // /**
    //  * Get total statistics (debit, credit, count) efficiently
    //  * Use this for dashboard or summary displays
    //  */
    // public function getTotals(Request $request)
    // {
    //     try {
    //         $userId = auth()->id();
    //         $periode = auth()->user()->periode;

    //         // Build query for headers count
    //         $headersQuery = Jurnal::whereNull('is_deleted')
    //             ->where('created_by', $userId)
    //             ->where('periode', $periode);

    //         // Apply same filters
    //         if ($request->filled('jenis') && $request->jenis !== 'all') {
    //             $headersQuery->where('jenis', strtoupper($request->jenis));
    //         }

    //         if ($request->filled('month') && $request->month !== 'all') {
    //             $headersQuery->whereRaw('DATE_FORMAT(jurnal_tgl, "%Y-%m") = ?', [$request->month]);
    //         }

    //         $totalJurnal = $headersQuery->count();

    //         // Get totals from details
    //         $totalsQuery = DB::table('jurnal_details as jd')
    //             ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
    //             ->where('jh.created_by', $userId)
    //             ->where('jh.periode', $periode)
    //             ->whereNull('jh.is_deleted')
    //             ->whereNull('jd.is_deleted');

    //         // Apply filters
    //         if ($request->filled('jenis') && $request->jenis !== 'all') {
    //             $totalsQuery->where('jh.jenis', strtoupper($request->jenis));
    //         }

    //         if ($request->filled('month') && $request->month !== 'all') {
    //             $totalsQuery->whereRaw('DATE_FORMAT(jh.jurnal_tgl, "%Y-%m") = ?', [$request->month]);
    //         }

    //         $totals = $totalsQuery
    //             ->selectRaw('SUM(COALESCE(jd.debit, 0)) as total_debit')
    //             ->selectRaw('SUM(COALESCE(jd.credit, 0)) as total_credit')
    //             ->selectRaw('COUNT(DISTINCT jd.id) as total_entries')
    //             ->first();

    //         $totalDebit = (float) ($totals->total_debit ?? 0);
    //         $totalCredit = (float) ($totals->total_credit ?? 0);

    //         return response()->json([
    //             'status' => 'success',
    //             'data' => [
    //                 'total_jurnal' => $totalJurnal,
    //                 'total_entries' => (int) ($totals->total_entries ?? 0),
    //                 'total_debit' => $totalDebit,
    //                 'total_credit' => $totalCredit,
    //                 'selisih' => $totalDebit - $totalCredit,
    //                 'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
    //             ],
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Error in getTotals: '.$e->getMessage());

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Gagal memuat total',
    //             'data' => null,
    //         ], 500);
    //     }
    // }

    public function lampiran(Jurnal $jurnal)
    {
        $lampiran = Storage::disk('public')->files('lampiran/'.$jurnal->id);

        return view('report.views.lampiran', compact('jurnal', 'lampiran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->profile == 'trial' && auth()->user()->is_active == 0) {
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
        if (auth()->user()->profile == 'trial' && auth()->user()->is_active == 0) {
            Alert::error('Oops!', 'Masa trial anda sudah expired, Anda Tidak Bisa Membuat Jurnal');

            return redirect()->route('jurnal.index');
        }

        DB::beginTransaction();
        try {
            $input = $request->all();
            $debit = array_map(function ($x) {
                return strpos($x, '.') !== false ? (int) str_replace('.', '', $x) : (int) $x;
            }, $input['debit']);
            $kredit = array_map(function ($x) {
                return strpos($x, '.') !== false ? (int) str_replace('.', '', $x) : (int) $x;
            }, $input['kredit']);

            $sumDebit = array_sum($debit);
            $sumKredit = array_sum($kredit);

            if ($sumDebit != $sumKredit || ($sumDebit - $sumKredit) != 0) {
                Alert::error('Oops!', 'Debit tidak sama dengan kredit.');

                return redirect()->back();
            }

            // membuat angka dibelakang jenis
            $angka = '1';
            $jenis = $input['jenis'];
            $periode = auth()->user()->periode;
            $tanggal_dibuat = $periode.'-'.date('m-d');
            $created_by = Auth::user()->id;
            $query = DB::select('
                        SELECT COUNT(id) AS count
                        FROM jurnal_headers
                        WHERE is_deleted IS NULL
                        AND created_by = ?
                        AND jenis LIKE ?', [$created_by, $jenis.'%']);
            $count = ($query[0]->count) + $angka;

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
                'no_transaksi' => $count,
                'jurnal_tgl' => $tanggal_dibuat,
                'subtotal' => $sumDebit,
                'keterangan' => $input['keterangan_header'],
                'created_by' => Auth::user()->id,
                'created_at' => $tanggal_dibuat,
                'tgl_dibuat' => now(),
                'periode' => $periode,
            ]);

            // dd($dataJurnal);

            $details = [];
            foreach ($input['no_akun'] as $index => $noAkun) {
                if (strpos($noAkun, '-')) {
                    $coaAkun = str_replace('-', '', $noAkun);
                } else {
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
                    'created_at' => $tanggal_dibuat,
                    'tgl_dibuat' => now(),
                    'periode' => $periode,
                ];
            }

            foreach (array_chunk($details, 1000) as $chunk) {
                JurnalDetail::insert($chunk);
            }

            if ($request->hasFile('lampiran')) {
                $lampiranFiles = $request->file('lampiran');
                foreach ($lampiranFiles as $index => $file) {
                    $filePath = 'lampiran/'.auth()->user()->company_name.'/'.$dataJurnal->id;
                    $fileName = $details[$index]['id'].'.'.$file->getClientOriginalExtension();
                    $file->storeAs($filePath, $fileName, 'public');
                    JurnalDetail::where('id', $details[$index]['id'])->update(['lampiran' => $filePath.'/'.$fileName]);
                }
            }

            DB::commit();
            // da($input);
            Log::info('Jurnal berhasil dibuat.', ['jurnal_id' => $dataJurnal->id]);
            Alert::success('Sukses!', 'Jurnal berhasil dibuat.');

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal membuat jurnal: '.$e->getMessage());
            Alert::error('Oops!', 'Gagal membuat jurnal: '.$e->getMessage());

            return redirect()->back();
        }
    }

    public function getByID($id)
    {
        echo 'Lukman';
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

        // Get all COA for lookup (level 5 for transactional accounts)
        $coa = Coa::whereNull('is_deleted')
            ->where('created_by', auth()->user()->id)
            ->where('level', 5)
            ->get();
        
        // Create a COA lookup map by nomor_akun
        $coaMap = $coa->keyBy('nomor_akun');

        // Transform details to include nama_akun explicitly for JavaScript
        // This is necessary because dynamically set attributes on Eloquent models
        // don't serialize to JSON unless they're in $appends
        $transformedDetails = [];
        if ($jurnal && $jurnal->details) {
            foreach ($jurnal->details as $detail) {
                $namaAkun = '';
                
                // Try to get nama_akun from COA lookup
                if ($detail->coa_akun) {
                    $foundCoa = $coaMap->get($detail->coa_akun);
                    if ($foundCoa) {
                        $namaAkun = $foundCoa->nama_akun;
                    }
                }
                
                // Fallback to eager-loaded relationship
                if (empty($namaAkun) && $detail->coa && $detail->coa->nama_akun) {
                    $namaAkun = $detail->coa->nama_akun;
                }
                
                $transformedDetails[] = [
                    'id' => $detail->id,
                    'jurnal_id' => $detail->jurnal_id,
                    'coa_akun' => $detail->coa_akun,
                    'nama_akun' => $namaAkun, // Explicitly include nama_akun
                    'debit' => $detail->debit,
                    'credit' => $detail->credit,
                    'kredit' => $detail->credit, // Alias for frontend compatibility
                    'keterangan' => $detail->keterangan,
                    'tanggal_bukti' => $detail->tanggal_bukti ? \Carbon\Carbon::parse($detail->tanggal_bukti)->format('Y-m-d') : null,
                    'lampiran' => $detail->lampiran,
                    'coa' => $detail->coa ? [
                        'nomor_akun' => $detail->coa->nomor_akun,
                        'nama_akun' => $detail->coa->nama_akun,
                    ] : null,
                ];
            }
        }
        
        // Create a new object to pass to the view with transformed details
        $jurnalData = null;
        if ($jurnal) {
            $jurnalData = new \stdClass();
            $jurnalData->id = $jurnal->id;
            $jurnalData->no_urut_transaksi = $jurnal->no_urut_transaksi;
            $jurnalData->no_transaksi = $jurnal->no_transaksi;
            $jurnalData->jurnal_tgl = $jurnal->jurnal_tgl;
            $jurnalData->jenis = $jurnal->jenis;
            $jurnalData->keterangan = $jurnal->keterangan;
            $jurnalData->subtotal = $jurnal->subtotal;
            $jurnalData->details = $transformedDetails;
        }

        return view('jurnal.form', ['jurnal' => $jurnalData, 'coa' => $coa]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        // dd($request->all());

        $debit = array_map(function ($x) {
            return (int) str_replace('.', '', $x);
        }, $request['debit']);
        $kredit = array_map(function ($x) {
            return (int) str_replace('.', '', $x);
        }, $request['kredit']);
        $sumDebit = array_sum($debit);
        $sumKredit = array_sum($kredit);

        if ($sumDebit != $sumKredit || $sumDebit - $sumKredit != 0) {
            Alert::error('Oops!', 'Debit tidak sama dengan kredit.');

            return redirect()->back();
        }

        // da($request->all());

        DB::beginTransaction();
        try {
            $input = $request->all();

            $periode = auth()->user()->periode;
            $tanggal_dibuat = $periode.'-'.date('m-d');
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
            $periode = auth()->user()->periode;
            $tanggal_dibuat = $periode.'-'.date('m-d');
            $jurnal->jenis = strtoupper($input['jenis']);
            $jurnal->no_transaksi = $input['no_transaksi'];
            $jurnal->jurnal_tgl = $tanggal_dibuat;
            $jurnal->subtotal = $sumDebit;
            $jurnal->keterangan = $input['keterangan_header'];
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->updated_at = now();
            $jurnal->save();

            // da($input);

            if ($request->has('lampiran')) {
                $filePath = 'lampiran/'.auth()->user()->company_name.'/'.$jurnal->id;
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
                $periode = auth()->user()->periode;
                $tanggal_dibuat = $periode.'-'.date('m-d');
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
                    'periode' => $periode,
                ];
            }

            foreach ($details as $index => $detail) {
                $da = JurnalDetail::insert($detail);

                if ($request->hasFile('lampiran')) {
                    $lampiranFiles = $request->file('lampiran');
                    if (isset($lampiranFiles[$index])) {
                        $file = $lampiranFiles[$index];
                        $filePath = 'lampiran/'.auth()->user()->company_name.'/'.$jurnal->id;
                        $fileName = $da->id.'.'.$file->getClientOriginalExtension();
                        $file->storeAs($filePath, $fileName, 'public');
                        $da->lampiran = $filePath.'/'.$fileName;
                        $da->save();
                    }
                }
            }

            DB::commit();

            Alert::success('Sukses!', 'Jurnal berhasil diperbarui.');

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            DB::rollback();
            Alert::error('Oops!', 'Gagal memperbarui jurnal: '.$e->getMessage());

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
            'file' => 'required|mimes:xlsx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $path = $request->file('file')->store('temp');

        try {
            $import = new JurnalDetailImport;
            $data = Excel::toCollection($import, $path);
            unset($data[1]);

            // OPTIMIZATION: Batch COA lookup to eliminate N+1 queries
            $coaNumbers = [];
            foreach ($data[0] as $row) {
                if ($row['akun_coa'] !== null && strpos($row['akun_coa'], '|') === false) {
                    $akun_coa = str_replace('-', '', $row['akun_coa']);
                    $coaNumbers[] = $akun_coa;
                }
            }

            // Single query to fetch all COAs at once
            $coas = Coa::where('created_by', auth()->user()->id)
                ->whereIn('nomor_akun', array_unique($coaNumbers))
                ->get()
                ->keyBy('nomor_akun');

            $detail = [];
            foreach ($data[0] as $row) {
                if ($row['akun_coa'] !== null) {
                    if (strpos($row['akun_coa'], '|') !== false) {
                        $akun = explode('|', $row['akun_coa']);
                    } else {
                        $akun_coa = str_replace('-', '', $row['akun_coa']);

                        // Use pre-fetched COA from batch query
                        if (! isset($coas[$akun_coa])) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Akun COA: '.$row['akun_coa'].' tidak ditemukan.',
                            ], 500);
                        }

                        $coa = $coas[$akun_coa];
                        $akun[0] = $coa->nomor_akun;
                        $akun[1] = $coa->nama_akun;
                    }

                    // Format nomor akun dengan dash menggunakan helper (111-01-002)
                    $formattedNomorAkun = formatNomorAkun($akun[0]);

                    $detail[] = [
                        'no_akun' => $formattedNomorAkun,
                        'nama_akun' => $akun[1],
                        'debit' => (int) $row['debit'],
                        'kredit' => (int) $row['kredit'],
                        'keterangan' => $row['keterangan'],
                        'tanggal_bukti' => \Carbon\Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($row['tanggal_bukti'] - 25569) * 86400))->format('Y-m-d'),
                    ];
                }
            }

            // Clean up temporary file
            \Storage::delete($path);

            return $detail;

        } catch (\Exception $e) {
            // Clean up temporary file on error
            if (isset($path) && \Storage::exists($path)) {
                \Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error importing data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function importHtml(Request $request)
    {
        $file = $request->file('file');
        $importedData = $this->import($request);

        // UNLIMITED IMPORT - No more limits based on profile
        // Pagination will be handled on frontend for better UX

        if ($importedData instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $importedData->getData(true);
            if (isset($responseData['success']) && ! $responseData['success']) {
                return response()->json(['html' => 0, 'message' => $responseData['message']]);
            }
        } elseif (empty($importedData)) {
            return response()->json(['html' => 0, 'message' => 'Data impor kosong']);
        } else {
            $totalRows = count($importedData);
            $message = "Berhasil mengimport {$totalRows} baris data. Gunakan pagination untuk navigasi.";

            return response()->json([
                'html' => $importedData,
                'message' => $message,
                'total' => $totalRows,
            ]);
        }
    }

    public function sampleExport()
    {
        return Excel::download(new JurnalSampleExport, 'jurnal_sample.xlsx');
    }

    /**
     * Export jurnal ke Excel dengan berbagai opsi filter
     *
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
            $fileName = 'jurnal_export_'.$timestamp;

            // Tambahkan info filter ke nama file
            if ($startDate && $endDate) {
                $fileName .= '_'.date('Ymd', strtotime($startDate)).'_'.date('Ymd', strtotime($endDate));
            }
            if ($jurnalType) {
                $fileName .= '_'.strtolower($jurnalType);
            }
            $fileName .= $includeDetails ? '_detail' : '_header';

            // Buat export instance
            $export = new JurnalExport($startDate, $endDate, $jurnalType, $includeDetails);

            // Download berdasarkan tipe export
            if ($exportType === 'csv') {
                return Excel::download($export, $fileName.'.csv', \Maatwebsite\Excel\Excel::CSV);
            } else {
                return Excel::download($export, $fileName.'.xlsx');
            }

        } catch (\Exception $e) {
            Log::error('Error saat export jurnal: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat export jurnal: '.$e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Export jurnal dengan filter tanggal (method sederhana)
     *
     * @param  string|null  $startDate
     * @param  string|null  $endDate
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportJurnalByDate($startDate = null, $endDate = null)
    {
        try {
            $timestamp = now()->format('YmdHis');
            $fileName = 'jurnal_'.$timestamp.'.xlsx';

            $export = new JurnalExport($startDate, $endDate, null, true);

            return Excel::download($export, $fileName);

        } catch (\Exception $e) {
            Log::error('Error saat export jurnal by date: '.$e->getMessage());
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
            $fileName = 'semua_jurnal_'.$timestamp.'.xlsx';

            $export = new JurnalExport(null, null, null, true);

            return Excel::download($export, $fileName);

        } catch (\Exception $e) {
            Log::error('Error saat export semua jurnal: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat export jurnal.');

            return redirect()->back();
        }
    }

    public function totalJurnal()
    {
        $a = auth()->user()->id;
        $users = DB::select('
            SELECT
            COUNT(jh.id) as total
            FROM jurnal_headers jh

            WHERE
            jh.is_deleted IS NULL AND
            jh.created_by = ?
        ', [$a]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil get data',
            'data' => $users[0]->total,
        ]);
    }

    /**
     * Get available months based on user's periode
     */
    public function getAvailableMonths()
    {
        try {
            $periode = auth()->user()->periode;
            $months = [];

            // Generate 12 bulan untuk periode yang dipilih
            $monthNames = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ];

            foreach ($monthNames as $monthNum => $monthName) {
                $months[] = [
                    'value' => $periode.'-'.$monthNum,
                    'label' => $monthName.' '.$periode,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $months,
                'periode' => $periode,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data bulan: '.$e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function cekTrial()
    {
        $users = DB::select('
            SELECT id, trial_ends_at
            FROM users
            WHERE profile = ? AND is_active = ? AND trial_ends_at <= NOW()
        ', ['trial', 1]);

        $successCount = 0;

        foreach ($users as $value) {
            $data = [
                'is_active' => 0,
            ];
            $updated = DB::table('users')->where('id', $value->id)->update($data);
            if ($updated) {
                $successCount++;
            }
        }

        if ($successCount > 0) {
            return response()->json([
                'status' => 200,
                'message' => "{$successCount} data berhasil diupdate",
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Tidak ada data yang diupdate',
        ]);
    }

    public function view()
    {
        echo 'Lukman';
    }

    /**
     * Get total statistics (debit, credit, count) efficiently
     * Use this for dashboard or summary displays
     */
    public function getTotals(Request $request)
    {
        try {
            $userId = auth()->id();
            $periode = auth()->user()->periode;

            // Build query for headers count
            $headersQuery = Jurnal::whereNull('is_deleted')
                ->where('created_by', $userId)
                ->where('periode', $periode);

            // Apply same filters
            if ($request->filled('jenis') && $request->jenis !== 'all') {
                $headersQuery->where('jenis', strtoupper($request->jenis));
            }

            if ($request->filled('month') && $request->month !== 'all') {
                $headersQuery->whereRaw('DATE_FORMAT(jurnal_tgl, "%Y-%m") = ?', [$request->month]);
            }

            $totalJurnal = $headersQuery->count();

            // Get totals from details
            $totalsQuery = DB::table('jurnal_details as jd')
                ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
                ->where('jh.created_by', $userId)
                ->where('jh.periode', $periode)
                ->whereNull('jh.is_deleted')
                ->whereNull('jd.is_deleted');

            // Apply filters
            if ($request->filled('jenis') && $request->jenis !== 'all') {
                $totalsQuery->where('jh.jenis', strtoupper($request->jenis));
            }

            if ($request->filled('month') && $request->month !== 'all') {
                $totalsQuery->whereRaw('DATE_FORMAT(jh.jurnal_tgl, "%Y-%m") = ?', [$request->month]);
            }

            $totals = $totalsQuery
                ->selectRaw('SUM(COALESCE(jd.debit, 0)) as total_debit')
                ->selectRaw('SUM(COALESCE(jd.credit, 0)) as total_credit')
                ->selectRaw('COUNT(DISTINCT jd.id) as total_entries')
                ->first();

            $totalDebit = (float) ($totals->total_debit ?? 0);
            $totalCredit = (float) ($totals->total_credit ?? 0);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_jurnal' => $totalJurnal,
                    'total_entries' => (int) ($totals->total_entries ?? 0),
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'selisih' => $totalDebit - $totalCredit,
                    'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getTotals: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat total',
                'data' => null,
            ], 500);
        }
    }
}

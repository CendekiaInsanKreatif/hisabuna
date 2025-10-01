<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Alert;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Saldo;
use App\Models\User;
use App\Exports\NeracaExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as Dompdf;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;


class ReportController extends Controller
{
    public function printJurnalFilter(Request $request)
    {
        // da("fafa");
        $dari = $request->a;
        $sampai = $request->b;
        $limit = $sampai - $dari + 1; // Jumlah baris yang akan diambil
        $offset = $dari - 1; // Offset baris yang akan dilewati

        // dd($request->all());
        // Ambil ID pengguna
        $a = auth()->user()->id;

        // dd(auth()->user()->periode, $a, $limit, $offset);

        // Query untuk mendapatkan data jurnal
        $users = DB::select("
            SELECT *
            FROM jurnal_headers jh
            WHERE
                jh.is_deleted IS NULL AND jh.periode = ? AND
                jh.created_by = ?
            ORDER BY jh.id ASC
            LIMIT ? OFFSET ?", [auth()->user()->periode, $a, $limit, $offset]);

        // dd($users);
        // Periksa jika data kosong
        if (empty($users)) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Inisialisasi tanggal awal dan akhir
        $tgl_awal = $tgl_akhir = null;

        // Iterasi untuk menemukan tanggal awal dan akhir
        foreach ($users as $user) {
            if ($tgl_awal === null || $user->jurnal_tgl < $tgl_awal) {
                $tgl_awal = $user->jurnal_tgl;
            }
            if ($tgl_akhir === null || $user->jurnal_tgl > $tgl_akhir) {
                $tgl_akhir = $user->jurnal_tgl;
            }
        }

        // Render view untuk PDF
        $view = view('report.daftarjurnal', [
            'jurnal' => $users,
            'tgl_awal' => $dari,
            'tgl_akhir' => $sampai
        ])->render();

        // Buat dan unduh PDF
        $pdf = Dompdf::loadHTML($view);
        return $pdf->download('daftar_jurnal_' . Carbon::now()->format('YmdHis') . '.pdf');
    }

    public function daftarJurnal()
    {
        $jurnal = Jurnal::with('details')->where('created_by', auth()->user()->id)->get();
        $tgl_awal = $jurnal->min('jurnal_tgl');
        $tgl_akhir = $jurnal->max('jurnal_tgl');

        // da("faa");

        if($jurnal->isEmpty()){
            Alert::error('Oops!', 'Data tidak ditemukan');
            return redirect()->back();
        }

        $view = view('report.daftarjurnal', ['jurnal' => $jurnal, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir])->render();
        $pdf = Dompdf::loadHTML($view);
        return $pdf->download('daftar_jurnal_'.Carbon::now()->format('YmdHis').'.pdf');
    }

    public function transaksi($id) {
        $jurnal = Jurnal::with(['details' => function($query) {
            $query->orderBy('coa_akun');
        }])->where(['id' => $id, 'created_by' => auth()->user()->id])->first();

        if ($jurnal) {
            $coaList = Coa::where(['created_by' => auth()->user()->id])->get()->keyBy('nomor_akun');

            $jurnalDetails = JurnalDetail::where('jurnal_id', $id)
                ->where('created_by', auth()->user()->id)
                ->get()
                ->groupBy(function($detail) {
                    return substr($detail->coa_akun, 0, 5);
                });

            foreach ($jurnal['details'] as $key => $detail) {
                $coaAkun = $detail['coa_akun'];
                $child = $coaList->get($coaAkun);

                if ($child) {
                    $jurnal['details'][$key]['nama_akun'] = $child['nama_akun'];
                }

                $parent = substr($coaAkun, 0, 5);
                $coa = $coaList->get($parent);

                if ($coa) {
                    $noKun = formatNomorAkun($coa['nomor_akun']);
                    $get = explode('-', $noKun);
                    $deNo = $parent;

                    $total = 0;
                    if ($coa->saldo_normal == 'db' || $coa->saldo_normal == 'debit') {
                        $total = $jurnalDetails->get($deNo)->sum(function($detail) {
                            return $detail->debit - $detail->credit;
                        });
                    } else {
                        $total = $jurnalDetails->get($deNo)->sum(function($detail) {
                            return $detail->credit - $detail->debit;
                        });
                    }

                    $jurnal['details'][$key]['parent'] = [
                        'nomor_akun' => $get[0] . '-' . $get[1],
                        'nama_akun' => $coa['nama_akun'],
                        'total' => $total ?: 0,
                    ];
                }
            }
        }

        // da($jur)
        return view('report.transaksi', ['jurnal' => $jurnal]);

        // da($jurnal);
        // $pdf = Dompdf::loadView('report.transaksi', ['jurnal' => $jurnal]);
        // return $pdf->download('transaksi_jurnal_' . $id . '_' . Carbon::now()->format('YmdHis') . '.pdf');
    }


    // public function downloadBukuBesar(Request $request){

    // }

    public function bukuBesar(Request $request)
    {
        if (!$request->isMethod('post')) {
            $coa = Coa::whereNull('is_deleted')
                ->where('level', 5)
                ->where('created_by', auth()->id())
                ->select('nomor_akun', 'nama_akun')
                ->get()
                ->toArray();

            return view('report.views.template', compact('coa'));
        }

        $userId = auth()->id();

        $defaultDates = null;
        if (!$request->filled('start_date') || !$request->filled('end_date')) {
            $defaultDates = JurnalDetail::where('created_by', $userId)
                ->selectRaw('MIN(tanggal_bukti) as min_date, MAX(tanggal_bukti) as max_date')
                ->first();
        }

        $startInput = $request->input('start_date')
            ?: ($defaultDates?->min_date ? Carbon::parse($defaultDates->min_date)->format('d-m-Y') : now()->format('d-m-Y'));
        $endInput = $request->input('end_date')
            ?: ($defaultDates?->max_date ? Carbon::parse($defaultDates->max_date)->format('d-m-Y') : now()->format('d-m-Y'));

        try {
            $tanggalMulai = Carbon::createFromFormat('d-m-Y', trim($startInput))->format('Y-m-d');
            $tanggalSelesai = Carbon::createFromFormat('d-m-Y', trim($endInput))->format('Y-m-d');
        } catch (\Throwable $e) {
            $tanggalMulai = now()->format('Y-m-d');
            $tanggalSelesai = now()->format('Y-m-d');
        }

        $akun = (string) $request->input('akun', '');
        if ($akun !== '') {
            $akun = str_replace('-', '', $akun);
        }

        $baseRange = JurnalDetail::withoutGlobalScopes()
            ->from('jurnal_details as jd')
            ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
            ->where('jh.created_by', $userId)
            ->whereBetween('jd.tanggal_bukti', [$tanggalMulai.' 00:00:00', $tanggalSelesai.' 23:59:59']);

        if ($akun !== '') {
            $baseRange->where('jd.coa_akun', 'like', '%'.$akun.'%');
        }

        $accounts = (clone $baseRange)
            ->select('jd.coa_akun')
            ->distinct()
            ->orderBy('jd.coa_akun')
            ->pluck('jd.coa_akun')
            ->filter()
            ->values();

        if ($accounts->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan untuk rentang/akun tersebut'], 404);
        }

        $coas = Coa::where('created_by', $userId)
            ->whereIn('nomor_akun', $accounts)
            ->get(['nomor_akun', 'nama_akun', 'saldo_normal', 'saldo_awal_debit', 'saldo_awal_credit'])
            ->keyBy('nomor_akun');

        $aggAwal = DB::table('jurnal_details')
            ->select('coa_akun',
                DB::raw('SUM(debit) as sum_debit'),
                DB::raw('SUM(credit) as sum_credit')
            )
            ->where('created_by', $userId)
            ->where('tanggal_bukti', '<', $tanggalMulai.' 00:00:00')
            ->whereIn('coa_akun', $accounts)
            ->groupBy('coa_akun')
            ->get()
            ->keyBy('coa_akun');

        $ledgers = [];

        foreach ($accounts as $acc) {
            $coaRow = $coas->get($acc);
            if (!$coaRow) {
                continue;
            }

            $isDebitNormal = in_array($coaRow->saldo_normal, ['db', 'debit'], true);
            $awalAgg = $aggAwal->get($acc);
            $sumD = (float)($awalAgg->sum_debit ?? 0);
            $sumC = (float)($awalAgg->sum_credit ?? 0);
            $saldoStatik = $isDebitNormal ? (float)($coaRow->saldo_awal_debit ?? 0) : (float)($coaRow->saldo_awal_credit ?? 0);
            $saldoPer = $saldoStatik + ($isDebitNormal ? ($sumD - $sumC) : ($sumC - $sumD));
            $running = $saldoPer;

            $accountTx = new Collection();

            // da($accountTx)

            $cursor = JurnalDetail::withoutGlobalScopes()
                ->from('jurnal_details as jd')
                ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
                ->where('jh.created_by', $userId)
                ->where('jd.coa_akun', $acc)
                ->whereBetween('jd.tanggal_bukti', [$tanggalMulai.' 00:00:00', $tanggalSelesai.' 23:59:59'])
                ->orderBy('jd.tanggal_bukti')
                ->orderBy('jd.id')
                ->cursor();

            foreach ($cursor as $r) {
                $debit = (float) $r->debit;
                $credit = (float) $r->credit;
                $running += $isDebitNormal ? ($debit - $credit) : ($credit - $debit);

                $r->coa = (object)[
                    'nama_akun'   => $coaRow->nama_akun,
                    'saldo_normal'=> ($coaRow->saldo_normal === 'db') ? 'debit' :
                                     (($coaRow->saldo_normal === 'cr') ? 'credit' : $coaRow->saldo_normal),
                ];
                $r->saldo = $running;

                $accountTx->push($r);
            }

            $accountTx->saldo_per_tanggal = $saldoPer;

            $ledgers[$acc] = $accountTx;
        }

        $ledgers = collect($ledgers)->sortKeys();

        @set_time_limit(900);
        @ini_set('memory_limit', '1536M');

        SnappyPDF::setBinary('/usr/bin/wkhtmltopdf');

        // da($ledgers);
        // da($akun);
        // da($tanggalSelesai);
        // da($tanggalMulai);

        $pdf = SnappyPDF::loadView('report.bukubesar_download', [
                'ledgers'        => $ledgers,
                'tanggalMulai'   => $tanggalMulai,
                'tanggalSelesai' => $tanggalSelesai,
                'akun'           => $akun,
            ])
            ->setPaper('A4')
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 8)
            ->setOption('margin-bottom', 12)
            ->setOption('margin-left', 8)
            ->setOption('print-media-type', true)
            ->setOption('enable-local-file-access', true);

        $relOut = 'pdf/buku-besar-'.now()->format('Ymd-His').'.pdf';
        Storage::put($relOut, $pdf->output());

        if (config('app.use_x_accel', env('USE_X_ACCEL', false))) {
            return response('')
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="buku-besar.pdf"')
                ->header('X-Accel-Redirect', '/protected/'.$relOut);
        }

        return response()->file(
            Storage::path($relOut),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="buku-besar.pdf"',
            ]
        );

    }

    private function array_keys_to_lowercase_recursive($array)
    {
        $lowercasedArray = [];
        foreach ($array as $key => $value) {
            $lowerKey = strtolower($key);
            if (is_array($value)) {
                $lowercasedArray[$lowerKey] = $this->array_keys_to_lowercase_recursive($value);
            } else {
                $lowercasedArray[$lowerKey] = $value;
            }
        }
        return $lowercasedArray;
    }

    public function array_change_key_case_recursive($array, $case = CASE_LOWER)
    {
        $array = array_change_key_case($array, $case);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->array_change_key_case_recursive($value, $case);
            }
        }

        return $array;
    }

    //ini skrip arus kas tanggal 01-12-2024
    // public function arusKas(Request $request)
    // {
    //     if ($request->isMethod('post')) {
    //         $validated = $request->validate([
    //             'start_date' => 'required|date',
    //             'end_date' => 'required|date',
    //             'dibuat' => 'required|string|max:255',
    //             'alamat' => 'nullable|string|max:255',
    //             'tanggal' => 'nullable|date',
    //             'jabatan' => 'nullable|string|max:255',
    //             'jumlahLaman' => 'nullable|integer|min:1',
    //         ]);

    //         $start_date = Carbon::parse($validated['start_date'])->startOfDay()->format('Y-m-d H:i:s');
    //         $end_date = Carbon::parse($validated['end_date'])->endOfDay()->format('Y-m-d H:i:s');

    //         $jurnal = Jurnal::whereNull('is_deleted')
    //             ->with(['details' => function($query) use ($start_date, $end_date) {
    //                 $query->where('coa_akun', '>', '1')
    //                     ->whereBetween('tanggal_bukti', [$start_date, $end_date])
    //                     ->orderBy('coa_akun');
    //             }])
    //             ->where('created_by', auth()->user()->id)
    //             ->get();

    //         if ($jurnal->isEmpty()) {
    //             Alert::error('Oops!', 'Data tidak ditemukan');
    //             return redirect()->back();
    //         }

    //         $coas = Coa::where('created_by', auth()->user()->id)
    //             ->where('nomor_akun', 'not like', '111%')
    //             ->orderBy('nomor_akun')
    //             ->get()
    //             ->keyBy('nomor_akun');

    //         $data = [];
    //         $totalKas = 0;
    //         foreach ($jurnal as $entry) {
    //             foreach ($entry->details as $detail) {
    //                 $lv5 = $coas->get(substr($detail->coa_akun, 0, 8));
    //                 $aruskas = $coas->get(substr($detail->coa_akun, 0, 5));

    //                 if ($lv5) {
    //                     $nilai = (in_array(strtolower($lv5->saldo_normal), ['db', 'debit']))
    //                         ? $detail->debit - $detail->credit
    //                         : $detail->credit - $detail->debit;

    //                     $kategori = $aruskas->arus_kas ?? null;

    //                     if ($kategori) {
    //                         $data[$kategori][$aruskas->nama_akun] = ($data[$kategori][$aruskas->nama_akun] ?? 0) + $nilai;
    //                     }
    //                 }
    //                 if (strpos($detail->coa_akun, '1110') === 0) {
    //                     $totalKas += $detail->debit - $detail->credit;
    //                 }
    //             }
    //         }

    //         $getKas = $this->array_change_key_case_recursive($this->neracaFunc($end_date), CASE_LOWER);
    //         $kasAwal = $getKas[date('Y') - 1][1]['aset lancar']['kas dan setara kas'] ?? 0;
    //         $kasAkhir = $getKas[date('Y')][1]['aset lancar']['kas dan setara kas'] ?? 0;

    //         foreach ($data as $key => $value) {
    //             $data[$key]['Total'] = array_sum($value);
    //         }
    //         $data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'] = $totalKas;
    //         $data['Total']['Kas dan Setara Kas Awal'] = $kasAwal;
    //         $data['Total']['Kas dan Setara Kas Akhir'] = $kasAkhir;

    //         $dataChunked = collect($data)->chunk(4);

    //         $pdf = Dompdf::loadView('report.aruskas', [
    //             'dataChunked' => $dataChunked,
    //             'start_date' => Carbon::parse($start_date)->format('d/m/Y'),
    //             'end_date' => Carbon::parse($end_date)->format('d/m/Y'),
    //             'paged' => [
    //                 'dibuat' => $validated['dibuat'],
    //                 'alamat' => $validated['alamat'],
    //                 'tanggal' => $validated['tanggal'],
    //                 'jabatan' => $validated['jabatan'],
    //                 'jumlahLaman' => $validated['jumlahLaman'],
    //             ],
    //         ]);
    //         $pdf->setPaper('A4', 'portrait');

    //         return $pdf->stream('aruskas.pdf');
    //     }

    //     return view('report.views.template');
    // }

    //ini skrip awal tanggal 30-11-2024
    public function arusKas(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('report.views.template');
        }

        $start_date_input = $request->input('start_date');
        $end_date_input   = $request->input('end_date');

        if (!$start_date_input || !$end_date_input) {
            // Alert::error('Oops!', 'Data tidak ditemukan');
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Parse tanggal aman
        try {
            $start = Carbon::parse($start_date_input)->startOfDay();
            $end   = Carbon::parse($end_date_input)->endOfDay();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Format tanggal tidak valid');
        }

        $userId = auth()->id();
        $year   = (int) $start->format('Y');

        // ========== 1) Prefetch COA level 4 (prefix 5 digit) & level 5 ==========
        // Level-4 (5 digit) dipakai untuk ambil 'arus_kas' & nama kelompok
        $coasLv4 = Coa::where('created_by', $userId)
            ->where('level', 4)
            ->whereNull('is_deleted')
            ->get(['nomor_akun','nama_akun','arus_kas'])
            ->keyBy('nomor_akun');

        // Level-5 (akun detail) dipakai untuk cek saldo_normal
        $coasLv5 = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->get(['nomor_akun','nama_akun','saldo_normal'])
            ->keyBy('nomor_akun');

        // Jika COA kosong, hentikan
        if ($coasLv5->isEmpty()) {
            return redirect()->back()->with('error', 'COA level 5 tidak ditemukan');
        }

        // ========== 2) Agregat transaksi periode dari jurnal_details ==========
        // Ambil total debit/credit per coa_akun dalam rentang waktu
        $aggPeriode = JurnalDetail::withoutGlobalScopes()
            ->select('coa_akun',
                DB::raw('SUM(debit)  AS sum_debit'),
                DB::raw('SUM(credit) AS sum_credit')
            )
            ->where('created_by', $userId)
            ->whereBetween('tanggal_bukti', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('coa_akun')
            ->get();

        if ($aggPeriode->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // ========== 3) Hitung total kas (perubahan kas) langsung di DB ==========
        // Asumsi semua akun kas mulai dengan '1110' (seperti kode kamu)
        $totalKas = (float) JurnalDetail::withoutGlobalScopes()
            ->where('created_by', $userId)
            ->whereBetween('tanggal_bukti', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->where('coa_akun', 'like', '1110%')
            ->select(DB::raw('COALESCE(SUM(debit - credit),0) as delta_kas'))
            ->value('delta_kas');

        // ========== 4) Bentuk $data: kelompok per arus_kas (dari COA lv4) ==========
        // Struktur: $data[$kategori][$nama_kelompok_lv4] = total nilai
        $data = [];

        foreach ($aggPeriode as $row) {
            $coa = (string) $row->coa_akun;

            // skip akun kas (opsional), kode lama memfilter coas: not like '111%'
            if (strpos($coa, '111') === 0) {
                continue;
            }

            // cari info lv5 (saldo_normal)
            $lv5 = $coasLv5->get($coa);
            if (!$lv5) {
                // kalau nomornya disimpan dengan format ber-strip, coba bersihkan
                $lv5 = $coasLv5->get(str_replace('-', '', $coa));
                if (!$lv5) continue;
            }

            $isDebitNormal = in_array(strtolower((string)$lv5->saldo_normal), ['db','debit'], true);

            // cari lv4 (prefix 5 digit)
            $prefix5 = substr($coa, 0, 5);
            $lv4 = $coasLv4->get($prefix5);
            if (!$lv4) {
                // jika COA lv4 tidak ada, lewati
                continue;
            }

            // kategori arus kas & label nama kelompok (lv4)
            $kategori = $lv4->arus_kas;        // mis: "Operasional", "Investasi", "Pendanaan"
            $labelKelompok = $lv4->nama_akun;  // nama akun lv4 untuk tampilan

            if (!$kategori) {
                // kalau belum diklasifikasikan, lewati
                continue;
            }

            // nilai arus kas per akun: tergantung saldo normal lv5
            $debit  = (float) $row->sum_debit;
            $credit = (float) $row->sum_credit;
            $nilai  = $isDebitNormal ? ($debit - $credit) : ($credit - $debit);

            // akumulasikan ke kategori + label lv4
            if (!isset($data[$kategori])) {
                $data[$kategori] = [];
            }
            $data[$kategori][$labelKelompok] = ($data[$kategori][$labelKelompok] ?? 0) + $nilai;
        }

        // ========== 5) Ambil Kas Awal/Akhir dari neracaFunc (logika kamu) ==========
        $getKas = $this->neracaFunc($end->toDateTimeString());
        $getKas = $this->array_change_key_case_recursive($getKas, CASE_LOWER);

        // Safety guard ambil kas awal/akhir
        $kasAwal  = $getKas[$year - 1][1]['aset lancar']['kas dan setara kas'] ?? 0;
        $kasAkhir = $getKas[$year][1]['aset lancar']['kas dan setara kas'] ?? 0;

        // Tambahkan total per kategori & ringkasan total
        foreach ($data as $kategori => $rows) {
            $data[$kategori]['Total'] = array_sum($rows);
        }
        $data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'] = $totalKas;
        $data['Total']['Kas dan Setara Kas Awal']  = $kasAwal;
        $data['Total']['Kas dan Setara Kas Akhir'] = $kasAkhir;

        if (empty($data)) {
            echo "<script>alert('Oops! Data tidak ditemukan'); window.close();</script>";
            return;
        }

        // Paging meta (tanpa perubahan)
        $paged = [
            'dibuat'      => $request->input('dibuat'),
            'alamat'      => $request->input('alamat'),
            'tanggal'     => $request->input('tanggal'),
            'jabatan'     => $request->input('jabatan'),
            'jumlahLaman' => (int) $request->input('jumlahLaman'),
        ];

        // (opsional) chunk untuk layout
        $dataChunked = collect($data)->chunk(4);

        // Render Dompdf (bukan Snappy)
        $pdf = Dompdf::loadView('report.aruskas', [
                'dataChunked' => $dataChunked,
                'start_date'  => $start->format('d/m/Y'),
                'end_date'    => $end->format('d/m/Y'),
                'paged'       => $paged,
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->stream('aruskas.pdf');
    }

    public function labaRugiView(Request $request , $n = 0)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET'){
              return view('report.views.template');
        }
        $start_date = Carbon::parse($request->input('start'))->format('Y-m-d H:i:s');
        $start      = $request->input('start');
        $end_date = Carbon::parse($request->input('end'))->format('Y-m-d H:i:s');
        $end      = $request->input('end_date');
        $ttd1 = $request->input('text_input1');
        $ttd2 = $request->input('text_input2');

        $tahunSebelumnya = date('Y');
        if($n == 1){
            $jurnal = Jurnal::whereNull('is_deleted')
                    ->with(['details' => function($query) use ($end_date) {
                        $query->whereRaw('LEFT(coa_akun, 1) >= ?', ['4'])
                        ->where('tanggal_bukti', '<=' , $end_date);
                    }])
                    ->whereYear('jurnal_tgl', $tahunSebelumnya)
                    ->where('created_by', auth()->user()->id)
                    ->get();
        }else{
            $jurnal = Jurnal::whereNull('is_deleted')
                    ->with(['details' => function($query) use ($start_date, $end_date) {
                        $query->whereRaw('LEFT(coa_akun, 1) >= ?', ['4'])
                        ->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                    }])
                    ->whereYear('jurnal_tgl', $tahunSebelumnya)
                    ->where('created_by', auth()->user()->id)
                    ->get();
        }

        if($jurnal->isEmpty()){
            Alert::error('Oops!', 'Data tidak ditemukan');
            return redirect()->back();
        }

        $kategori = Coa::where('created_by', auth()->user()->id)->where('level', '=', '1')->get()->keyBy('nomor_akun')->toArray();
        $data = [];

        foreach ($jurnal as $entry) {
            foreach ($entry->details as $detail) {
                $kategoriAkun = substr($detail->coa_akun, 0, 1);
                $lv3 = substr($detail->coa_akun, 0, 3);
                if (isset($kategori[$kategoriAkun])) {
                    $parent = $kategori[$kategoriAkun];
                    $child = Coa::where(['nomor_akun' => $detail->coa_akun, 'created_by' => auth()->user()->id])->first();
                    $lv3 = Coa::where(['nomor_akun' => $lv3, 'created_by' => auth()->user()->id])->first();
                    if ($parent['saldo_normal'] == 'db' || $parent['saldo_normal'] == 'debit') {
                        $nilai = $child['saldo_awal_debit'] + $detail->debit - $detail->credit;
                    } else {
                        $nilai = $child['saldo_awal_credit'] + $detail->credit - $detail->debit;
                    }

                    $data[$parent['nama_akun']]['Jumlah'] = ($data[$parent['nama_akun']]['Jumlah'] ?? 0) + $nilai;
                    if (isset($child['nama_akun'])) {
                        $data[$parent['nama_akun']]['Detail'][$lv3['nama_akun']] = ($data[$parent['nama_akun']]['Detail'][$lv3['nama_akun']] ?? 0) + $nilai;
                    }
                }
            }
        }

        uksort($data, function($a, $b) use ($kategori) {
            $order = array_flip(array_map(function($item) {
                return $item['nama_akun'];
            }, $kategori));
            return ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX);
        });

        $totalPendapatan = 0;
        $totalBiaya = 0;

        foreach ($data as $category => $details) {
            if ($category == 'Pendapatan') {
                $totalPendapatan += $details['Jumlah'];
            } else {
                $totalBiaya += $details['Jumlah'];
            }
        }


        $labaRugiBersih = $totalPendapatan - $totalBiaya;
        // da($labaRugiBersih);
        if($n == 1){
            return $labaRugiBersih;
        }

        // da($data);
        return view('report.labarugi', [
                'data' => $data,
                'tahunSebelumnya' => $tahunSebelumnya,
                'kategori' => $kategori,
                'labaRugiBersih' => $labaRugiBersih,
                'ttd1' => $ttd1,
                'ttd2' => $ttd2,
                'start' => $start,
                'end'   => $end,
            ]);

    }


    public function labaRugi(Request $request, $n = 0)
    {
        if (!$request->isMethod('post')) {
            return view('report.views.template');
        }

        // Parse tanggal
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
            $end_date   = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal tidak valid');
        }
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $year  = substr($start_date, 0, 4) ?: date('Y');
        $userId = auth()->id();

        $paged = [
            'dibuat' => $request->input('dibuat'),
            'alamat' => $request->input('alamat'),
            'tanggal' => $request->input('tanggal'),
            'jabatan' => $request->input('jabatan'),
            'jumlahLaman' => (int) $request->input('jumlahLaman'),
        ];
        $tahunSebelumnya = date('Y');

        // ===== 1) Prefetch COA Level 1, 3, 5 =====
        // Level 1: kategori (Pendapatan, Beban, dll) → untuk saldo_normal & urutan
        $coasLv1 = Coa::where('created_by', $userId)
            ->where('level', 1)
            ->orderBy('nomor_akun')
            ->get(['nomor_akun','nama_akun','saldo_normal'])
            ->keyBy('nomor_akun'); // key: '4','5','6','7', dst

        if ($coasLv1->isEmpty()) {
            return back()->with('error', 'COA level 1 tidak ditemukan');
        }

        // Level 3: nama grup untuk tampilan detail
        $coasLv3 = Coa::where('created_by', $userId)
            ->where('level', 3)
            ->get(['nomor_akun','nama_akun'])
            ->keyBy('nomor_akun'); // key: '4xx'

        // Level 5: saldo_awal + saldo_normal akun (child)
        $coasLv5 = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->get(['nomor_akun','saldo_awal_debit','saldo_awal_credit'])
            ->keyBy('nomor_akun');

        // ===== 2) Agregat transaksi periode dari jurnal_details =====
        $agg = JurnalDetail::withoutGlobalScopes()
            ->select('coa_akun',
                DB::raw('SUM(debit)  AS s_debit'),
                DB::raw('SUM(credit) AS s_credit')
            )
            ->where('created_by', $userId)
            ->when($n == 1, function ($q) use ($end_date) {
                $q->where('tanggal_bukti', '<=', $end_date);
            }, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('tanggal_bukti', [$start_date, $end_date]);
            })
            // hanya kelas 4+ (pendapatan/beban/dll)
            ->whereRaw('LEFT(coa_akun, 1) >= ?', ['4'])
            ->groupBy('coa_akun')
            ->get();

        if ($agg->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        // ===== 3) Bentuk data: kategori (Lv1) -> Detail (Lv3) =====
        $data = [];

        foreach ($agg as $row) {
            $acc = (string) $row->coa_akun;
            $katKey = substr($acc, 0, 1);     // '4','5',...
            $lv3Key = substr($acc, 0, 3);     // '4xx'

            $kat = $coasLv1->get($katKey);
            if (!$kat) continue; // ignore jika master tidak ada

            $child = $coasLv5->get($acc);
            if (!$child) {
                // coba fallback tanpa tanda pisah
                $child = $coasLv5->get(str_replace('-', '', $acc));
                if (!$child) continue;
            }

            $lv3 = $coasLv3->get($lv3Key);
            $lv3Name = $lv3->nama_akun ?? $lv3Key;

            $isDebitNormKategori = in_array(strtolower((string)$kat->saldo_normal), ['db','debit'], true);

            $sumD = (float) $row->s_debit;
            $sumC = (float) $row->s_credit;

            // nilai menurut saldo normal KATEGORI (sesuai kode lama)
            $nilai = $isDebitNormKategori
                ? ((float)($child->saldo_awal_debit ?? 0)  + ($sumD - $sumC))
                : ((float)($child->saldo_awal_credit ?? 0) + ($sumC - $sumD));

            $katName = $kat->nama_akun;

            // akumulasi
            $data[$katName]['Jumlah'] = ($data[$katName]['Jumlah'] ?? 0) + $nilai;
            $data[$katName]['Detail'][$lv3Name] = ($data[$katName]['Detail'][$lv3Name] ?? 0) + $nilai;
        }

        if (empty($data)) {
            echo "<script>alert('Oops! Data tidak ditemukan'); window.close();</script>";
            return;
        }

        // ===== 4) Urutkan kategori mengikuti urutan Lv1 =====
        uksort($data, function($a, $b) use ($coasLv1) {
            $order = [];
            foreach ($coasLv1 as $lv1) { $order[$lv1->nama_akun] = count($order); }
            return ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX);
        });

        // ===== 5) Hitung Laba Rugi Bersih (kategori pertama - sisa) =====
        $firstKey = null; $firstValue = 0.0;
        foreach ($data as $k => $v) { $firstKey = $k; $firstValue = (float)($v['Jumlah'] ?? 0); break; }

        $sumOthers = 0.0; $i = 0;
        foreach ($data as $k => $v) {
            if ($i++ === 0) continue;
            $sumOthers += (float)($v['Jumlah'] ?? 0);
        }
        $labaRugiBersih = $firstValue - $sumOthers;

        if ($n == 1) {
            return $labaRugiBersih; // kompatibel dengan perilaku lama
        }

        // ===== 6) Render PDF (Dompdf) =====
        $chunkedData = collect($data)->chunk(4);
        if ($chunkedData->isNotEmpty() && optional($chunkedData->last())->isEmpty()) {
            $chunkedData->pop();
        }

        $pdf = Dompdf::loadView('report.labarugi', [
                'dataChunked'      => $chunkedData,
                'tahunSebelumnya'  => $tahunSebelumnya,
                'kategori'         => $coasLv1->toArray(), // kalau view masih butuh
                'labaRugiBersih'   => $labaRugiBersih,
                'paged'            => $paged,
                'start'            => $start,
                'end'              => $end,
                'jumlahLaman'      => $paged['jumlahLaman'],
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->stream('labarugi.pdf');
    }

    public function perubahanEkuitas(Request $request) {
        if($request->isMethod('post')){
            $start_date_input = $request->input('start_date');
            $end_date_input = $request->input('end_date');

            if (!$start_date_input || !$end_date_input) {
                Alert::error('Oops!', 'Data tidak ditemukan');
                return redirect()->back();
            }

            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay()->format('Y-m-d H:i:s');

            if($start_date){
                $year = substr($start_date,0,4);
            }else{
                $year = date('Y');
            }

            $tahunSebelumnya = $year - 1;
            $tahunSekarang = $year;

            $jurnalDulu = Jurnal::whereNull('is_deleted')
                            ->with(['details' => function($query) use ($start_date, $end_date) {
                                $query->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                            }])
                            ->whereYear('jurnal_tgl', $tahunSebelumnya)
                            ->where('created_by', auth()->user()->id)->get();

            $jurnalSekarang = Jurnal::whereNull('is_deleted')
                            ->with(['details' => function($query) use ($start_date, $end_date) {
                                $query->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                            }])
                            ->whereYear('jurnal_tgl', $tahunSekarang)
                            ->where('created_by', auth()->user()->id)->get();

            if($jurnalDulu->isEmpty() && $jurnalSekarang->isEmpty()){
                Alert::error('Oops!', 'Data tidak ditemukan');
                return redirect()->back();
            }

            $labaRugi   = $this->labaRugi($request, 1);
            $neraca     = $this->neracaEkuitas($end_date, $labaRugi);

            //   dd($neraca);
            if(@$neraca['status'] == 'error'){
                Alert::error($neraca['title'], $neraca['msg']);
                return redirect()->back();

                $result = [
                    $tahunSekarang => [
                        'MODAL' => 0,
                        'Sisa Hasil Usaha' => 0,
                    ],
                    $tahunSebelumnya => [
                        'MODAL' => 0,
                        'Sisa Hasil Usaha' => 0,
                    ]
                ];
            }else{
                $result = [];
                foreach ($neraca as $tahun => $data) {
                    if (isset($data["Liabilitas dan Ekuitas"])) {
                        // Mengubah semua key menjadi lowercase untuk pencarian
                        $liabilitasDanEkuitas = array_change_key_case($data["Liabilitas dan Ekuitas"], CASE_LOWER);

                        //ambil array terakhir
                        $lastItem = end($liabilitasDanEkuitas);
                        $result[$tahun] = $lastItem;
                    }

                }

                // Dede 11/03/2025
                //  foreach ($result as $year => $items) {
                        // Menghapus elemen pertama dari array setiap tahun
                        //array_shift($result[$year]);
                // }
            }

            foreach ($result as $tahun => $values) {
                if (isset($values["Saldo Tahun Berjalan"])) {
                    $saldo = ["Saldo Tahun Berjalan" => $values["Saldo Tahun Berjalan"]];
                    unset($values["Saldo Tahun Berjalan"]); // Hapus dari posisi awal
                    $result[$tahun] = array_merge($values, $saldo); // Tambahkan di posisi akhir
                }
            }


            // da($result);

            $pdf = Dompdf::loadView('report.perubahanekuitas', [
                'data' => $result,
                'tahun'  => $tahunSekarang,
                'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
                'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y'),
            ]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('perubahan_ekuitas.pdf');
        }

        return view('report.views.template');
    }

    private function calculateTotals($journals, $coa) {
        $totalPendapatan = 0;
        $totalHPP = 0;
        $totalBeban = 0;
        $totalModal = 0;
        $namaAkun = '';

        foreach ($journals as $item) {
            foreach ($item->details as $detail) {
                $nomorAkun = substr($detail->coa_akun, 0, 1);


                if ($nomorAkun == '4') {
                    $totalPendapatan += $detail->credit - $detail->debit;
                } elseif ($nomorAkun == '5') {
                    $totalHPP += $detail->debit - $detail->credit;
                } elseif ($nomorAkun == '6') {
                    $totalBeban += $detail->debit - $detail->credit;
                } elseif ($nomorAkun == '3') {
                    $noKun = substr($detail->coa_akun, 0, 4);
                    $namaAkun = $coa->where('nomor_akun', $noKun)->first();
                    $totalModal += $detail->debit + $detail->credit;
                }
            }
        }

        return [
            'pendapatan' => $totalPendapatan,
            'hpp' => $totalHPP,
            'beban' => $totalBeban,
            'modal' => $totalModal,
            'namaAkun' => $namaAkun ? $namaAkun->nama_akun : ''
        ];
    }

    function processLevel($coa, $labaRugi)
    {
        $result = [];
        $sisa = [];
            foreach ($coa as $lv1) {
                $lv1Result = [];
                foreach ($lv1['child'] as $lv2) {
                    $lv2Result = [];
                    foreach ($lv2['child'] as $lv3) {
                        $lv3Result = [];
                        foreach ($lv3['child'] as $lv4) {
                            $lv4Sum = 0;
                            $sumSisa = 0;
                            foreach ($lv4['child'] as $lv5) {
                                if($lv5['saldo_awal_debit'] != '0' || $lv5['saldo_awal_credit'] != '0'){
                                    if($lv5['saldo_normal'] == 'debit' || $lv5['saldo_normal'] == 'db'){
                                        $balance = $lv5['saldo_awal_debit'];
                                        // $sisa[$lv5['nomor_akun']] = $lv5['saldo_awal_debit'] - $lv5['saldo_awal_credit'];
                                    }else{
                                        $balance = $lv5['saldo_awal_credit'];
                                        // $sisa[$lv5['nomor_akun']] = $lv5['saldo_awal_credit'] - $lv5['saldo_awal_debit'];
                                    }

                                    if($lv5['saldo_awal_debit'] != '0' && $lv5['saldo_normal'] != 'debit'){
                                        $sisa[$lv5['nomor_akun']] = $lv5['saldo_awal_debit'] - $lv5['saldo_awal_credit'];
                                    }

                                    $lv4Sum += $balance;
                                    $sumSisa += @$sisa[$lv5['nomor_akun']];
                                }else{
                                    if($lv5['saldo_normal'] == 'debit' || $lv5['saldo_normal'] == 'db'){
                                        $balance = $lv5['saldo_awal_debit'];
                                    }else{
                                        $balance = $lv5['saldo_awal_credit'];
                                    }
                                    $lv4Sum += $balance;
                                }
                            }
                            if ($lv4Sum != 0 || $sumSisa != 0) {
                                $lv3Result[$lv4['nama_akun']] = $lv4Sum;
                                // $lv3Result['Sisa'] = $sumSisa;
                            }
                        }
                        if (!empty($lv3Result)) {
                            $sus = array_sum(array_values($lv3Result));
                            $lv2Result[$lv3['nama_akun']] = $sus;
                            if($lv1['nomor_akun'] == 3 || $lv1['nomor_akun'] == '3'){
                                $lv2Result['Saldo Tahun Berjalan'] = $labaRugi;
                            }
                        }
                    }
                    if (!empty($lv2Result)) {
                        $lv1Result[$lv2['nama_akun']] = $lv2Result;
                    }
                }
                if (!empty($lv1Result)) {
                    if($lv1['golongan'] == 'Ekuitas' || $lv1['golongan'] == 'Liabilitas'){
                        if (isset($result['Liabilitas dan Ekuitas'])) {
                            $result['Liabilitas dan Ekuitas'] = array_merge_recursive($result['Liabilitas dan Ekuitas'], $lv1Result);
                        } else {
                            $result['Liabilitas dan Ekuitas'] = $lv1Result;
                        }
                    }else{
                        $result[$lv1['nama_akun']] = $lv1Result;
                    }

                    if(@$sisa){
                        $getSisa = array_keys($sisa);
                        $nom = substr($getSisa[0], 0, 5);
                        if($lv4['nomor_akun'] == $nom){
                            if($lv1['golongan'] == 'Ekuitas' || $lv1['golongan'] == 'Liabilitas'){
                                $result['Liabilitas dan Ekuitas'][$lv1['nama_akun']]['sisa'] = @$sisa[$getSisa[0]];
                            }
                        }
                    }
                }
            }

        return $result;
    }

    // hitung saldo awal
    private function hitungSaldoAwal($detail)
    {
        $saldoAwal = Coa::where('nomor_akun', $detail->coa_akun)
            ->where('created_by', auth()->user()->id)
            ->whereNull('is_deleted')
            ->first();

        $saldoNormal = strtolower($saldoAwal->saldo_normal);
        return $saldoNormal == 'db' || $saldoNormal == 'debit'
            ? $saldoAwal->saldo_awal_debit
            : $saldoAwal->saldo_awal_credit;
    }

    public function neracaEkuitas($tanggal, $labaRugi = null)
    {
        $title = "";
        $message = "";
        $error = 0;

        $year = $tanggal ? substr($tanggal, 0, 4) : date('Y');
        $userId = auth()->id();

        // ===== 1) Prefetch COA level 1..5 (sekali) =====
        $coas = Coa::whereNull('is_deleted')
            ->where('created_by', $userId)
            ->whereIn('level', [1,2,3,4,5])
            ->orderBy('nomor_akun','asc')
            ->get(['level','nomor_akun','nama_akun','saldo_normal','saldo_awal_debit','saldo_awal_credit','saldo_berjalan_debit','saldo_berjalan_credit','golongan','periode'])
            ->groupBy('level');

        $coasLv1 = ($coas[1] ?? collect())->keyBy('nomor_akun'); // '1','2','3'
        $coasLv2 = ($coas[2] ?? collect())->keyBy('nomor_akun'); // '11','21',dst
        $coasLv3 = ($coas[3] ?? collect())->keyBy('nomor_akun'); // '111','311',dst
        $coasLv4 = ($coas[4] ?? collect())->keyBy('nomor_akun'); // '11101',dst
        $coasLv5 = ($coas[5] ?? collect())->keyBy('nomor_akun'); // '11101001',dst

        if ($coasLv1->isEmpty() || $coasLv5->isEmpty()) {
            return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Master COA tidak lengkap.'];
        }

        // ===== 2) Agregat jurnal_details ≤ tanggal, hanya akun 1/2/3, GROUP BY 4-digit prefix =====
        $jdAgg4 = DB::table('jurnal_details')
            ->selectRaw("LEFT(coa_akun,4) AS k4, SUM(debit) AS debit, SUM(credit) AS credit")
            ->where('created_by', $userId)
            ->where(function($q){
                $q->where('coa_akun', 'like', '1%')
                ->orWhere('coa_akun', 'like', '2%')
                ->orWhere('coa_akun', 'like', '3%');
            })
            ->where('tanggal_bukti', '<=', $tanggal)
            ->where('periode', $year)
            ->groupBy('k4')
            ->get()
            ->keyBy('k4');

        if ($jdAgg4->isEmpty()) {
            return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Data jurnal tidak ditemukan.'];
        }

        // ===== 3) Agregat saldo awal COA (SUM) GROUP BY 4-digit prefix =====
        $coaAgg4 = DB::table('coas')
            ->selectRaw("LEFT(nomor_akun,4) AS k4, SUM(saldo_awal_debit) AS sad, SUM(saldo_awal_credit) AS sac")
            ->where('created_by', $userId)
            ->where(function($q){
                $q->where('nomor_akun', 'like', '1%')
                ->orWhere('nomor_akun', 'like', '2%')
                ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('periode', $year)
            ->groupBy('k4')
            ->get()
            ->keyBy('k4');

        // ===== 4) Siapkan data & validasi ringan (tanpa N+1) =====
        $data = [];

        foreach ($coasLv5 as $nomorAkun => $coa5) {
            // Hanya akun 1/2/3 sesuai kode lama
            $h1 = substr($nomorAkun, 0, 1);
            if (!in_array($h1, ['1','2','3'], true)) continue;

            $p1 = $coasLv1->get($h1);
            $p2 = $coasLv2->get(substr($nomorAkun, 0, 2));
            $p3 = $coasLv3->get(substr($nomorAkun, 0, 3));
            $p4 = $coasLv4->get(substr($nomorAkun, 0, 5));

            if (!$p3) {
                return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Akun CoA level 3 ('.substr($nomorAkun,0,3).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !'];
            }
            if (!$p2) {
                return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Akun CoA level 2 ('.substr($nomorAkun,0,2).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !'];
            }
            if (!$p4) {
                return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Akun CoA level 4 ('.substr($nomorAkun,0,5).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !'];
            }

            // Ambil agregat berdasarkan 4-digit prefix
            $k4 = substr($nomorAkun, 0, 4);
            $jt = $jdAgg4->get($k4);
            $ct = $coaAgg4->get($k4);

            $sumDebit  = (float)($jt->debit ?? 0);
            $sumCredit = (float)($jt->credit ?? 0);
            $sumAwalD  = (float)($ct->sad ?? 0);
            $sumAwalC  = (float)($ct->sac ?? 0);

            // Hitung saldo dan saldo awal menurut saldo_normal akun detail (level 5)
            $isDebitNormal = in_array(strtolower((string)$coa5->saldo_normal), ['debit','d','db'], true);
            $saldo     = $isDebitNormal ? ($sumAwalD + $sumDebit - $sumCredit)
                                        : ($sumAwalC + $sumCredit - $sumDebit);
            $saldoAwal = $isDebitNormal ? $sumAwalD : $sumAwalC;

            // Simpan ke struktur data
            $parentKey     = $p1->nomor_akun;        // '1','2','3'
            $childName     = $p2->nama_akun;         // nama level 2
            $subChildKey   = $p3->nomor_akun;        // '111','311', ...
            $currentYear   = (int)$year;
            $previousYear  = $currentYear - 1;

            if ($saldo != 0) {
                $data[$currentYear][$parentKey][$childName][$subChildKey] = $saldo;
            }

            if ($saldoAwal != 0) {
                $data[$previousYear][$parentKey][$childName][$subChildKey] = $saldoAwal;
            }

            // Susunan tambahan "Liabilitas dan Ekuitas" mengikuti pola lama
            $liabEqKey = 'Liabilitas dan Ekuitas';
            $data[$currentYear][$liabEqKey][$childName][$subChildKey]   = $data[$currentYear][$parentKey][$childName][$subChildKey] ?? $saldo;
            $data[$previousYear][$liabEqKey][$childName][$subChildKey]  = $data[$previousYear][$parentKey][$childName][$subChildKey] ?? $saldoAwal;

            // Sisipkan "Saldo Tahun Berjalan" pada 311 atau prefix '30'
            if ($subChildKey === '311' || strpos($subChildKey, '30') === 0) {
                $data[$currentYear][$liabEqKey][$childName]['Saldo Tahun Berjalan']  = $labaRugi;
                $data[$previousYear][$liabEqKey][$childName]['Saldo Tahun Berjalan'] = 0;
            }
        }

        // ===== 5) Komponen Ekuitas dari COA level-5 '3%' (tanpa raw SELECT per baris) =====
        // Meniru blok "coas2" dengan agregasi in-memory
        $ekuitasLv5 = $coasLv5->filter(function($row){
            return strpos($row->nomor_akun, '3') === 0
                && (
                    ($row->saldo_awal_debit ?? 0) != 0 ||
                    ($row->saldo_awal_credit ?? 0) != 0 ||
                    ($row->saldo_berjalan_debit ?? 0) != 0 ||
                    ($row->saldo_berjalan_credit ?? 0) != 0
                );
        });

        $ekuitasAgg3 = [];
        foreach ($ekuitasLv5 as $row) {
            $k3 = substr($row->nomor_akun, 0, 3);
            // Pilih saldo seperti blok lama: saldo_berjalan_credit jika ada, else saldo_awal_credit (fallback 0)
            $saldo = ($row->saldo_berjalan_credit ?? 0) ?: ($row->saldo_awal_credit ?? 0);
            if ($saldo != 0) {
                $ekuitasAgg3[$k3] = ($ekuitasAgg3[$k3] ?? 0) + $saldo;
            }
        }
        if (!empty($ekuitasAgg3)) {
            $currentYear = (int)$year;
            foreach ($ekuitasAgg3 as $k3 => $saldo) {
                $data[$currentYear][3]['Ekuitas'][$k3] = $saldo; // mengikuti pola lama
            }
        }


        foreach($data as $key => $value){
            foreach($value as $key2 => $value2){
                foreach($value2 as $key3 => $value3){
                    if (is_array($value3)) {
                        ksort($value3);
                        $data[$key][$key2][$key3] = $value3;
                        foreach($value3 as $key4 => $value4){
                            $coa = Coa::where('nomor_akun', $key4)->where('created_by', auth()->user()->id)->first();
                            if($coa){
                                $data[$key][$key2][$key3][@$coa->nama_akun] = $value4;
                                unset($data[$key][$key2][$key3][$key4]);
                            }
                        }
                    }
                }
            }
        }

        $currentYear = (int) $year; // variabel $year yang kamu pakai di function
        $data = pruneZerosPairYears($data, $currentYear);

        return $data;
    }

    public function neracaFunc($tanggal, $labaRugi = null){
        $year = substr($tanggal,0,4);

        // Get all relevant journal details
        $jurnal = JurnalDetail::where('created_by', auth()->user()->id)
            ->where(function($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })
            ->where('tanggal_bukti', '<=', $tanggal)
            ->orderBy('coa_akun', 'asc')
            ->get()
            ->keyBy('coa_akun');

        // Get all COA accounts (like trial balance does)
        $coa = Coa::whereNull('is_deleted')
            ->where(function($query) {
                $query->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('created_by', auth()->user()->id)
            ->whereYear('created_at', $year)
            ->orderBy('nomor_akun', 'asc')
            ->get()
            ->keyBy('nomor_akun');

        if($coa->isEmpty()) {
            return [];
        }

        $data = [];

        // Process ALL COA accounts (like trial balance does)
        foreach($coa as $nomorAkun => $coaData) {
            // Skip if not detail level account
            if($coaData->level < 5) {
                continue;
            }

            $parent = $coa->get(substr($nomorAkun, 0, 1));
            $child = $coa->get(substr($nomorAkun, 0, 2));
            $subChild = $coa->get(substr($nomorAkun, 0, 3));

            if(!$parent || !$child || !$subChild) {
                continue;
            }

            // Calculate current balance for this account
            $saldo = 0;
            $saldoAwal = $coaData->saldo_awal_debit + $coaData->saldo_awal_credit;

            // Add journal movements if any
            if($jurnal->has($nomorAkun)) {
                $jurnalEntry = $jurnal->get($nomorAkun);
                if(in_array($coaData->saldo_normal, ['debit', 'd', 'db'])) {
                    $saldo = $coaData->saldo_awal_debit + $jurnalEntry->debit - $jurnalEntry->credit;
                    $saldoAwal = $coaData->saldo_awal_debit;
                } else {
                    $saldo = $coaData->saldo_awal_credit + $jurnalEntry->credit - $jurnalEntry->debit;
                    $saldoAwal = $coaData->saldo_awal_credit;
                }
            } else {
                // No journal entries, use opening balance
                if(in_array($coaData->saldo_normal, ['debit', 'd', 'db'])) {
                    $saldo = $coaData->saldo_awal_debit;
                    $saldoAwal = $coaData->saldo_awal_debit;
                } else {
                    $saldo = $coaData->saldo_awal_credit;
                    $saldoAwal = $coaData->saldo_awal_credit;
                }
            }

            // Only include accounts with non-zero balances
            if($saldo != 0 || $saldoAwal != 0) {
                $data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;

                // Handle Liabilities and Equity grouping
                if(@$parent->golongan == 'Liabilitas' || @$parent->golongan == 'Ekuitas'){
                    $data[$year]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                    $data[$year - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;
                }

                // Add current year profit/loss to equity
                if($subChild->nomor_akun == '311' || strpos($subChild->nomor_akun, '30') === 0){
                    $data[$year]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = $labaRugi;
                    $data[$year - 1]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = 0;
                }
            }
        }

        // Clean up the data structure - remove separate liability and equity sections
        foreach($data as $tahun => $rows) {
            foreach($rows as $rowKey => $row) {
                if(in_array($rowKey, ['2', '3', 2, 3], true)){
                    unset($data[$tahun][$rowKey]);
                }
            }
        }

        // Replace account numbers with account names
        foreach($data as $key => $value){
            foreach($value as $key2 => $value2){
                foreach($value2 as $key3 => $value3){
                    if (is_array($value3)) {
                        ksort($value3);
                        $data[$key][$key2][$key3] = $value3;
                        foreach($value3 as $key4 => $value4){
                            $coaItem = Coa::where('nomor_akun', $key4)
                                ->where('created_by', auth()->user()->id)
                                ->first();
                            if($coaItem){
                                $data[$key][$key2][$key3][$coaItem->nama_akun] = $value4;
                                unset($data[$key][$key2][$key3][$key4]);
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function neraca(Request $request, $n = null){
        // da($request);
        if($request->isMethod('post')){
            // da($request->query('excel'));
            $start_date_input = $request->input('start_date');
            $end_date_input = $request->input('end_date');

            if (!$start_date_input || !$end_date_input) {
                Alert::error('Oops!', 'Data tidak ditemukan');
                return redirect()->back();
            }
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay()->format('Y-m-d H:i:s');
            // $ttd1 = $request->input('dibuat');
            // $alamat = $request->input('alamat');
            // $tanggal = $request->input('tanggal');
            // $jabatan = $request->input('jabatan');
            // $jumlahLaman = $request->input('jumlahLaman');
            if($start_date){
                $year = substr($start_date,0,4);
            }else{
                $year = date('Y');
            }
            $paged = [
                'dibuat' => $request->input('dibuat'),
                'alamat' => $request->input('alamat'),
                'tanggal' => $request->input('tanggal'),
                'jabatan' => $request->input('jabatan'),
                'jumlahLaman' => (int) $request->input('jumlahLaman'),
            ];

            // da('fafa');
            $labaRugi = $this->labarugi($request, 1, $year);

            // da($labaRugi);

            $data = $this->neracaFuncPerbandingan($end_date, $labaRugi, $year);
            //  da($data);

            if ($data instanceof \Illuminate\Http\RedirectResponse) {
                return $data;
            }
            if($n == true){
                unset($data[auth()->user()->periode - 1]);
            }
            if(!$data){
                Alert::error('Oops!', 'Data tidak ditemukan');
                return redirect()->back();
            }
            if(isset($data[0])){
                unset($data[0]);
            }
            $sisaModalUsaha = $data;

            //Kewajiban Lancar start
            $jurnal = JurnalDetail::where('created_by', auth()->user()->id)
                    ->whereBetween('tanggal_bukti', [$start_date, $end_date])
                    ->get();

            $coa = Coa::where('created_by', auth()->user()->id)
                    ->where(function ($query) {
                        $query->where('nomor_akun', 'like', '2%')
                            ->orWhere('nomor_akun', 'like', '3%');
                    })
                    ->where('level', 5)
                    ->whereNull('is_deleted')
                    ->orderBy('nomor_akun', 'asc')
                    ->get()
                    ->keyBy('nomor_akun');

                    $coa_nama = Coa::where('created_by', auth()->user()->id)
                    ->where(function ($query) {
                        $query->where('nomor_akun', 'like', '2%')
                            ->orWhere('nomor_akun', 'like', '3%');
                    })
                    ->where('level', 3)
                    ->whereNull('is_deleted')
                    ->get()
                    ->keyBy('nomor_akun');

                     // da($coa_nama);

            $liabilitas = [];
            $subTotalSaldoAwalDebit     = 0;
            $subTotalSaldoAwalKredit    = 0;
            $subTotalmutasiDebit        = 0;
            $subTotalmutasiKredit       = 0;
            $subTotalsaldoAkhirDebit    = 0;
            $subTotalsaldoAkhirKredit   = 0;

            foreach($coa as $akun => $coaData){
              // da(substr($akun, 0, 3));
              $kepala_coa = $coa_nama[substr($akun, 0, 3)] ?? '';

             // da($data);
                $saldoNormal = strtolower($coaData->saldo_normal);
                if(Carbon::parse($request->input('start_date'))->format('d') != '01'){
                    $awal_bulan = Carbon::parse($request->input('start_date'))->startOfMonth()->format('Y-m-d H:i:s');
                    $saldoAwalDebit = JurnalDetail::where('created_by', auth()->user()->id)
                                                ->where('coa_akun', $akun)
                                                ->whereBetween('tanggal_bukti', [$awal_bulan, $start_date])
                                                ->sum('debit');
                    $saldoAwalKredit = JurnalDetail::where('created_by', auth()->user()->id)
                                                ->where('coa_akun', $akun)
                                                ->whereBetween('tanggal_bukti', [$awal_bulan, $start_date])
                                                ->sum('credit');
                }else{
                    $saldoAwalDebit = 0;
                    $saldoAwalKredit = 0;
                }

                $saldoAwalDebit = $coaData->saldo_awal_debit + $saldoAwalDebit ?? 0;
                $saldoAwalKredit = $coaData->saldo_awal_credit + $saldoAwalKredit ?? 0;

                $subTotalSaldoAwalDebit     += $coaData->saldo_awal_debit ?? 0;
                $subTotalSaldoAwalKredit    += $coaData->saldo_awal_credit ?? 0;

                $mutasiDebit = 0;
                $mutasiKredit = 0;

                $jurnalAkun = $jurnal->where('coa_akun', $akun);
                if($jurnalAkun->isNotEmpty()){
                    $mutasiDebit            = $jurnalAkun->sum('debit');
                    $mutasiKredit           = $jurnalAkun->sum('credit');
                    //Lukman menambahkan tgl 15/11/2024
                    $subTotalmutasiDebit    += $jurnalAkun->sum('debit');
                    $subTotalmutasiKredit   += $jurnalAkun->sum('credit');
                    //
                }

                if ($saldoNormal == 'db' || $saldoNormal == 'debit') {
                    $saldoAkhirDebit            = $saldoAwalDebit + $mutasiDebit - $mutasiKredit;
                    $saldoAkhirKredit = 0;
                    //Lukman menambahkan tgl 15/11/2024
                    $subTotalsaldoAkhirDebit    += $saldoAwalDebit + $mutasiDebit - $mutasiKredit;
                    //
                } else {
                    $saldoAkhirKredit           = $saldoAwalKredit + $mutasiKredit - $mutasiDebit;
                    $saldoAkhirDebit = 0;
                    //Lukman menambahkan tgl 15/11/2024
                    $subTotalsaldoAkhirKredit   += $saldoAwalKredit + $mutasiKredit - $mutasiDebit;
                    //
                }


                // dede 10/03/2025
                $liabilitas[$coaData->golongan][$akun] = [
                    $kepala_coa['nama_akun'] => $saldoAkhirKredit
                ];
            }

        // da($liabilitas);



          //da($tampunganku);

            foreach ($liabilitas as $key => $subArray) {
             // da($subArray);

                foreach ($subArray as $subKey => $item) {
                    // Menghapus item yang memiliki nilai 0
                    $liabilitas[$key][$subKey] = array_filter($item, function($value) {
                        return $value !== 0; // Menghapus item yang nilainya 0
                    });

                    // Menghilangkan elemen array jika kosong setelah filtering
                    if (empty($liabilitas[$key][$subKey])) {
                        unset($liabilitas[$key][$subKey]);
                    }
                }
            }

         // da ($liabilitas);
            $kewajibanLancar = [];
             // $ekuitas = [];
//              foreach ($liabilitas['Liabilitas'] as $kode => $item) {
//                 foreach ($item as $key => $value) {
//                     $kewajibanLancar[$key] = $value;
//                 }
//             }

    // da($kewajibanLancar);
    // dede 11/03/2025
                foreach ($liabilitas['Liabilitas'] as $kode => $item) {
                    foreach ($item as $namaAkun => $nilai) {
                        if (isset($kewajibanLancar[$namaAkun])) {
                            $kewajibanLancar[$namaAkun] += $nilai;
                        } else {
                            $kewajibanLancar[$namaAkun] = $nilai;
                        }
                    }
                }

// da($kewajibanLancar);

            if (!isset($sisaModalUsaha[$year]['Liabilitas dan Ekuitas'])) {
                //Mengambil Ekuitasnya saja
                $ekuitas_values = array_values($liabilitas["Ekuitas"]);

                $tampungEkuitas = [];

            foreach ($ekuitas_values as $ekuitas) {
                foreach ($ekuitas as $key => $value) {
                    if (!isset($tampungEkuitas[$key])) {
                        $tampungEkuitas[$key] = 0;

                    }
                    $tampungEkuitas[$key] += $value;
                }
            }
                //$tampungEkuitas = array_merge(...$ekuitas_values);

                 // da($tampungEkuitas);
                $data[$year]['Liabilitas dan Ekuitas'] = [];
                if (!isset($data[$year]['Liabilitas dan Ekuitas']['LIABILITAS'])) {
                    $liabilitas = [];
                    foreach ($kewajibanLancar as $a => $b) {
                        $liabilitas[$a] = $b; // Menambahkan elemen ke array $liabilitas
                    }
                    $data[$year]['Liabilitas dan Ekuitas']['LIABILITAS'] = $liabilitas;
                }

               // da($data);

                if (!isset($data[$year]['Liabilitas dan Ekuitas']['EKUITAS'])) {
                    $data[$year]['Liabilitas dan Ekuitas']['EKUITAS'] = array_merge(
                        $tampungEkuitas, // Ekuitas yang sudah ada duluan
                        ['Saldo Tahun Berjalan' => $labaRugi] // Ditambahkan di akhir
                    );
                }


                 // da($data);

            }else{
                // da($kewajibanLancar);
                $minusValues = array_filter($kewajibanLancar, function($value) {
                    return $value < 0; // Hanya ambil nilai yang kurang dari 0
                });

               // da($minusValues);

                $liabilitasDanEkuitasIndexed = array_values($data[$year]['Liabilitas dan Ekuitas']);
                $hutangJangkaPendek = array_key_first($data[$year]['Liabilitas dan Ekuitas']); // Mendapatkan key pertama
                if (!empty($minusValues)) {
                    $data[$year]['Liabilitas dan Ekuitas'][$hutangJangkaPendek] = array_merge(
                        $data[$year]['Liabilitas dan Ekuitas'][$hutangJangkaPendek], // Data lama
                        $minusValues // Data baru
                    );

                }

            //    da($data);
                // foreach ($liabilitas['Ekuitas'] as $kode => $item) {
//                     foreach ($item as $key => $value) {
//                         if ($key !== 'Saldo Tahun Berjalan') {
//                             $ekuitas[$key] = $value;
//                         }
//                     }
//                 }

                // dede 11/03/2025
                // da($liabilitas);
                foreach ($liabilitas['Ekuitas'] as $kode => $item) {
                    foreach ($item as $namaAkun => $nilai) {
                        if ($namaAkun !== 'Saldo Tahun Berjalan') {
                            $ekuitas[$namaAkun] = ($ekuitas[$namaAkun] ?? 0) + $nilai;
                        }
                    }
                }

                // da($data);

                $lastKey = array_key_last($data[$year]['Liabilitas dan Ekuitas']);
                if (isset($sisaModalUsaha[$year]['Liabilitas dan Ekuitas'][$lastKey]['Saldo Tahun Berjalan'])) {
                    $ekuitas['Saldo Tahun Berjalan'] = $sisaModalUsaha[$year]['Liabilitas dan Ekuitas'][$lastKey]['Saldo Tahun Berjalan'];
                }

                //   da($data);

                // dd(array_key_last($ekuitas));

                // Update struktur array $data dengan Kewajiban Lancar dan Ekuitas baru
                if(!empty($data[$year]['Liabilitas dan Ekuitas']['Kewajiban Lancar'])){
                //  da($kewajibanLancar);
                    $data[$year]['Liabilitas dan Ekuitas']['Kewajiban Lancar'] = $kewajibanLancar;
                }
                $data[$year]['Liabilitas dan Ekuitas'][$lastKey] = $ekuitas;
            }


            //    da($data);

            if($request->query('excel') == 0){

                $pdf = Dompdf::loadView('report.neraca', [
                    'data'              => $data,
                    'label'             => $n == true ? 'Neraca' : 'Neraca Perbandingan',
                    'periode'           => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
                    'paged'             => $paged,
                    'tanggal_mulai'     => Carbon::parse($start_date)->format('d/m/Y'),
                    'tanggal_selesai'   => Carbon::parse($end_date)->format('d/m/Y'),
                ]);

                $pdf->setPaper('A4', 'portrait');
                if($n){
                    return $pdf->stream('neraca.pdf');
                }else{
                    return $pdf->stream('neraca perbandingan.pdf');
                }
            }else{
                // da($data);
                $label = $n == true ? 'Neraca' : 'Neraca Perbandingan';
                $periode = Carbon::parse($request->input('end_date'))->translatedFormat('j F Y');

                return Excel::download(new NeracaExport($data, $label, $periode, $paged), 'Neraca.xlsx');
            }
            // $pdf = Dompdf::loadView('report.neraca', [
            //     'data' => $data,
            //     'periode' => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
            //     'ttd1' => $ttd1,
            //     'ttd2' => $ttd2,
            // ]);
            // return $pdf->download('neraca_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
    }

    private function removeDuplicatesFromArray(array $array)
    {
          // Serialize setiap elemen untuk membandingkan data bersarang
        $serializedArray = array_map('serialize', $array);

        // Hapus duplikat
        $uniqueSerialized = array_unique($serializedArray);

        // Kembalikan ke bentuk asli dengan unserialize
        $uniqueArray = array_map('unserialize', $uniqueSerialized);

        // Proses array bersarang
        foreach ($uniqueArray as $key => $value) {
            if (is_array($value)) {
                $uniqueArray[$key] = $this->removeDuplicatesFromArray($value); // Rekursif
            }
        }

        return $uniqueArray;
    }

    function sortArrayByKey($array) {
        foreach ($array as &$subArray) {
            if (is_array($subArray)) {
                // Check if it's an associative array that needs sorting
                if (array_keys($subArray) !== range(0, count($subArray) - 1)) {
                    uksort($subArray, function($a, $b) {
                        $aNum = intval(explode(' - ', $a)[0]);
                        $bNum = intval(explode(' - ', $b)[0]);
                        return $aNum <=> $bNum;
                    });
                }
                // Recursively sort the subarray
                $subArray = $this->sortArrayByKey($subArray);
            }
        }
        return $array;
    }


    public function neracaSaldo(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('report.views.template');
        }

        $userId = auth()->id();

        // --- Parse tanggal sekali dengan aman ---
        try {
            $start = Carbon::parse($request->input('start_date'))->startOfDay();
            $end   = Carbon::parse($request->input('end_date'))->endOfDay();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal tidak valid');
        }

        // === Ambil COA: level 5 untuk akun detail, plus level 3 & 4 untuk mapping nama kepala ===
        $coasLv5 = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->orderBy('nomor_akun', 'asc')
            ->get(['nomor_akun','nama_akun','saldo_normal','saldo_awal_debit','saldo_awal_credit'])
            ->keyBy('nomor_akun');

        if ($coasLv5->isEmpty()) {
            return back()->with('error', 'COA level 5 tidak ditemukan');
        }

        // Map nama kepala level 3 & 4 (sekali ambil, hindari DB::select berulang)
        $coasLv3 = Coa::where('created_by', $userId)
            ->where('level', 3)
            ->get(['nomor_akun','nama_akun'])
            ->keyBy('nomor_akun');

        $coasLv4 = Coa::where('created_by', $userId)
            ->where('level', 4)
            ->get(['nomor_akun','nama_akun'])
            ->keyBy('nomor_akun');

        // === Agregat mutasi periode: SUM debit/credit per coa_akun ===
        $mutasiPeriode = JurnalDetail::withoutGlobalScopes()
            ->select('coa_akun',
                DB::raw('SUM(debit)  AS m_debit'),
                DB::raw('SUM(credit) AS m_credit')
            )
            ->where('created_by', $userId)
            ->whereBetween('tanggal_bukti', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('coa_akun')
            ->get()
            ->keyBy('coa_akun');

        if ($mutasiPeriode->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        // === Agregat awal bulan → start (untuk saldo awal berjalan), hanya jika perlu ===
        $mutasiAwal = collect();
        if ($start->day !== 1) {
            $awalBulan = (clone $start)->startOfMonth();
            $mutasiAwal = JurnalDetail::withoutGlobalScopes()
                ->select('coa_akun',
                    DB::raw('SUM(debit)  AS a_debit'),
                    DB::raw('SUM(credit) AS a_credit')
                )
                ->where('created_by', $userId)
                ->whereBetween('tanggal_bukti', [$awalBulan->toDateTimeString(), $start->toDateTimeString()])
                ->groupBy('coa_akun')
                ->get()
                ->keyBy('coa_akun');
        }

        // === Hitung saldo akhir per akun & bentuk struktur bertingkat (kepala3 → kepala4 → akun5) ===
        $data = [];

        // Subtotal global (mengikuti pola variabel lama)
        $subTotalSaldoAwalDebit   = 0.0;
        $subTotalSaldoAwalKredit  = 0.0;
        $subTotalmutasiDebit      = 0.0;
        $subTotalmutasiKredit     = 0.0;
        $subTotalsaldoAkhirDebit  = 0.0;
        $subTotalsaldoAkhirKredit = 0.0;

        foreach ($coasLv5 as $akun5 => $coa5) {
            // saldo normal
            $saldoNormal = strtolower((string)$coa5->saldo_normal);

            // saldo awal statik COA + mutasi awal bulan→start
            $awalAgg = $mutasiAwal->get($akun5);
            $saldoAwalDebit  = (float)($coa5->saldo_awal_debit  ?? 0) + (float)($awalAgg->a_debit  ?? 0);
            $saldoAwalKredit = (float)($coa5->saldo_awal_credit ?? 0) + (float)($awalAgg->a_credit ?? 0);

            // subtotal saldo awal (sesuai kode lama: COA statik saja)
            $subTotalSaldoAwalDebit  += (float)($coa5->saldo_awal_debit  ?? 0);
            $subTotalSaldoAwalKredit += (float)($coa5->saldo_awal_credit ?? 0);

            // mutasi periode
            $mut = $mutasiPeriode->get($akun5);
            $mDebit  = (float)($mut->m_debit  ?? 0);
            $mKredit = (float)($mut->m_credit ?? 0);

            $subTotalmutasiDebit  += $mDebit;
            $subTotalmutasiKredit += $mKredit;

            // saldo akhir
            $saldoAkhirDebit = 0.0; $saldoAkhirKredit = 0.0;
            if ($saldoNormal === 'db' || $saldoNormal === 'debit') {
                $saldoAkhirDebit = $saldoAwalDebit + $mDebit - $mKredit;
                $subTotalsaldoAkhirDebit += $saldoAkhirDebit;
            } else {
                $saldoAkhirKredit = $saldoAwalKredit + $mKredit - $mDebit;
                $subTotalsaldoAkhirKredit += $saldoAkhirKredit;
            }

            // skip kalau nol semua (ikuti logika if di kode lama)
            if ($saldoAkhirDebit == 0 && $saldoAkhirKredit == 0) {
                continue;
            }

            // Tentukan kepala 3/4
            $kepala3 = substr($akun5, 0, 3);
            $kepala4 = substr($akun5, 0, 5);

            $nama_kepala3 = $coasLv3->get($kepala3)->nama_akun ?? 'Unknown';
            $nama_kepala4 = $coasLv4->get($kepala4)->nama_akun ?? 'Unknown';
            $nama_kepala5 = $coa5->nama_akun;

            $label5 = $akun5.' - '.$nama_kepala5;

            if (!isset($data[$nama_kepala3])) $data[$nama_kepala3] = [];
            if (!isset($data[$nama_kepala3][$nama_kepala4])) $data[$nama_kepala3][$nama_kepala4] = [];

            $data[$nama_kepala3][$nama_kepala4][$label5] = [
                'debit'  => $saldoAkhirDebit,
                'kredit' => $saldoAkhirKredit,
            ];

            // siapkan placeholder Total level 3 (akan dihitung belakangan)
            $data[$nama_kepala3]['Total'] = 0;
        }

        // === Rekap Total per level & penomoran seperti kode kamu ===
        $parentNo = 1;
        $newData = [];

        foreach ($data as $akunLv3 => $subLv4) {
            $newLv3 = $parentNo.'.'.$akunLv3;

            $childNo = 1;
            $parentTotalDebit = 0.0;
            $parentTotalKredit = 0.0;

            foreach ($subLv4 as $akunLv4 => $accountsLv5) {
                if ($akunLv4 === 'Total') {
                    continue;
                }

                $newLv4 = $parentNo.'.'.$childNo.'.'.$akunLv4;

                $subTotalDebit = 0.0;
                $subTotalKredit = 0.0;

                foreach ($accountsLv5 as $label5 => $balance) {
                    if ($label5 === 'Total') continue;
                    $subTotalDebit  += (float)($balance['debit']  ?? 0);
                    $subTotalKredit += (float)($balance['kredit'] ?? 0);
                }

                // set total di level 4
                $accountsLv5['Total'] = [
                    'debit'  => $subTotalDebit,
                    'kredit' => $subTotalKredit,
                ];

                $newData[$newLv3][$newLv4] = $accountsLv5;

                $parentTotalDebit  += $subTotalDebit;
                $parentTotalKredit += $subTotalKredit;

                $childNo++;
            }

            // total level 3
            $newData[$newLv3]['Total'] = [
                'debit'  => $parentTotalDebit,
                'kredit' => $parentTotalKredit,
            ];

            $parentNo++;
        }

        // (Opsional) kalau kamu masih butuh subtotal global, variabel sudah tersedia:
        // $subTotalSaldoAwalDebit, $subTotalSaldoAwalKredit, $subTotalmutasiDebit, $subTotalmutasiKredit,
        // $subTotalsaldoAkhirDebit, $subTotalsaldoAkhirKredit

        // === Paged meta (tanpa perubahan) ===
        $paged = [
            'dibuat'       => $request->input('dibuat'),
            'alamat'       => $request->input('alamat'),
            'tanggal'      => $request->input('tanggal'),
            'jabatan'      => $request->input('jabatan'),
            'jumlahLaman'  => (int) $request->input('jumlahLaman'),
        ];

        // === Generate PDF dengan Dompdf (tidak sentuh Snappy) ===
        $pdf = Dompdf::loadView('report.neraca_saldo', [
                'data'              => $newData,
                'tanggal_mulai'     => $start->format('d/m/Y'),
                'tanggal_selesai'   => $end->format('d/m/Y'),
                'paged'             => $paged,
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->stream('neraca_saldo.pdf');
    }

    private function kalNeDo(&$data)
    {
        foreach ($data as $mainCategory => &$subcategories) {
            $mainTotal = 0;
            foreach ($subcategories as $subcategory => &$accounts) {
                if ($subcategory === 'Total') continue;
                $subTotal = array_sum($accounts);
                $accounts['Total'] = $subTotal;
                $mainTotal += $subTotal;
            }
            $subcategories['Total'] = $mainTotal;
        }
    }

    //skrip asli 30 november 2024
    public function neracaPerbandingan(Request $request, $n = null){
        return $this->neraca($request, true);
        // if($request->isMethod('post')){
        //     // da($request->query('excel'));
        //     $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
        //     // return response()->json([
        //     //     'data'  => $start_date
        //     // ]);
        //     $end_date = Carbon::parse($request->input('end_date'))->endOfDay()->format('Y-m-d H:i:s');
        //     // $ttd1 = $request->input('dibuat');
        //     // $alamat = $request->input('alamat');
        //     // $tanggal = $request->input('tanggal');
        //     // $jabatan = $request->input('jabatan');
        //     // $jumlahLaman = $request->input('jumlahLaman');
        //     $paged = [
        //         'dibuat' => $request->input('dibuat'),
        //         'alamat' => $request->input('alamat'),
        //         'tanggal' => $request->input('tanggal'),
        //         'jabatan' => $request->input('jabatan'),
        //         'jumlahLaman' => (int) $request->input('jumlahLaman'),
        //     ];

        //     $labaRugi = $this->labarugi($request, 1);
        //     $data = $this->neracaFunc($end_date, $labaRugi);

        //     // da($data);
        //     if ($data instanceof \Illuminate\Http\RedirectResponse) {
        //         return $data;
        //     }
        //     if($n == true){
        //         unset($data[auth()->user()->periode - 1]);
        //     }
        //     // da($data);
        //     if(!$data){
        //         Alert::error('Oops!', 'Data tidak ditemukan');
        //         return redirect()->back();
        //     }
        //     if(isset($data[0])){
        //         unset($data[0]);
        //     }

        //     // da($data);

        //     if($request->query('excel') == 0){
        //         // return view('report.neraca', [
        //         //     'data'              => $data,
        //         //     'label'             => $n == true ? 'Neraca' : 'Neraca Perbandingan',
        //         //     'periode'           => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
        //         //     'paged'             => $paged,
        //         //     'tanggal_mulai'     => Carbon::parse($start_date)->format('d/m/Y'),
        //         //     'tanggal_selesai'   => Carbon::parse($end_date)->format('d/m/Y'),
        //         // ]);

        //         $pdf = Dompdf::loadView('report.neraca', [
        //             'data'              => $data,
        //             'label'             => $n == true ? 'Neraca' : 'Neraca Perbandingan',
        //             'periode'           => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
        //             'paged'             => $paged,
        //             'tanggal_mulai'     => Carbon::parse($start_date)->format('d/m/Y'),
        //             'tanggal_selesai'   => Carbon::parse($end_date)->format('d/m/Y'),
        //         ]);
        //         $pdf->setPaper('A4', 'portrait');
        //         if($n){
        //             return $pdf->stream('neraca.pdf');
        //         }else{
        //             return $pdf->stream('neraca perbandingan.pdf');
        //         }
        //     }else{
        //         // da($data);
        //         $label = $n == true ? 'Neraca' : 'Neraca Perbandingan';
        //         $periode = Carbon::parse($request->input('end_date'))->translatedFormat('j F Y');

        //         return Excel::download(new NeracaExport($data, $label, $periode, $paged), 'Neraca.xlsx');
        //     }
        //     // $pdf = Dompdf::loadView('report.neraca', [
        //     //     'data' => $data,
        //     //     'periode' => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
        //     //     'ttd1' => $ttd1,
        //     //     'ttd2' => $ttd2,
        //     // ]);
        //     // return $pdf->download('neraca_' . Carbon::now()->format('YmdHis') . '.pdf');
        // }
        // return view('report.views.template');
    }

    private function neracaFuncPerbandingan($tanggal, $labaRugi = null, $year){
        $jurnal = JurnalDetail::where('created_by', auth()->user()->id)
            ->where(function($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })->where('tanggal_bukti', '<=', $tanggal)->orderBy('coa_akun', 'asc')->get()->keyBy('coa_akun');

        // da($jurnal);

        if($jurnal->isEmpty()){
            return $data = [];
        }

        $coa = Coa::whereNull('is_deleted')
            ->where(function($query) {
                $query->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->whereYear('created_at', $year)
            ->where('created_by', auth()->user()->id)
            ->orderBy('nomor_akun', 'asc')
            ->get()
            ->keyBy('nomor_akun');

        // da($coa);
        if(!$jurnal->isEmpty() && !$coa->isEmpty()) {
            $data = [];
            foreach($jurnal as $row) {
                $nomorAkun = $row->coa_akun;
                $parent = $coa->get(substr($nomorAkun, 0, 1));
                $child = $coa->get(substr($nomorAkun, 0, 2));
                $subChild = $coa->get(substr($nomorAkun, 0, 3));
                $grandChild = $coa->get(substr($nomorAkun, 0, 5));
                $detail = $coa->get(substr($nomorAkun, 0, 8));

            //    da($nomorAkun);

                if($subChild === null){
                    Alert::error('Oops!', 'Akun CoA level 3 ('.substr($nomorAkun, 0 , 3).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !');
                    return redirect()->back();
                }elseif ($child === null) {
                    Alert::error('Oops!', 'Akun CoA level 2 ('.substr($nomorAkun, 0 , 2).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !');
                    return redirect()->back();
                }elseif ($grandChild === null) {
                    Alert::error('Oops!', 'Akun CoA level 4 ('.substr($nomorAkun, 0 , 5).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !');
                    return redirect()->back();
                }

                $jurnalTotals = DB::table('jurnal_details')
                ->select(DB::raw('SUM(debit) as debit, SUM(credit) as credit'))
                ->where('created_by', auth()->user()->id)
                ->whereYear('created_at', $year)
                ->where('coa_akun', 'like', substr($nomorAkun, 0, 4).'%')
                ->limit(1)
                ->first();

                // da($jurnalTotals);

                $coasTotals = DB::table('coas')
                    ->select(
                        DB::raw('SUM(saldo_awal_debit) AS saldo_awal_debit'),
                        DB::raw('SUM(saldo_awal_credit) AS saldo_awal_credit')
                    )
                    ->where('nomor_akun', 'like', substr($nomorAkun, 0, 4).'%')
                    ->where('created_by', auth()->user()->id)
                    ->whereYear('created_at', $year)
                    ->first();

                if($nomorAkun === $detail->nomor_akun) {
                    $saldo = 0;
                    $saldoAwal = 0;
                    if(in_array($detail->saldo_normal, ['debit', 'd', 'db'])) {
                        $saldo = $coasTotals->saldo_awal_debit + $jurnalTotals->debit - $jurnalTotals->credit;
                        $saldoAwal = $coasTotals->saldo_awal_debit;
                    } else {
                        $saldo = $coasTotals->saldo_awal_credit + $jurnalTotals->credit - $jurnalTotals->debit;
                        $saldoAwal = $coasTotals->saldo_awal_credit;
                    }

                    // da($saldo);

                    $data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                    $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;

                    if($saldo == 0){
                      unset($data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun]);

                      unset($data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun]);
                    }
                }
            }

        //  da($data);
            $temp = [];
            $saldo = 0;
            $saldoAwal = 0;
            $saldo_paling_awal = 0;
            // da($coa);
            foreach($coa as $nomorAkun => $coaDetail) {
                // da($coaDetail);
                if ($coaDetail->saldo_awal_debit != 0 || $coaDetail->saldo_awal_credit != 0) {
                    $parent = $coa->get(substr($nomorAkun, 0, 1));
                    $child = $coa->get(substr($nomorAkun, 0, 2));
                    $subChild = $coa->get(substr($nomorAkun, 0, 3));

                    // if($nomorAkun == '12101002'){
                    //     da($nomorAkun);
                    // }
                    $coasTotals = DB::table('coas')
                        ->select(
                            DB::raw('SUM(saldo_awal_debit) AS saldo_awal_debit'),
                            DB::raw('SUM(saldo_awal_credit) AS saldo_awal_credit')
                        )
                        // ->where('nomor_akun', 'like', substr($nomorAkun, 0, 4).'%')
                        ->where('nomor_akun', $nomorAkun)
                        ->where('created_by', auth()->user()->id)
                        ->whereYear('created_at', $year)
                        ->first();

                    $coasTotalsTahunLalu = DB::table('coas')
                        ->select(
                            DB::raw('SUM(saldo_awal_debit) AS saldo_awal_debit'),
                            DB::raw('SUM(saldo_awal_credit) AS saldo_awal_credit')
                        )
                        ->where('nomor_akun', 'like', substr($nomorAkun, 0, 4).'%')
                        ->where('created_by', auth()->user()->id)
                        ->whereYear('created_at', $year)
                        ->first();

                    // da(substr($nomorAkun, 0, 4));

                    //Lukman
                    $query = DB::select('
                        SELECT
                            SUM(js.debit) as debit,
                            SUM(js.credit) as credit
                        FROM jurnal_details js

                        WHERE
                        js.is_deleted IS NULL
                        AND js.coa_akun = ?
                        AND js.created_by = ?
                        AND YEAR(js.created_at) = ?', [$nomorAkun, auth()->user()->id, $year]);
                    $result = $query[0] ?? null;

                    // da($data);


                    // if($coaDetail->saldo_awal_debit != 0 || $coaDetail->saldo_awal_credit != 0){
                    //     if($coaDetail->saldo_normal == 'debit' && $coaDetail->saldo_awal_credit != 0){
                    //         $data[0][$child->nama_akun][$subChild->nomor_akun] = $coaDetail->saldo_awal_debit ?: $coaDetail->saldo_awal_credit;
                    //     }
                    //     // dd($data);
                    //     if($coaDetail->saldo_normal == 'credit' && $coaDetail->saldo_awal_debit != 0){
                    //         $data[0][$child->nama_akun][$subChild->nomor_akun] = $coaDetail->saldo_awal_credit ?: $coaDetail->saldo_awal_debit;
                    //     }
                    // }

                    // if($subChild->nomor_akun == '121'){
                    //     da($subChild);
                    // }
                        // $temp[] = $coasTotals;
                        if (in_array(strtolower($coaDetail->saldo_normal), ['debit','d','db'])) {
                            // debit normal
                            if ($coasTotals->saldo_awal_debit < 0) {
                                // kalau saldo awal debit sudah negatif, hitung dengan pengurangan
                                $saldo = ($result->debit - $result->credit) - abs($coasTotals->saldo_awal_debit);
                            } else {
                                // default (positif) → tambah
                                $saldo = ($coasTotals->saldo_awal_debit + $result->debit) - $result->credit;
                            }

                            // da($subChild);
                            // if($subChild->nomor_akun == '121') {
                            //     $saldo_paling_awal += $saldo;
                            // }

                            $saldoAwal = $coasTotalsTahunLalu->saldo_awal_debit ?? 0;

                        } else {
                            // credit normal
                            if ($coasTotals->saldo_awal_credit < 0) {
                                // kalau saldo awal kredit sudah negatif
                                $saldo = ($result->credit - $result->debit) - abs($coasTotals->saldo_awal_credit);
                            } else {
                                $saldo = ($coasTotals->saldo_awal_credit + $result->credit) - $result->debit;
                            }
                            $saldoAwal = $coasTotalsTahunLalu->saldo_awal_credit ?? 0;
                        }

                    // da($saldo);

                    if (!isset($data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun]) || $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] == 0) {

                        if (!isset($data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun])) {
                            $data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = 0;
                        }
                        if (!isset($data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun])) {
                            $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = 0;
                        }

                        $data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] += $saldoAwal;
                        $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] += $saldoAwal;
                    }


                    if(@$parent['golongan'] == 'Liabilitas' || @$parent['golongan'] == 'Ekuitas'){
                        $data[$year]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] ?: $saldo;
                        $data[$year - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] ?: $saldo;
                    }else{
                        $data;
                        // $data[date('Y')]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] ?: $saldo;
                        // $data[date('Y') - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] ?: $saldo;
                    }

                    if($subChild->nomor_akun == '311' || strpos($subChild->nomor_akun, '30') === 0){
                        $data[$year]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = $labaRugi;
                        $data[$year - 1]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = 0;
                    }
                }
            }

            // da($saldo_paling_awal);
            // da($data);
            // da($temp);

            foreach($data as $tahun => $rows) {
                foreach($rows as $rowKey => $row) {
                    if (isset($rows['Liabilitas dan Ekuitas']) && $rows['Liabilitas dan Ekuitas']) {
                        // Skip processing
                    } else {
                        if (isset($data[date('Y')]['2']) && isset($data[date('Y')]['3'])) {
                            $data[date('Y')]['Liabilitas dan Ekuitas'] = $data[date('Y')]['2'] + $data[date('Y')]['3'];
                            $is = array_keys($data[date('Y')]['3']);
                            if (!empty($is)) {
                                $data[date('Y')]['Liabilitas dan Ekuitas'][$is[0]]['Saldo Tahun Berjalan'] = $labaRugi;
                            }
                        }
                    }

                    if(in_array($rowKey, ['2', '3', 2, 3], true)){
                        unset($data[$tahun][$rowKey]);
                    }
                }
            }
        }

        foreach($data as $key => $value){
            foreach($value as $key2 => $value2){
                foreach($value2 as $key3 => $value3){
                    if (is_array($value3)) {
                        ksort($value3);
                        $data[$key][$key2][$key3] = $value3;
                        foreach($value3 as $key4 => $value4){
                            if (substr($key4, 0, 1) === '3') {
                                $coa = Coa::where('nomor_akun', $key4)
                                    ->whereYear('created_at', $year)
                                    ->where('created_by', auth()->user()->id)
                                    ->first();
                            } else {
                                $coa = Coa::where('nomor_akun', $key4)
                                    ->whereYear('created_at', $year)
                                    ->where('created_by', auth()->user()->id)
                                    ->first();
                            }

                            if($coa){
                                $data[$key][$key2][$key3][$coa->nama_akun] = $value4;
                                unset($data[$key][$key2][$key3][$key4]);
                            }
                        }
                    }
                }
            }
        }

        // da($temp);
        da($data);
        return $data;
    }

    private function totalNeraca($data) {
        foreach ($data as $year => $categories) {
            foreach ($categories as $category => $subCategories) {
                $categoryTotal = 0;
                foreach ($subCategories as $subCategory => $values) {
                    if (is_array($values)) {
                        $subCategoryTotal = array_sum($values);
                        $data[$year][$category][$subCategory]['Total'] = $subCategoryTotal;
                        $categoryTotal += $subCategoryTotal;
                    } else {
                        $categoryTotal += $values;
                    }
                }
                $data[$year][$category]['Total'] = $categoryTotal;
            }
        }
        return $data;
    }

    private function neracaFunction($jurnal, $coa){
        $totalPendapatanDulu = 0;
        $totalHPPDulu = 0;
        $totalBebanDulu = 0;
        $totalModalDulu = 0;
        $namaAkunDulu = '';
        $salwal = 0;
        foreach ($jurnal as $item) {
            foreach ($item->details as $detail) {
                $nomorAkun = substr($detail->coa_akun, 0, 1);
                $saldoAwal = Coa::where('nomor_akun', $detail->coa_akun)->where('created_by', auth()->user()->id)->whereNull('is_deleted')->first();
                $saldoNormal = strtolower($saldoAwal->saldo_normal);
                if($saldoNormal == 'db' || $saldoNormal == 'debit'){
                    $salwal = $saldoAwal->saldo_awal_debit;
                }else{
                    $salwal = $saldoAwal->saldo_awal_credit;
                }
                if ($nomorAkun == '4') {
                    $totalPendapatanDulu += $salwal + $detail->credit - $detail->debit;
                } elseif ($nomorAkun == '5') {
                    $totalHPPDulu += $salwal + $detail->debit - $detail->credit;
                } elseif ($nomorAkun == '6') {
                    $totalBebanDulu += $salwal + $detail->debit - $detail->credit;
                } elseif($nomorAkun == '3'){
                    $noKunLu = substr($detail->coa_akun, 0 , 4);
                    $namaAkunDulu = $coa->where('nomor_akun', $noKunLu)->first();
                    $totalModalDulu += $salwal + $detail->debit + $detail->credit;
                }
            }
        }
        // da($totalPendapatanDulu);
        $labaKotorDulu = $totalPendapatanDulu - $totalHPPDulu;
        $labaBersihDulu = $labaKotorDulu - $totalBebanDulu;
        // da($labaBersihDulu);


        $data = [];
        $saldoAwal = 0;
        foreach ($jurnal as $item) {
            foreach ($item->details as $detail) {
                $pAkun = substr($detail->coa_akun, 0, 1);
                $cAkun = substr($detail->coa_akun, 0, 2);
                $scAkun = substr($detail->coa_akun, 0, 3);

                $parent = Coa::where('nomor_akun', $pAkun)->first();
                $child = Coa::where('nomor_akun', $cAkun)->first();
                $subChild = Coa::where('nomor_akun', $scAkun)->first();

                $xCoa = Coa::where('nomor_akun', $detail->coa_akun)->where('created_by', auth()->user()->id)->whereNull('is_deleted')->first();
                $saldoNormal = strtolower($xCoa->saldo_normal);
                if($saldoNormal == 'db' || $saldoNormal == 'debit'){
                    $saldoAwal = $xCoa->saldo_awal_debit;
                }else{
                    $saldoAwal = $xCoa->saldo_awal_credit;
                }

                if (!isset($data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun])) {
                    $data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun] = $saldoAwal;
                }
                if($saldoNormal == 'db' || $saldoNormal == 'debit'){
                    $data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun] += $detail->debit - $detail->credit;
                }else{
                    $data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun] += $detail->credit - $detail->debit;
                }
            }
        }
        // da($data);

        // Gabungkan akun Liabilitas dan Ekuitas secara dinamis
        $liabilitasEkuitas = ['Liabilitas', 'Ekuitas'];
        $data['Liabilitas dan Ekuitas'] = [];

        foreach ($liabilitasEkuitas as $group) {
            if (isset($data[$group])) {
                $data['Liabilitas dan Ekuitas'] = array_merge_recursive($data['Liabilitas dan Ekuitas'], $data[$group]);
                unset($data[$group]); // Hapus data asli untuk menghindari duplikasi
            }
        }



        // Tambahkan Saldo Tahun Berjalan
        $data['Liabilitas dan Ekuitas']['Ekuitas']['Saldo Tahun Berjalan'] = $labaBersihDulu;

        return $data;
    }

    public function mutasiSaldo(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('report.views.template');
        }

        $userId = auth()->id();

        // --- Parse tanggal sekali, aman ---
        try {
            $start = Carbon::parse($request->input('start_date'))->startOfDay();
            $end   = Carbon::parse($request->input('end_date'))->endOfDay();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal tidak valid');
        }
        $jumlahLaman = $request->input('jumlahLaman');

        // --- Ambil COA level 5 milik user (sekali) ---
        $coas = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->orderBy('nomor_akun', 'asc')
            ->get(['nomor_akun','nama_akun','saldo_normal','saldo_awal_debit','saldo_awal_credit','golongan'])
            ->keyBy('nomor_akun');

        if ($coas->isEmpty()) {
            return back()->with('error', 'COA level 5 tidak ditemukan');
        }

        // --- Agregat mutasi periode (SEMUA data; hemat) ---
        $mutasiPeriode = JurnalDetail::withoutGlobalScopes()
            ->select('coa_akun',
                DB::raw('SUM(debit)  AS m_debit'),
                DB::raw('SUM(credit) AS m_credit')
            )
            ->where('created_by', $userId)
            ->whereBetween('tanggal_bukti', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('coa_akun')
            ->get()
            ->keyBy('coa_akun');

        if ($mutasiPeriode->isEmpty()) {
            // konsisten dengan perilaku lama jika tidak ada data periode
            // Alert::error('Oops!', 'Data tidak ditemukan');
            return back()->with('error', 'Data tidak ditemukan');
        }

        // --- Agregat untuk saldo awal berjalan (awal bulan → start), hanya jika perlu ---
        $mutasiAwal = collect();
        if ($start->day !== 1) {
            $awalBulan = (clone $start)->startOfMonth();
            $mutasiAwal = JurnalDetail::withoutGlobalScopes()
                ->select('coa_akun',
                    DB::raw('SUM(debit)  AS a_debit'),
                    DB::raw('SUM(credit) AS a_credit')
                )
                ->where('created_by', $userId)
                ->whereBetween('tanggal_bukti', [$awalBulan->toDateTimeString(), $start->toDateTimeString()])
                ->groupBy('coa_akun')
                ->get()
                ->keyBy('coa_akun');
        }

        // --- Bangun data per golongan & subtotal (kompatibel dengan view lama) ---
        $data = [];

        $subTotalSaldoAwalDebit   = 0.0;
        $subTotalSaldoAwalKredit  = 0.0;
        $subTotalmutasiDebit      = 0.0;
        $subTotalmutasiKredit     = 0.0;
        $subTotalsaldoAkhirDebit  = 0.0;
        $subTotalsaldoAkhirKredit = 0.0;

        foreach ($coas as $akun => $coaData) {
            $saldoNormal = strtolower((string)$coaData->saldo_normal);

            // Saldo awal statik COA + mutasi awal-bulan→start (meniru logika lama)
            $awal = $mutasiAwal->get($akun);
            $saldoAwalDebit  = (float)($coaData->saldo_awal_debit  ?? 0) + (float)($awal->a_debit  ?? 0);
            $saldoAwalKredit = (float)($coaData->saldo_awal_credit ?? 0) + (float)($awal->a_credit ?? 0);

            // Subtotal saldo awal (sesuai kode lama: hanya dari COA statik)
            $subTotalSaldoAwalDebit  += (float)($coaData->saldo_awal_debit  ?? 0);
            $subTotalSaldoAwalKredit += (float)($coaData->saldo_awal_credit ?? 0);

            // Mutasi periode (0 jika tidak ada)
            $mut = $mutasiPeriode->get($akun);
            $mutasiDebit  = (float)($mut->m_debit  ?? 0);
            $mutasiKredit = (float)($mut->m_credit ?? 0);

            $subTotalmutasiDebit  += $mutasiDebit;
            $subTotalmutasiKredit += $mutasiKredit;

            // Saldo akhir mengikuti saldo_normal
            $saldoAkhirDebit = 0.0;
            $saldoAkhirKredit = 0.0;
            if ($saldoNormal === 'db' || $saldoNormal === 'debit') {
                $saldoAkhirDebit = $saldoAwalDebit + $mutasiDebit - $mutasiKredit;
                $subTotalsaldoAkhirDebit += $saldoAkhirDebit;
            } else {
                $saldoAkhirKredit = $saldoAwalKredit + $mutasiKredit - $mutasiDebit;
                $subTotalsaldoAkhirKredit += $saldoAkhirKredit;
            }

            $gol = (string)($coaData->golongan ?? 'LAINNYA');
            if (!isset($data[$gol])) $data[$gol] = [];

            $data[$gol][$akun] = [
                'nama_akun'   => $coaData->nama_akun,
                'saldo_awal'  => ['debit' => $saldoAwalDebit,  'credit' => $saldoAwalKredit],
                'mutasi'      => ['debit' => $mutasiDebit,     'credit' => $mutasiKredit],
                'saldo_akhir' => ['debit' => $saldoAkhirDebit, 'credit' => $saldoAkhirKredit],
            ];
        }

        $subTotal = [
            'Jumlah' => [
                'nama_akun'   => 'Jumlah',
                'saldo_awal'  => ['debit' => $subTotalSaldoAwalDebit,  'kredit' => $subTotalSaldoAwalKredit],
                'mutasi'      => ['debit' => $subTotalmutasiDebit,     'kredit' => $subTotalmutasiKredit],
                'saldo_akhir' => ['debit' => $subTotalsaldoAkhirDebit, 'kredit' => $subTotalsaldoAkhirKredit],
            ],
        ];

        // --- Chunk tampilan seperti semula ---
        $chunkedData = collect($data)
            ->map(function ($items) { ksort($items); return $items; })
            ->chunk(4);

        if ($chunkedData->isNotEmpty() && optional($chunkedData->last())->isEmpty()) {
            $chunkedData->pop();
        }

        // --- Generate PDF dengan Dompdf (tidak menyentuh Snappy) ---
        $pdf = Dompdf::loadView('report.mutasi_saldo', [
            'dataChunked'     => $chunkedData,
            'subTotal'        => $subTotal,
            'tanggal_mulai'   => $start->format('d/m/Y'),
            'tanggal_selesai' => $end->format('d/m/Y'),
            'jumlahLaman'     => $jumlahLaman,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('mutasi_saldo.pdf');
    }
}

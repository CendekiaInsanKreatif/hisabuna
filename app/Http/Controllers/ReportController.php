<?php

namespace App\Http\Controllers;

use App\Exports\NeracaExport;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Barryvdh\DomPDF\Facade\Pdf as Dompdf;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class ReportController extends Controller
{
    public function printJurnalFilter(Request $request)
    {
        $dari = $request->a;
        $sampai = $request->b;
        $limit = $sampai - $dari + 1;
        $offset = $dari - 1;

        $a = Auth::user()->id;


        $users = DB::select('
            SELECT *
            FROM jurnal_headers jh
            WHERE
                jh.is_deleted IS NULL AND jh.periode = ? AND
                jh.created_by = ?
            ORDER BY jh.id ASC
            LIMIT ? OFFSET ?', [Auth::user()->periode, $a, $limit, $offset]);

        if (empty($users)) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $tgl_awal = $tgl_akhir = null;

        foreach ($users as $user) {
            if ($tgl_awal === null || $user->jurnal_tgl < $tgl_awal) {
                $tgl_awal = $user->jurnal_tgl;
            }
            if ($tgl_akhir === null || $user->jurnal_tgl > $tgl_akhir) {
                $tgl_akhir = $user->jurnal_tgl;
            }
        }

        $view = view('report.daftarjurnal', [
            'jurnal' => $users,
            'tgl_awal' => $dari,
            'tgl_akhir' => $sampai,
        ])->render();

        $pdf = Dompdf::loadHTML($view);

        return $pdf->download('daftar_jurnal_'.Carbon::now()->format('YmdHis').'.pdf');
    }

    public function daftarJurnal()
    {
        $jurnal = Jurnal::with('details')->where('created_by', Auth::user()->id)->get();
        $tgl_awal = $jurnal->min('jurnal_tgl');
        $tgl_akhir = $jurnal->max('jurnal_tgl');


        if ($jurnal->isEmpty()) {
            Alert::error('Oops!', 'Data tidak ditemukan');

            return redirect()->back();
        }

        $view = view('report.daftarjurnal', ['jurnal' => $jurnal, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir])->render();
        $pdf = Dompdf::loadHTML($view);

        return $pdf->download('daftar_jurnal_'.Carbon::now()->format('YmdHis').'.pdf');
    }

    public function transaksi($id)
    {
        $jurnal = Jurnal::with(['details' => function ($query) {
            $query->orderBy('coa_akun');
        }])->where(['id' => $id, 'created_by' => Auth::user()->id])->first();

        if ($jurnal) {
            $coaList = Coa::where(['created_by' => Auth::user()->id])->get()->keyBy('nomor_akun');

            $jurnalDetails = JurnalDetail::where('jurnal_id', $id)
                ->where('created_by', Auth::user()->id)
                ->get()
                ->groupBy(function ($detail) {
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
                        $total = $jurnalDetails->get($deNo)->sum(function ($detail) {
                            return $detail->debit - $detail->credit;
                        });
                    } else {
                        $total = $jurnalDetails->get($deNo)->sum(function ($detail) {
                            return $detail->credit - $detail->debit;
                        });
                    }

                    $jurnal['details'][$key]['parent'] = [
                        'nomor_akun' => $get[0].'-'.$get[1],
                        'nama_akun' => $coa['nama_akun'],
                        'total' => $total ?: 0,
                    ];
                }
            }
        }

        return view('report.transaksi', ['jurnal' => $jurnal]);

    }



    public function bukuBesar(Request $request)
    {
        if (! $request->isMethod('post')) {
            $coa = Coa::whereNull('is_deleted')
                ->where('level', 5)
                ->where('created_by', Auth::id())
                ->select('nomor_akun', 'nama_akun')
                ->get()
                ->toArray();

            return view('report.views.template', compact('coa'));
        }

        $userId = Auth::id();

        $defaultDates = null;
        if (! $request->filled('start_date') || ! $request->filled('end_date')) {
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

        $baseQuery = DB::table('jurnal_details as jd')
            ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
            ->where('jd.created_by', $userId)
            ->where('jh.created_by', $userId);

        if ($akun !== '') {
            $baseQuery->where('jd.coa_akun', 'like', '%'.$akun.'%');
        }

        $accounts = (clone $baseQuery)
            ->whereBetween('jd.tanggal_bukti', [$tanggalMulai.' 00:00:00', $tanggalSelesai.' 23:59:59'])
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

        $allTransactions = DB::table('jurnal_details as jd')
            ->join('jurnal_headers as jh', 'jd.jurnal_id', '=', 'jh.id')
            ->where('jd.created_by', $userId)
            ->where('jh.created_by', $userId)
            ->whereIn('jd.coa_akun', $accounts)
            ->whereBetween('jd.tanggal_bukti', [$tanggalMulai.' 00:00:00', $tanggalSelesai.' 23:59:59'])
            ->select(
                'jd.id',
                'jd.coa_akun',
                'jd.debit',
                'jd.credit',
                'jd.keterangan',
                'jd.tanggal_bukti',
                'jh.jenis',
                'jh.no_urut_transaksi'
            )
            ->orderBy('jd.coa_akun')
            ->orderBy('jd.tanggal_bukti')
            ->orderBy('jd.id')
            ->get()
            ->groupBy('coa_akun');

        $ledgers = [];

        foreach ($accounts as $acc) {
            $coaRow = $coas->get($acc);
            if (! $coaRow) {
                continue;
            }

            $isDebitNormal = in_array($coaRow->saldo_normal, ['db', 'debit'], true);
            $awalAgg = $aggAwal->get($acc);
            $sumD = (float) ($awalAgg->sum_debit ?? 0);
            $sumC = (float) ($awalAgg->sum_credit ?? 0);
            $saldoStatik = $isDebitNormal ? (float) ($coaRow->saldo_awal_debit ?? 0) : (float) ($coaRow->saldo_awal_credit ?? 0);
            $saldoPer = $saldoStatik + ($isDebitNormal ? ($sumD - $sumC) : ($sumC - $sumD));
            $running = $saldoPer;

            $accountTx = new Collection;

            $transactions = $allTransactions->get($acc, collect());

            foreach ($transactions as $r) {
                $debit = (float) $r->debit;
                $credit = (float) $r->credit;
                $running += $isDebitNormal ? ($debit - $credit) : ($credit - $debit);

                $r->coa = (object) [
                    'nama_akun' => $coaRow->nama_akun,
                    'saldo_normal' => ($coaRow->saldo_normal === 'db') ? 'debit' :
                                     (($coaRow->saldo_normal === 'cr') ? 'credit' : $coaRow->saldo_normal),
                ];
                $r->saldo = $running;

                $accountTx->push($r);
            }

            $accountTx = (object) $accountTx;
            $accountTx->saldo_per_tanggal = $saldoPer;

            $ledgers[$acc] = $accountTx;
        }

        $ledgers = collect($ledgers)->sortKeys();

        @set_time_limit(900);
        @ini_set('memory_limit', '1536M');

        SnappyPDF::setBinary('/usr/local/bin/wkhtmltopdf');

        $pdf = SnappyPDF::loadView('report.bukubesar_download', [
            'ledgers' => $ledgers,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'akun' => $akun,
        ]);

        $pdf->setPaper('A4')
            ->setOption('margin-top', 15)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 20)
            ->setOption('margin-left', 10)
            ->setOption('encoding', 'UTF-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('footer-right', '[page] dari [toPage]')
            ->setOption('footer-font-size', 9)
            ->setOption('footer-spacing', 10);

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
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="buku-besar.pdf"',
            ]
        );

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

    public function arusKas(Request $request)
    {
        if (! $request->isMethod('post')) {
            return view('report.views.template');
        }

        $start_date_input = $request->input('start_date');
        $end_date_input = $request->input('end_date');

        if (! $start_date_input || ! $end_date_input) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        try {
            $start = Carbon::parse($start_date_input)->startOfDay();
            $end = Carbon::parse($end_date_input)->endOfDay();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Format tanggal tidak valid');
        }

        $userId = Auth::id();
        $year = (int) $start->format('Y');

        $coasLv4 = Coa::where('created_by', $userId)
            ->where('level', 4)
            ->whereNull('is_deleted')
            ->get(['nomor_akun', 'nama_akun', 'arus_kas'])
            ->keyBy('nomor_akun');

        $coasLv5 = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->get(['nomor_akun', 'nama_akun', 'saldo_normal'])
            ->keyBy('nomor_akun');

        if ($coasLv5->isEmpty()) {
            return redirect()->back()->with('error', 'COA level 5 tidak ditemukan');
        }

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

        $totalKas = (float) JurnalDetail::withoutGlobalScopes()
            ->where('created_by', $userId)
            ->whereBetween('tanggal_bukti', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->where('coa_akun', 'like', '1110%')
            ->select(DB::raw('COALESCE(SUM(debit - credit),0) as delta_kas'))
            ->value('delta_kas');

        $data = [];

        foreach ($aggPeriode as $row) {
            $coa = (string) $row->coa_akun;

            if (strpos($coa, '111') === 0) {
                continue;
            }

            $lv5 = $coasLv5->get($coa);
            if (! $lv5) {
                $lv5 = $coasLv5->get(str_replace('-', '', $coa));
                if (! $lv5) {
                    continue;
                }
            }

            $isDebitNormal = in_array(strtolower((string) $lv5->saldo_normal), ['db', 'debit'], true);

            $prefix5 = substr($coa, 0, 5);
            $lv4 = $coasLv4->get($prefix5);
            if (! $lv4) {
                continue;
            }

            $kategori = $lv4->arus_kas;
            $labelKelompok = $lv4->nama_akun;

            if (! $kategori) {
                continue;
            }

            $debit = (float) $row->sum_debit;
            $credit = (float) $row->sum_credit;

            $nilai = $this->calculateCashFlowImpact($coa, $debit, $credit, $kategori, $isDebitNormal);

            if (! isset($data[$kategori])) {
                $data[$kategori] = [];
            }
            $data[$kategori][$labelKelompok] = ($data[$kategori][$labelKelompok] ?? 0) + $nilai;
        }

        $kasAwal = $this->calculateCashBalance($start->subDay()->endOfDay()->toDateTimeString(), $userId);
        $kasAkhir = $this->calculateCashBalance($end->toDateTimeString(), $userId);

        $getKas = $this->neracaFunc($end->toDateTimeString());
        $getKas = $this->array_change_key_case_recursive($getKas, CASE_LOWER);

        if ($kasAwal == 0 && $kasAkhir == 0) {
            $kasAwal = $getKas[$year - 1][1]['aset lancar']['kas dan setara kas'] ?? 0;
            $kasAkhir = $getKas[$year][1]['aset lancar']['kas dan setara kas'] ?? 0;
        }

        foreach ($data as $kategori => $rows) {
            $data[$kategori]['Total'] = array_sum($rows);
        }

        $perubahanKas = $kasAkhir - $kasAwal;

        $data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'] = $perubahanKas;
        $data['Total']['Kas dan Setara Kas Awal'] = $kasAwal;
        $data['Total']['Kas dan Setara Kas Akhir'] = $kasAkhir;

        $totalArusKas = 0;
        foreach (['Operasional', 'Investasi', 'Pendanaan'] as $kategori) {
            if (isset($data[$kategori]['Total'])) {
                $totalArusKas += $data[$kategori]['Total'];
            }
        }

        if (abs($totalArusKas - $perubahanKas) > 1) {
            error_log("Cash flow imbalance detected: Total flows = {$totalArusKas}, Actual change = {$perubahanKas}");
        }

        if (empty($data)) {
            echo "<script>alert('Oops! Data tidak ditemukan'); window.close();</script>";

            return;
        }

        $paged = [
            'dibuat' => $request->input('dibuat'),
            'alamat' => $request->input('alamat'),
            'tanggal' => $request->input('tanggal'),
            'jabatan' => $request->input('jabatan'),
            'jumlahLaman' => (int) $request->input('jumlahLaman'),
        ];

        $dataChunked = collect($data)->chunk(4);

        $pdf = Dompdf::loadView('report.aruskas', [
            'dataChunked' => $dataChunked,
            'start_date' => $start->format('d/m/Y'),
            'end_date' => $end->format('d/m/Y'),
            'paged' => $paged,
        ])
            ->setPaper('A4', 'portrait');

        return $pdf->stream('aruskas.pdf');
    }

    private function calculateCashFlowImpact($coaAccount, $debit, $credit, $category, $isDebitNormal)
    {

        $firstDigit = substr($coaAccount, 0, 1);

        switch ($category) {
            case 'Operasional':
                if ($firstDigit == '4') {
                    return $credit - $debit;
                } elseif ($firstDigit == '5' || $firstDigit == '6') {
                    return $debit - $credit;
                } elseif ($firstDigit == '1' && ! str_starts_with($coaAccount, '111')) {
                    return $isDebitNormal ? ($credit - $debit) : ($debit - $credit);
                } elseif ($firstDigit == '2') {
                    return $isDebitNormal ? ($debit - $credit) : ($credit - $debit);
                }
                break;

            case 'Investasi':
                if ($firstDigit == '1' && (str_starts_with($coaAccount, '12') || str_starts_with($coaAccount, '13'))) {
                    return $isDebitNormal ? ($credit - $debit) : ($debit - $credit);
                }
                break;

            case 'Pendanaan':
                if ($firstDigit == '2' && (str_starts_with($coaAccount, '21') || str_starts_with($coaAccount, '22'))) {
                    return $isDebitNormal ? ($debit - $credit) : ($credit - $debit);
                } elseif ($firstDigit == '3') {
                    return $isDebitNormal ? ($debit - $credit) : ($credit - $debit);
                }
                break;
        }

        return $isDebitNormal ? ($debit - $credit) : ($credit - $debit);
    }

    private function calculateCashBalance($date, $userId)
    {
        $saldoAwalKas = Coa::where('created_by', $userId)
            ->where('nomor_akun', 'like', '1110%')
            ->whereNull('is_deleted')
            ->sum('saldo_awal_debit');

        $totalTransaksiKas = JurnalDetail::withoutGlobalScopes()
            ->where('created_by', $userId)
            ->where('coa_akun', 'like', '1110%')
            ->where('tanggal_bukti', '<=', $date)
            ->select(DB::raw('COALESCE(SUM(debit - credit), 0) as total_kas'))
            ->value('total_kas');

        return (float) ($saldoAwalKas + $totalTransaksiKas);
    }

    public function labaRugiView(Request $request, $n = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            return view('report.views.template');
        }
        $start_date = Carbon::parse($request->input('start'))->format('Y-m-d H:i:s');
        $start = $request->input('start');
        $end_date = Carbon::parse($request->input('end'))->format('Y-m-d H:i:s');
        $end = $request->input('end_date');
        $ttd1 = $request->input('text_input1');
        $ttd2 = $request->input('text_input2');

        $tahunSebelumnya = date('Y');
        if ($n == 1) {
            $jurnal = Jurnal::whereNull('is_deleted')
                ->with(['details' => function ($query) use ($end_date) {
                    $query->whereRaw('LEFT(coa_akun, 1) >= ?', ['4'])
                        ->where('tanggal_bukti', '<=', $end_date);
                }])
                ->whereYear('jurnal_tgl', $tahunSebelumnya)
                ->where('created_by', Auth::user()->id)
                ->get();
        } else {
            $jurnal = Jurnal::whereNull('is_deleted')
                ->with(['details' => function ($query) use ($start_date, $end_date) {
                    $query->whereRaw('LEFT(coa_akun, 1) >= ?', ['4'])
                        ->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                }])
                ->whereYear('jurnal_tgl', $tahunSebelumnya)
                ->where('created_by', Auth::user()->id)
                ->get();
        }

        if ($jurnal->isEmpty()) {
            Alert::error('Oops!', 'Data tidak ditemukan');

            return redirect()->back();
        }

        $kategori = Coa::where('created_by', Auth::user()->id)->where('level', '=', '1')->get()->keyBy('nomor_akun')->toArray();
        $data = [];

        foreach ($jurnal as $entry) {
            foreach ($entry->details as $detail) {
                $kategoriAkun = substr($detail->coa_akun, 0, 1);
                $lv3 = substr($detail->coa_akun, 0, 3);
                if (isset($kategori[$kategoriAkun])) {
                    $parent = $kategori[$kategoriAkun];
                    $child = Coa::where(['nomor_akun' => $detail->coa_akun, 'created_by' => Auth::user()->id])->first();
                    $lv3 = Coa::where(['nomor_akun' => $lv3, 'created_by' => Auth::user()->id])->first();
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

        uksort($data, function ($a, $b) use ($kategori) {
            $order = array_flip(array_map(function ($item) {
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
        if ($n == 1) {
            return $labaRugiBersih;
        }

        return view('report.labarugi', [
            'data' => $data,
            'tahunSebelumnya' => $tahunSebelumnya,
            'kategori' => $kategori,
            'labaRugiBersih' => $labaRugiBersih,
            'ttd1' => $ttd1,
            'ttd2' => $ttd2,
            'start' => $start,
            'end' => $end,
        ]);

    }

    public function labaRugi(Request $request, $n = 0)
    {
        if (! $request->isMethod('post')) {
            return view('report.views.template');
        }

        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal tidak valid');
        }
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $userId = Auth::id();
        $year = substr($end_date, 0, 4) ?: date('Y');

        $paged = [
            'dibuat' => $request->input('dibuat'),
            'alamat' => $request->input('alamat'),
            'tanggal' => $request->input('tanggal'),
            'jabatan' => $request->input('jabatan'),
            'jumlahLaman' => (int) $request->input('jumlahLaman'),
        ];
        $tahunSebelumnya = (string) ($year - 1);

        $coasLv1 = Coa::where('created_by', $userId)
            ->where('level', 1)
            ->orderBy('nomor_akun')
            ->get(['nomor_akun', 'nama_akun', 'saldo_normal'])
            ->keyBy('nomor_akun');

        if ($coasLv1->isEmpty()) {
            return back()->with('error', 'COA level 1 tidak ditemukan');
        }

        $coasLv3 = Coa::where('created_by', $userId)
            ->where('level', 3)
            ->get(['nomor_akun', 'nama_akun'])
            ->keyBy('nomor_akun');

        $ytdStart = Carbon::parse($end_date)->copy()->startOfYear()->format('Y-m-d 00:00:00');

        $agg = JurnalDetail::withoutGlobalScopes()
            ->select('coa_akun',
                DB::raw('SUM(debit)  AS s_debit'),
                DB::raw('SUM(credit) AS s_credit')
            )
            ->where('created_by', $userId)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->whereRaw('LEFT(coa_akun, 1) BETWEEN ? AND ?', ['4', '7'])
            ->when($n == 1, function ($q) use ($ytdStart, $end_date) {
                $q->whereBetween('tanggal_bukti', [$ytdStart, $end_date]);
            }, function ($q) use ($ytdStart, $end_date) {
                $q->whereBetween('tanggal_bukti', [$ytdStart, $end_date]);
            })
            ->groupBy('coa_akun')
            ->get();

        if ($agg->isEmpty()) {
            Alert::error('Oops!', 'Data tidak ditemukan');

            return redirect()->back();
        }

        $data = [];
        $totalPendapatan = 0.0;
        $totalBeban = 0.0;

        foreach ($agg as $row) {
            $acc = (string) $row->coa_akun;
            $head = substr($acc, 0, 1);
            $lv3Key = substr($acc, 0, 3);

            $kat = $coasLv1->get($head);
            if (! $kat) {
                $fallback = [
                    '4' => ['nama' => 'Pendapatan', 'saldo_normal' => 'credit'],
                    '5' => ['nama' => 'Beban', 'saldo_normal' => 'debit'],
                    '6' => ['nama' => 'Beban', 'saldo_normal' => 'debit'],
                    '7' => ['nama' => 'Pendapatan/Beban Lain-lain', 'saldo_normal' => 'debit'],
                ];
                $katName = $fallback[$head]['nama'] ?? "Kelompok $head";
                $katSaldoNorm = $fallback[$head]['saldo_normal'] ?? 'debit';
            } else {
                $katName = $kat->nama_akun;
                $katSaldoNorm = strtolower((string) $kat->saldo_normal);
            }

            $lv3Name = optional($coasLv3->get($lv3Key))->nama_akun ?? $lv3Key;

            $sumD = (float) $row->s_debit;
            $sumC = (float) $row->s_credit;

            $isDebitNorm = in_array($katSaldoNorm, ['db', 'debit', 'd'], true);
            $nilai = $isDebitNorm ? ($sumD - $sumC) : ($sumC - $sumD);

            $data[$katName]['Jumlah'] = ($data[$katName]['Jumlah'] ?? 0) + $nilai;
            $data[$katName]['Detail'][$lv3Name] = ($data[$katName]['Detail'][$lv3Name] ?? 0) + $nilai;

            if ($isDebitNorm) {
                $totalBeban += $nilai;
            } else {
                $totalPendapatan += $nilai;
            }
        }

        if (empty($data)) {
            echo "<script>alert('Oops! Data tidak ditemukan'); window.close();</script>";

            return;
        }

        uksort($data, function ($a, $b) use ($coasLv1) {
            $order = [];
            foreach ($coasLv1 as $lv1) {
                $order[$lv1->nama_akun] = count($order);
            }

            return ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX);
        });

        $labaRugiBersih = (float) $totalPendapatan - (float) $totalBeban;

        if ($n == 1) {
            return $labaRugiBersih;
        }

        $chunkedData = collect($data)->chunk(4);
        if ($chunkedData->isNotEmpty() && optional($chunkedData->last())->isEmpty()) {
            $chunkedData->pop();
        }

        $pdf = Dompdf::loadView('report.labarugi', [
            'dataChunked' => $chunkedData,
            'tahunSebelumnya' => $tahunSebelumnya,
            'kategori' => $coasLv1->toArray(),
            'labaRugiBersih' => $labaRugiBersih,
            'paged' => $paged,
            'start' => $start,
            'end' => $end,
            'jumlahLaman' => $paged['jumlahLaman'],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('labarugi.pdf');
    }

    public function perubahanEkuitas(Request $request)
    {
        if ($request->isMethod('post')) {
            $start_date_input = $request->input('start_date');
            $end_date_input = $request->input('end_date');

            if (! $start_date_input || ! $end_date_input) {
                Alert::error('Oops!', 'Data tidak ditemukan');

                return redirect()->back();
            }

            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay()->format('Y-m-d H:i:s');

            if ($start_date) {
                $year = substr($start_date, 0, 4);
            } else {
                $year = date('Y');
            }

            $tahunSebelumnya = $year - 1;
            $tahunSekarang = $year;

            $jurnalDulu = Jurnal::whereNull('is_deleted')
                ->with(['details' => function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                }])
                ->whereYear('jurnal_tgl', $tahunSebelumnya)
                ->where('created_by', Auth::user()->id)->get();

            $jurnalSekarang = Jurnal::whereNull('is_deleted')
                ->with(['details' => function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                }])
                ->whereYear('jurnal_tgl', $tahunSekarang)
                ->where('created_by', Auth::user()->id)->get();

            if ($jurnalDulu->isEmpty() && $jurnalSekarang->isEmpty()) {
                Alert::error('Oops!', 'Data tidak ditemukan');

                return redirect()->back();
            }

            $labaRugi = $this->labaRugi($request, 1);
            $neraca = $this->neracaEkuitas($end_date, $labaRugi);

            if (@$neraca['status'] == 'error') {
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
                    ],
                ];
            } else {
                $result = [];
                foreach ($neraca as $tahun => $data) {
                    if (isset($data['Liabilitas dan Ekuitas'])) {
                        $liabilitasDanEkuitas = array_change_key_case($data['Liabilitas dan Ekuitas'], CASE_LOWER);

                        $lastItem = end($liabilitasDanEkuitas);
                        $result[$tahun] = $lastItem;
                    }

                }

            }

            foreach ($result as $tahun => $values) {
                if (isset($values['Saldo Tahun Berjalan'])) {
                    $saldo = ['Saldo Tahun Berjalan' => $values['Saldo Tahun Berjalan']];
                    unset($values['Saldo Tahun Berjalan']);
                    $result[$tahun] = array_merge($values, $saldo);
                }
            }


            $pdf = Dompdf::loadView('report.perubahanekuitas', [
                'data' => $result,
                'tahun' => $tahunSekarang,
                'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
                'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y'),
            ]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('perubahan_ekuitas.pdf');
        }

        return view('report.views.template');
    }

    public function processLevel($coa, $labaRugi)
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
                            if ($lv5['saldo_awal_debit'] != '0' || $lv5['saldo_awal_credit'] != '0') {
                                if ($lv5['saldo_normal'] == 'debit' || $lv5['saldo_normal'] == 'db') {
                                    $balance = $lv5['saldo_awal_debit'];
                                } else {
                                    $balance = $lv5['saldo_awal_credit'];
                                }

                                if ($lv5['saldo_awal_debit'] != '0' && $lv5['saldo_normal'] != 'debit') {
                                    $sisa[$lv5['nomor_akun']] = $lv5['saldo_awal_debit'] - $lv5['saldo_awal_credit'];
                                }

                                $lv4Sum += $balance;
                                $sumSisa += @$sisa[$lv5['nomor_akun']];
                            } else {
                                if ($lv5['saldo_normal'] == 'debit' || $lv5['saldo_normal'] == 'db') {
                                    $balance = $lv5['saldo_awal_debit'];
                                } else {
                                    $balance = $lv5['saldo_awal_credit'];
                                }
                                $lv4Sum += $balance;
                            }
                        }
                        if ($lv4Sum != 0 || $sumSisa != 0) {
                            $lv3Result[$lv4['nama_akun']] = $lv4Sum;
                        }
                    }
                    if (! empty($lv3Result)) {
                        $sus = array_sum(array_values($lv3Result));
                        $lv2Result[$lv3['nama_akun']] = $sus;
                        if ($lv1['nomor_akun'] == 3 || $lv1['nomor_akun'] == '3') {
                            $lv2Result['Saldo Tahun Berjalan'] = $labaRugi;
                        }
                    }
                }
                if (! empty($lv2Result)) {
                    $lv1Result[$lv2['nama_akun']] = $lv2Result;
                }
            }
            if (! empty($lv1Result)) {
                if ($lv1['golongan'] == 'Ekuitas' || $lv1['golongan'] == 'Liabilitas') {
                    if (isset($result['Liabilitas dan Ekuitas'])) {
                        $result['Liabilitas dan Ekuitas'] = array_merge_recursive($result['Liabilitas dan Ekuitas'], $lv1Result);
                    } else {
                        $result['Liabilitas dan Ekuitas'] = $lv1Result;
                    }
                } else {
                    $result[$lv1['nama_akun']] = $lv1Result;
                }

                if (@$sisa) {
                    $getSisa = array_keys($sisa);
                    $nom = substr($getSisa[0], 0, 5);
                    if ($lv4['nomor_akun'] == $nom) {
                        if ($lv1['golongan'] == 'Ekuitas' || $lv1['golongan'] == 'Liabilitas') {
                            $result['Liabilitas dan Ekuitas'][$lv1['nama_akun']]['sisa'] = @$sisa[$getSisa[0]];
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function neracaEkuitas($tanggal, $labaRugi = null)
    {
        $title = '';
        $message = '';
        $error = 0;

        $year = $tanggal ? substr($tanggal, 0, 4) : date('Y');
        $userId = Auth::id();

        $coas = Coa::whereNull('is_deleted')
            ->where('created_by', $userId)
            ->whereIn('level', [1, 2, 3, 4, 5])
            ->orderBy('nomor_akun', 'asc')
            ->get(['level', 'nomor_akun', 'nama_akun', 'saldo_normal', 'saldo_awal_debit', 'saldo_awal_credit', 'saldo_berjalan_debit', 'saldo_berjalan_credit', 'golongan', 'periode'])
            ->groupBy('level');

        $coasLv1 = ($coas[1] ?? collect())->keyBy('nomor_akun');
        $coasLv2 = ($coas[2] ?? collect())->keyBy('nomor_akun');
        $coasLv3 = ($coas[3] ?? collect())->keyBy('nomor_akun');
        $coasLv4 = ($coas[4] ?? collect())->keyBy('nomor_akun');
        $coasLv5 = ($coas[5] ?? collect())->keyBy('nomor_akun');

        if ($coasLv1->isEmpty() || $coasLv5->isEmpty()) {
            return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Master COA tidak lengkap.'];
        }

        $jdAgg4 = DB::table('jurnal_details')
            ->selectRaw('LEFT(coa_akun,4) AS k4, SUM(debit) AS debit, SUM(credit) AS credit')
            ->where('created_by', $userId)
            ->where(function ($q) {
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

        $coaAgg4 = DB::table('coas')
            ->selectRaw('LEFT(nomor_akun,4) AS k4, SUM(saldo_awal_debit) AS sad, SUM(saldo_awal_credit) AS sac')
            ->where('created_by', $userId)
            ->where(function ($q) {
                $q->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('periode', $year)
            ->groupBy('k4')
            ->get()
            ->keyBy('k4');

        $data = [];

        foreach ($coasLv5 as $nomorAkun => $coa5) {
            $h1 = substr($nomorAkun, 0, 1);
            if (! in_array($h1, ['1', '2', '3'], true)) {
                continue;
            }

            $p1 = $coasLv1->get($h1);
            $p2 = $coasLv2->get(substr($nomorAkun, 0, 2));
            $p3 = $coasLv3->get(substr($nomorAkun, 0, 3));
            $p4 = $coasLv4->get(substr($nomorAkun, 0, 5));

            if (! $p3) {
                return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Akun CoA level 3 ('.substr($nomorAkun, 0, 3).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !'];
            }
            if (! $p2) {
                return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Akun CoA level 2 ('.substr($nomorAkun, 0, 2).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !'];
            }
            if (! $p4) {
                return ['status' => 'error', 'title' => 'Oops!', 'msg' => 'Akun CoA level 4 ('.substr($nomorAkun, 0, 5).') untuk Nomor Akun: '.$nomorAkun.' tidak ditemukan !'];
            }

            $k4 = substr($nomorAkun, 0, 4);
            $jt = $jdAgg4->get($k4);
            $ct = $coaAgg4->get($k4);

            $sumDebit = (float) ($jt->debit ?? 0);
            $sumCredit = (float) ($jt->credit ?? 0);
            $sumAwalD = (float) ($ct->sad ?? 0);
            $sumAwalC = (float) ($ct->sac ?? 0);

            $isDebitNormal = in_array(strtolower((string) $coa5->saldo_normal), ['debit', 'd', 'db'], true);
            $saldo = $isDebitNormal ? ($sumAwalD + $sumDebit - $sumCredit)
                                        : ($sumAwalC + $sumCredit - $sumDebit);
            $saldoAwal = $isDebitNormal ? $sumAwalD : $sumAwalC;

            $parentKey = $p1->nomor_akun;
            $childName = $p2->nama_akun;
            $subChildKey = $p3->nomor_akun;
            $currentYear = (int) $year;
            $previousYear = $currentYear - 1;

            if ($saldo != 0) {
                $data[$currentYear][$parentKey][$childName][$subChildKey] = $saldo;
            }

            if ($saldoAwal != 0) {
                $data[$previousYear][$parentKey][$childName][$subChildKey] = $saldoAwal;
            }

            $liabEqKey = 'Liabilitas dan Ekuitas';
            $data[$currentYear][$liabEqKey][$childName][$subChildKey] = $data[$currentYear][$parentKey][$childName][$subChildKey] ?? $saldo;
            $data[$previousYear][$liabEqKey][$childName][$subChildKey] = $data[$previousYear][$parentKey][$childName][$subChildKey] ?? $saldoAwal;

            if ($subChildKey === '311' || strpos($subChildKey, '30') === 0) {
                $data[$currentYear][$liabEqKey][$childName]['Saldo Tahun Berjalan'] = $labaRugi;
                $data[$previousYear][$liabEqKey][$childName]['Saldo Tahun Berjalan'] = 0;
            }
        }

        $ekuitasLv5 = $coasLv5->filter(function ($row) {
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
            $saldo = ($row->saldo_berjalan_credit ?? 0) ?: ($row->saldo_awal_credit ?? 0);
            if ($saldo != 0) {
                $ekuitasAgg3[$k3] = ($ekuitasAgg3[$k3] ?? 0) + $saldo;
            }
        }
        if (! empty($ekuitasAgg3)) {
            $currentYear = (int) $year;
            foreach ($ekuitasAgg3 as $k3 => $saldo) {
                $data[$currentYear][3]['Ekuitas'][$k3] = $saldo;
            }
        }

        foreach ($data as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        ksort($value3);
                        $data[$key][$key2][$key3] = $value3;
                        foreach ($value3 as $key4 => $value4) {
                            $coa = Coa::where('nomor_akun', $key4)->where('created_by', Auth::user()->id)->first();
                            if ($coa) {
                                $data[$key][$key2][$key3][@$coa->nama_akun] = $value4;
                                unset($data[$key][$key2][$key3][$key4]);
                            }
                        }
                    }
                }
            }
        }

        $currentYear = (int) $year;
        $data = pruneZerosPairYears($data, $currentYear);

        return $data;
    }

    public function neracaFunc($tanggal, $labaRugi = null)
    {
        $year = substr($tanggal, 0, 4);

        $jurnal = JurnalDetail::where('created_by', Auth::user()->id)
            ->where(function ($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })
            ->where('tanggal_bukti', '<=', $tanggal)
            ->orderBy('coa_akun', 'asc')
            ->get()
            ->keyBy('coa_akun');

        $coa = Coa::whereNull('is_deleted')
            ->where(function ($query) {
                $query->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('created_by', Auth::user()->id)
            ->whereYear('created_at', $year)
            ->orderBy('nomor_akun', 'asc')
            ->get()
            ->keyBy('nomor_akun');

        if ($coa->isEmpty()) {
            return [];
        }

        $data = [];

        foreach ($coa as $nomorAkun => $coaData) {
            if ($coaData->level < 5) {
                continue;
            }

            $parent = $coa->get(substr($nomorAkun, 0, 1));
            $child = $coa->get(substr($nomorAkun, 0, 2));
            $subChild = $coa->get(substr($nomorAkun, 0, 3));

            if (! $parent || ! $child || ! $subChild) {
                continue;
            }

            $saldo = 0;
            $saldoAwal = $coaData->saldo_awal_debit + $coaData->saldo_awal_credit;

            if ($jurnal->has($nomorAkun)) {
                $jurnalEntry = $jurnal->get($nomorAkun);
                if (in_array($coaData->saldo_normal, ['debit', 'd', 'db'])) {
                    $saldo = $coaData->saldo_awal_debit + $jurnalEntry->debit - $jurnalEntry->credit;
                    $saldoAwal = $coaData->saldo_awal_debit;
                } else {
                    $saldo = $coaData->saldo_awal_credit + $jurnalEntry->credit - $jurnalEntry->debit;
                    $saldoAwal = $coaData->saldo_awal_credit;
                }
            } else {
                if (in_array($coaData->saldo_normal, ['debit', 'd', 'db'])) {
                    $saldo = $coaData->saldo_awal_debit;
                    $saldoAwal = $coaData->saldo_awal_debit;
                } else {
                    $saldo = $coaData->saldo_awal_credit;
                    $saldoAwal = $coaData->saldo_awal_credit;
                }
            }

            if ($saldo != 0 || $saldoAwal != 0) {
                $data[$year][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                $data[$year - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;

                if (@$parent->golongan == 'Liabilitas' || @$parent->golongan == 'Ekuitas') {
                    $data[$year]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                    $data[$year - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;
                }

                if ($subChild->nomor_akun == '311' || strpos($subChild->nomor_akun, '30') === 0) {
                    $data[$year]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = $labaRugi;
                    $data[$year - 1]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = 0;
                }
            }
        }

        foreach ($data as $tahun => $rows) {
            foreach ($rows as $rowKey => $row) {
                if (in_array($rowKey, ['2', '3', 2, 3], true)) {
                    unset($data[$tahun][$rowKey]);
                }
            }
        }

        foreach ($data as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        ksort($value3);
                        $data[$key][$key2][$key3] = $value3;
                        foreach ($value3 as $key4 => $value4) {
                            $coaItem = Coa::where('nomor_akun', $key4)
                                ->where('created_by', Auth::user()->id)
                                ->first();
                            if ($coaItem) {
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

    public function neraca(Request $request, $n = null)
    {
        if (! $request->isMethod('post')) {
            return view('report.views.template');
        }

        $start_date_input = $request->input('start_date');
        $end_date_input = $request->input('end_date');
        if (! $start_date_input || ! $end_date_input) {
            Alert::error('Oops!', 'Data tidak ditemukan');

            return redirect()->back();
        }
        $start_date = Carbon::parse($start_date_input)->startOfDay()->format('Y-m-d H:i:s');
        $end_date = Carbon::parse($end_date_input)->endOfDay()->format('Y-m-d H:i:s');
        $year = substr($start_date, 0, 4) ?: date('Y');

        $paged = [
            'dibuat' => $request->input('dibuat'),
            'alamat' => $request->input('alamat'),
            'tanggal' => $request->input('tanggal'),
            'jabatan' => $request->input('jabatan'),
            'jumlahLaman' => (int) $request->input('jumlahLaman'),
        ];

        $ytdStart = Carbon::parse($end_date_input)->copy()->startOfYear()->format('Y-m-d 00:00:00');
        $reqYTD = clone $request;
        $reqYTD->merge([
            'start_date' => $ytdStart,
            'end_date' => $end_date,
        ]);
        $labaRugiYTD = $this->labaRugi($reqYTD, 1);

        $data = $this->neracaFuncPerbandingan($end_date, $labaRugiYTD, $year, $start_date);
        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }
        if (! $data) {
            Alert::error('Oops!', 'Data tidak ditemukan');

            return redirect()->back();
        }
        if (isset($data[0])) {
            unset($data[0]);
        }

        $mergeGroups = function (array &$dst, array $src) {
            foreach ($src as $groupName => $leafs) {
                if (! is_array($leafs)) {
                    continue;
                }
                if (! isset($dst[$groupName])) {
                    $dst[$groupName] = [];
                }
                foreach ($leafs as $leafName => $val) {
                    if (! isset($dst[$groupName][$leafName])) {
                        $dst[$groupName][$leafName] = 0.0;
                    }
                    $dst[$groupName][$leafName] += (float) $val;
                }
            }
        };

        $equityCandidates = [];
        if (isset($data[$year]['3']) && is_array($data[$year]['3'])) {
            $equityCandidates = array_keys($data[$year]['3']);
        }

        if (isset($data[$year]) && is_array($data[$year])) {
            if (! isset($data[$year]['Liabilitas dan Ekuitas'])) {
                $data[$year]['Liabilitas dan Ekuitas'] = [];
            }
            if (isset($data[$year]['2']) && is_array($data[$year]['2'])) {
                $mergeGroups($data[$year]['Liabilitas dan Ekuitas'], $data[$year]['2']);
            }
            if (isset($data[$year]['3']) && is_array($data[$year]['3'])) {
                $mergeGroups($data[$year]['Liabilitas dan Ekuitas'], $data[$year]['3']);
            }
            unset($data[$year]['2'], $data[$year]['3']);
        }

        $curYear = (int) $year;
        $prevYear = $curYear - 1;
        if (isset($data[$prevYear]) && is_array($data[$prevYear])) {
            $combinedPrev = $data[$prevYear]['Liabilitas dan Ekuitas'] ?? [];
            if (isset($data[$prevYear]['2']) && is_array($data[$prevYear]['2'])) {
                $mergeGroups($combinedPrev, $data[$prevYear]['2']);
            }
            if (isset($data[$prevYear]['3']) && is_array($data[$prevYear]['3'])) {
                $mergeGroups($combinedPrev, $data[$prevYear]['3']);
            }
            if (! empty($combinedPrev)) {
                $data[$prevYear]['Liabilitas dan Ekuitas'] = $combinedPrev;
                unset($data[$prevYear]['2'], $data[$prevYear]['3']);
            }
        }

        if (isset($data[$year]['Liabilitas dan Ekuitas']) && is_array($data[$year]['Liabilitas dan Ekuitas'])) {
            foreach ($data[$year]['Liabilitas dan Ekuitas'] as $kelompok => &$leafs) {
                if (! is_array($leafs)) {
                    continue;
                }
                foreach ($leafs as $namaLeaf => $val) {
                    $nLower = mb_strtolower(trim($namaLeaf));
                    if (in_array($nLower, ['saldo tahun berjalan', 'laba rugi berjalan', 'laba/rugi berjalan'], true)) {
                        $leafs[$namaLeaf] = 0.0;
                    }
                }
            }
            unset($leafs);

            $equityGroupKey = null;

            foreach ($equityCandidates as $cand) {
                if (isset($data[$year]['Liabilitas dan Ekuitas'][$cand])) {
                    $equityGroupKey = $cand;
                    break;
                }
            }
            if (! $equityGroupKey) {
                foreach (array_keys($data[$year]['Liabilitas dan Ekuitas']) as $grp) {
                    $g = mb_strtolower($grp);
                    if (str_contains($g, 'ekuitas') || str_contains($g, 'aset neto') || str_contains($g, 'modal')) {
                        $equityGroupKey = $grp;
                        break;
                    }
                }
            }
            if (! $equityGroupKey) {
                $equityGroupKey = 'Ekuitas';
                if (! isset($data[$year]['Liabilitas dan Ekuitas'][$equityGroupKey])) {
                    $data[$year]['Liabilitas dan Ekuitas'][$equityGroupKey] = [];
                }
            }

            $data[$year]['Liabilitas dan Ekuitas'][$equityGroupKey]['Saldo Tahun Berjalan'] = (float) $labaRugiYTD;
        }

        $EPS = 0.000001;

        $prevNonZero = [];
        if (isset($data[$prevYear]) && is_array($data[$prevYear])) {
            foreach ($data[$prevYear] as $topKey => $groups) {
                if (! is_array($groups)) {
                    continue;
                }
                foreach ($groups as $groupName => $leafs) {
                    if (! is_array($leafs)) {
                        continue;
                    }
                    foreach ($leafs as $leafName => $val) {
                        if (is_numeric($val) && abs((float) $val) > $EPS) {
                            $prevNonZero[$leafName] = true;
                        }
                    }
                }
            }
        }

        $curNonZero = [];
        if (isset($data[$year]) && is_array($data[$year])) {
            foreach ($data[$year] as $topKey => $groups) {
                if (! is_array($groups)) {
                    continue;
                }
                foreach ($groups as $groupName => $leafs) {
                    if (! is_array($leafs)) {
                        continue;
                    }
                    foreach ($leafs as $leafName => $val) {
                        if (is_numeric($val) && abs((float) $val) > $EPS) {
                            $curNonZero[$leafName] = true;
                        }
                    }
                }
            }
        }

        if (isset($data[$year]) && is_array($data[$year])) {
            foreach ($data[$year] as $topKey => &$groups) {
                if (! is_array($groups)) {
                    continue;
                }
                foreach ($groups as $groupName => &$leafs) {
                    if (! is_array($leafs)) {
                        continue;
                    }
                    foreach ($leafs as $leafName => $val) {
                        if (is_numeric($val) && abs((float) $val) <= $EPS && empty($prevNonZero[$leafName])) {
                            unset($leafs[$leafName]);
                        }
                    }
                    if (empty($leafs)) {
                        unset($groups[$groupName]);
                    }
                }
                unset($leafs);
                if (empty($groups)) {
                    unset($data[$year][$topKey]);
                }
            }
            unset($groups);
        }

        if (isset($data[$prevYear]) && is_array($data[$prevYear])) {
            foreach ($data[$prevYear] as $topKey => &$groups) {
                if (! is_array($groups)) {
                    continue;
                }
                foreach ($groups as $groupName => &$leafs) {
                    if (! is_array($leafs)) {
                        continue;
                    }
                    foreach ($leafs as $leafName => $val) {
                        if (is_numeric($val) && abs((float) $val) <= $EPS && empty($curNonZero[$leafName])) {
                            unset($leafs[$leafName]);
                        }
                    }
                    if (empty($leafs)) {
                        unset($groups[$groupName]);
                    }
                }
                unset($leafs);
                if (empty($groups)) {
                    unset($data[$prevYear][$topKey]);
                }
            }
            unset($groups);
        }

        if ($n === true && isset($data[$year]) && is_array($data[$year])) {
            foreach ($data[$year] as $key => &$groups) {
                foreach ($groups as $subKey => $subGroup) {
                    if (is_array($subGroup) && count($subGroup) > 0) {
                        $allZero = true;
                        foreach ($subGroup as $val) {
                            if ((float) $val !== 0.0) {
                                $allZero = false;
                                break;
                            }
                        }
                        if ($allZero) {
                            unset($groups[$subKey]);
                        }
                    }
                }
                if (empty($groups)) {
                    unset($data[$year][$key]);
                }
            }
            unset($groups);
        }
        if ($n === true && isset(Auth::user()->periode)) {
            unset($data[Auth::user()->periode - 1]);
        }


        if ((int) $request->query('excel', 0) === 0) {
            $pdf = Dompdf::loadView('report.neraca', [
                'data' => $data,
                'label' => $n ? 'Neraca' : 'Neraca Perbandingan',
                'periode' => Carbon::parse($end_date_input)->translatedFormat('j F Y'),
                'paged' => $paged,
                'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
                'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y'),
            ]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream($n ? 'neraca.pdf' : 'neraca perbandingan.pdf');
        } else {
            $label = $n ? 'Neraca' : 'Neraca Perbandingan';
            $periode = Carbon::parse($end_date_input)->translatedFormat('j F Y');

            return Excel::download(new NeracaExport($data, $label, $periode, $paged), 'Neraca.xlsx');
        }
    }

    public function neracaSaldo(Request $request)
    {
        if (! $request->isMethod('post')) {
            return view('report.views.template');
        }

        $userId = Auth::id();

        try {
            $start = Carbon::parse($request->input('start_date'))->startOfDay();
            $end = Carbon::parse($request->input('end_date'))->endOfDay();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal tidak valid');
        }

        $ytdStart = (clone $end)->startOfYear();

        $coasLv5 = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->orderBy('nomor_akun', 'asc')
            ->get(['nomor_akun', 'nama_akun', 'saldo_normal', 'saldo_awal_debit', 'saldo_awal_credit'])
            ->keyBy('nomor_akun');

        if ($coasLv5->isEmpty()) {
            return back()->with('error', 'COA level 5 tidak ditemukan');
        }

        $coasLv3 = Coa::where('created_by', $userId)
            ->where('level', 3)
            ->get(['nomor_akun', 'nama_akun'])
            ->keyBy('nomor_akun');

        $coasLv4 = Coa::where('created_by', $userId)
            ->where('level', 4)
            ->get(['nomor_akun', 'nama_akun'])
            ->keyBy('nomor_akun');

        $mutasiYTD = JurnalDetail::withoutGlobalScopes()
            ->select(
                'coa_akun',
                DB::raw('SUM(debit)  AS m_debit'),
                DB::raw('SUM(credit) AS m_credit')
            )
            ->where('created_by', $userId)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->whereBetween('tanggal_bukti', [$ytdStart->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('coa_akun')
            ->get()
            ->keyBy('coa_akun');

        if ($mutasiYTD->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $data = [];

        $grandDebit = 0.0;
        $grandKredit = 0.0;

        foreach ($coasLv5 as $akun5 => $coa5) {
            $head = substr($akun5, 0, 1);
            $isPL = ($head >= '4');

            $mut = $mutasiYTD->get($akun5);
            $mD = (float) ($mut->m_debit ?? 0);
            $mC = (float) ($mut->m_credit ?? 0);

            $saD = $isPL ? 0.0 : (float) ($coa5->saldo_awal_debit ?? 0);
            $saC = $isPL ? 0.0 : (float) ($coa5->saldo_awal_credit ?? 0);

            $signed = ($saD - $saC) + ($mD - $mC);

            $saldoDebit = $signed >= 0 ? abs($signed) : 0.0;
            $saldoKredit = $signed < 0 ? abs($signed) : 0.0;

            if ($saldoDebit == 0.0 && $saldoKredit == 0.0) {
                continue;
            }

            $k3 = substr($akun5, 0, 3);
            $k4 = substr($akun5, 0, 5);

            $nama_k3 = optional($coasLv3->get($k3))->nama_akun ?? $k3;
            $nama_k4 = optional($coasLv4->get($k4))->nama_akun ?? $k4;
            $nama_k5 = $coa5->nama_akun;

            $label5 = $akun5.' - '.$nama_k5;

            $data[$nama_k3] ??= [];
            $data[$nama_k3][$nama_k4] ??= [];
            $data[$nama_k3][$nama_k4][$label5] = [
                'debit' => $saldoDebit,
                'kredit' => $saldoKredit,
            ];

            $grandDebit += $saldoDebit;
            $grandKredit += $saldoKredit;
        }

        $parentNo = 1;
        $newData = [];

        foreach ($data as $lvl3Name => $groupL4) {
            $lvl3Key = $parentNo.'.'.$lvl3Name;

            $childNo = 1;
            $sumL3D = 0.0;
            $sumL3K = 0.0;

            foreach ($groupL4 as $lvl4Name => $itemsL5) {
                $lvl4Key = $parentNo.'.'.$childNo.'.'.$lvl4Name;

                $sumL4D = 0.0;
                $sumL4K = 0.0;
                foreach ($itemsL5 as $label5 => $row) {
                    if ($label5 === 'Total') {
                        continue;
                    }
                    $sumL4D += (float) ($row['debit'] ?? 0);
                    $sumL4K += (float) ($row['kredit'] ?? 0);
                }

                $itemsL5['Total'] = [
                    'debit' => $sumL4D,
                    'kredit' => $sumL4K,
                ];

                $newData[$lvl3Key][$lvl4Key] = $itemsL5;

                $sumL3D += $sumL4D;
                $sumL3K += $sumL4K;
                $childNo++;
            }

            $newData[$lvl3Key]['Total'] = [
                'debit' => $sumL3D,
                'kredit' => $sumL3K,
            ];

            $parentNo++;
        }

        $paged = [
            'dibuat' => $request->input('dibuat'),
            'alamat' => $request->input('alamat'),
            'tanggal' => $request->input('tanggal'),
            'jabatan' => $request->input('jabatan'),
            'jumlahLaman' => (int) $request->input('jumlahLaman'),
        ];

        $pdf = Dompdf::loadView('report.neraca_saldo', [
            'data' => $newData,
            'tanggal_mulai' => $start->format('d/m/Y'),
            'tanggal_selesai' => $end->format('d/m/Y'),
            'paged' => $paged,
            'grand_debit' => $grandDebit,
            'grand_kredit' => $grandKredit,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('neraca_saldo.pdf');
    }

    public function neracaPerbandingan(Request $request, $n = null)
    {
        return $this->neraca($request, true);
    }

    private function neracaFuncPerbandingan($tanggal, $labaRugi, $year, $tanggalMulai = null)
    {
        $userId = Auth::user()->id;

        $ytdStart = Carbon::parse($tanggal)->copy()->startOfYear()->format('Y-m-d 00:00:00');

        $coas = Coa::whereNull('is_deleted')
            ->where('created_by', $userId)
            ->where('periode', $year)
            ->where(function ($q) {
                $q->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->orderBy('nomor_akun', 'asc')
            ->get()
            ->keyBy('nomor_akun');

        if ($coas->isEmpty()) {
            return [];
        }

        $getNode = function (string $acc, int $len) use ($coas) {
            return $coas->get(substr($acc, 0, $len));
        };

        $jurnalYTD = DB::table('jurnal_details')
            ->selectRaw('coa_akun, COALESCE(SUM(debit),0) AS sum_debit, COALESCE(SUM(credit),0) AS sum_credit')
            ->where('created_by', $userId)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->where(function ($q) {
                $q->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })
            ->whereBetween('tanggal_bukti', [$ytdStart, $tanggal])
            ->groupBy('coa_akun')
            ->get()
            ->keyBy('coa_akun');

        $data = [];
        foreach ($coas as $nomorAkun => $coa) {
            if ((int) ($coa->level ?? 0) !== 5) {
                continue;
            }

            $head = substr($nomorAkun, 0, 1);
            if (! in_array($head, ['1', '2', '3'], true)) {
                continue;
            }

            $parent = $getNode($nomorAkun, 1);
            $child = $getNode($nomorAkun, 2);
            $subChild = $getNode($nomorAkun, 3);
            $grandChild = $getNode($nomorAkun, 5);

            if (! $parent || ! $child || ! $subChild) {
                continue;
            }

            $mut = $jurnalYTD->get($nomorAkun);
            $sumD = (float) ($mut->sum_debit ?? 0);
            $sumC = (float) ($mut->sum_credit ?? 0);

            $saD = (float) ($coa->saldo_awal_debit ?? 0);
            $saC = (float) ($coa->saldo_awal_credit ?? 0);

            $saldoAwalNorm = 0.0;
            $saldoAkhir = 0.0;
            $sn = strtolower((string) ($coa->saldo_normal ?? 'debit'));

            if (in_array($sn, ['db', 'd', 'debit'], true)) {
                $saldoAwalNorm = $saD - $saC;
                $saldoAkhir = $saldoAwalNorm + ($sumD - $sumC);
            } else {
                $saldoAwalNorm = $saC - $saD;
                $saldoAkhir = $saldoAwalNorm + ($sumC - $sumD);
            }

            $parentKey = $parent->nomor_akun;
            $childKey = $child->nama_akun;
            $subChildKey = $subChild->nomor_akun;

            $data[$year] ??= [];
            $data[$year][$parentKey] ??= [];
            $data[$year][$parentKey][$childKey] ??= [];
            $data[$year][$parentKey][$childKey][$subChildKey] = ($data[$year][$parentKey][$childKey][$subChildKey] ?? 0) + $saldoAkhir;

            $data[$year - 1] ??= [];
            $data[$year - 1][$parentKey] ??= [];
            $data[$year - 1][$parentKey][$childKey] ??= [];
            $data[$year - 1][$parentKey][$childKey][$subChildKey] = ($data[$year - 1][$parentKey][$childKey][$subChildKey] ?? 0) + $saldoAwalNorm;
        }

        foreach ($data as $th => $top) {
            foreach ($top as $pKey => $groups) {
                foreach ($groups as $gKey => $leafs) {
                    foreach ($leafs as $leafKey => $val) {
                        if ((float) $val === 0.0) {
                            unset($data[$th][$pKey][$gKey][$leafKey]);
                        }
                    }
                    if (empty($data[$th][$pKey][$gKey])) {
                        unset($data[$th][$pKey][$gKey]);
                    }
                }
                if (empty($data[$th][$pKey])) {
                    unset($data[$th][$pKey]);
                }
            }
        }

        foreach ($data as $th => $top) {
            foreach ($top as $pKey => $groups) {
                foreach ($groups as $gKey => $leafs) {
                    $sorted = [];
                    ksort($leafs);
                    foreach ($leafs as $subKey => $amount) {
                        $coa3 = $coas->get($subKey);
                        $name = $coa3 ? ($coa3->nama_akun ?? $subKey) : $subKey;
                        $sorted[$name] = $amount;
                    }
                    $data[$th][$pKey][$gKey] = $sorted;
                }
            }
        }


        return $data;
    }

    public function mutasiSaldo(Request $request)
    {
        if (! $request->isMethod('post')) {
            return view('report.views.template');
        }

        $userId = Auth::id();

        try {
            $start = Carbon::parse($request->input('start_date'))->startOfDay();
            $end = Carbon::parse($request->input('end_date'))->endOfDay();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal tidak valid');
        }
        $jumlahLaman = $request->input('jumlahLaman');

        $coas = Coa::where('created_by', $userId)
            ->where('level', 5)
            ->whereNull('is_deleted')
            ->orderBy('nomor_akun', 'asc')
            ->get(['nomor_akun', 'nama_akun', 'saldo_normal', 'saldo_awal_debit', 'saldo_awal_credit', 'golongan'])
            ->keyBy('nomor_akun');

        if ($coas->isEmpty()) {
            return back()->with('error', 'COA level 5 tidak ditemukan');
        }

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


        $mutasiAwal = collect();
        $awalTahun = (clone $start)->startOfYear();

        if ($start > $awalTahun) {
            $sebelumStart = (clone $start)->subDay()->endOfDay();
            $mutasiAwal = JurnalDetail::withoutGlobalScopes()
                ->select('coa_akun',
                    DB::raw('SUM(debit)  AS a_debit'),
                    DB::raw('SUM(credit) AS a_credit')
                )
                ->where('created_by', $userId)
                ->whereBetween('tanggal_bukti', [$awalTahun->toDateTimeString(), $sebelumStart->toDateTimeString()])
                ->groupBy('coa_akun')
                ->get()
                ->keyBy('coa_akun');
        }

        $data = [];

        $subTotalSaldoAwalDebit = 0.0;
        $subTotalSaldoAwalKredit = 0.0;
        $subTotalmutasiDebit = 0.0;
        $subTotalmutasiKredit = 0.0;
        $subTotalsaldoAkhirDebit = 0.0;
        $subTotalsaldoAkhirKredit = 0.0;

        foreach ($coas as $akun => $coaData) {
            $saldoNormal = strtolower((string) $coaData->saldo_normal);
            $golongan = substr($akun, 0, 1);

            $awal = $mutasiAwal->get($akun);
            $mutasiAwalDebit = (float) ($awal->a_debit ?? 0);
            $mutasiAwalKredit = (float) ($awal->a_credit ?? 0);

            $saldoAwalDebit = 0.0;
            $saldoAwalKredit = 0.0;

            if ($saldoNormal === 'db' || $saldoNormal === 'debit') {
                $saldoAwalBersih = (float) ($coaData->saldo_awal_debit ?? 0) + $mutasiAwalDebit - $mutasiAwalKredit;
                if ($saldoAwalBersih >= 0) {
                    $saldoAwalDebit = $saldoAwalBersih;
                } else {
                    $saldoAwalKredit = abs($saldoAwalBersih);
                }
            } else {
                $saldoAwalBersih = (float) ($coaData->saldo_awal_credit ?? 0) + $mutasiAwalKredit - $mutasiAwalDebit;
                if ($saldoAwalBersih >= 0) {
                    $saldoAwalKredit = $saldoAwalBersih;
                } else {
                    $saldoAwalDebit = abs($saldoAwalBersih);
                }
            }

            $subTotalSaldoAwalDebit += $saldoAwalDebit;
            $subTotalSaldoAwalKredit += $saldoAwalKredit;

            $mut = $mutasiPeriode->get($akun);
            $mutasiDebit = (float) ($mut->m_debit ?? 0);
            $mutasiKredit = (float) ($mut->m_credit ?? 0);

            $subTotalmutasiDebit += $mutasiDebit;
            $subTotalmutasiKredit += $mutasiKredit;

            $saldoAkhirDebit = 0.0;
            $saldoAkhirKredit = 0.0;

            if ($saldoNormal === 'db' || $saldoNormal === 'debit') {
                $saldoAkhirBersih = $saldoAwalDebit - $saldoAwalKredit + $mutasiDebit - $mutasiKredit;
                if ($saldoAkhirBersih >= 0) {
                    $saldoAkhirDebit = $saldoAkhirBersih;
                } else {
                    $saldoAkhirKredit = abs($saldoAkhirBersih);
                }
                $subTotalsaldoAkhirDebit += $saldoAkhirDebit;
                $subTotalsaldoAkhirKredit += $saldoAkhirKredit;
            } else {
                $saldoAkhirBersih = $saldoAwalKredit - $saldoAwalDebit + $mutasiKredit - $mutasiDebit;
                if ($saldoAkhirBersih >= 0) {
                    $saldoAkhirKredit = $saldoAkhirBersih;
                } else {
                    $saldoAkhirDebit = abs($saldoAkhirBersih);
                }
                $subTotalsaldoAkhirDebit += $saldoAkhirDebit;
                $subTotalsaldoAkhirKredit += $saldoAkhirKredit;
            }

            $gol = (string) ($coaData->golongan ?? 'LAINNYA');
            if (! isset($data[$gol])) {
                $data[$gol] = [];
            }

            $data[$gol][$akun] = [
                'nama_akun' => $coaData->nama_akun,
                'saldo_awal' => ['debit' => $saldoAwalDebit,  'credit' => $saldoAwalKredit],
                'mutasi' => ['debit' => $mutasiDebit,     'credit' => $mutasiKredit],
                'saldo_akhir' => ['debit' => $saldoAkhirDebit, 'credit' => $saldoAkhirKredit],
            ];
        }

        $subTotal = [
            'Jumlah' => [
                'nama_akun' => 'Jumlah',
                'saldo_awal' => ['debit' => $subTotalSaldoAwalDebit,  'kredit' => $subTotalSaldoAwalKredit],
                'mutasi' => ['debit' => $subTotalmutasiDebit,     'kredit' => $subTotalmutasiKredit],
                'saldo_akhir' => ['debit' => $subTotalsaldoAkhirDebit, 'kredit' => $subTotalsaldoAkhirKredit],
            ],
        ];

        $chunkedData = collect($data)
            ->map(function ($items) {
                ksort($items);

                return $items;
            })
            ->chunk(4);

        if ($chunkedData->isNotEmpty() && optional($chunkedData->last())->isEmpty()) {
            $chunkedData->pop();
        }

        $pdf = Dompdf::loadView('report.mutasi_saldo', [
            'dataChunked' => $chunkedData,
            'subTotal' => $subTotal,
            'tanggal_mulai' => $start->format('d/m/Y'),
            'tanggal_selesai' => $end->format('d/m/Y'),
            'jumlahLaman' => $jumlahLaman,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('mutasi_saldo.pdf');
    }
}

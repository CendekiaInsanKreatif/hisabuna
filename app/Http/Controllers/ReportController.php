<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Saldo;
use App\Models\User;

class ReportController extends Controller
{
    public function daftarJurnal()
    {
        $jurnal = Jurnal::with('details')->where('created_by', auth()->user()->id)->get();
        $tgl_awal = $jurnal->min('jurnal_tgl');
        $tgl_akhir = $jurnal->max('jurnal_tgl');

        if($jurnal->isEmpty()){
            return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
        }

        $view = view('report.daftarjurnal', ['jurnal' => $jurnal, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir])->render();
        $pdf = PDF::loadHTML($view);
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
        // $pdf = PDF::loadView('report.transaksi', ['jurnal' => $jurnal]);
        // return $pdf->download('transaksi_jurnal_' . $id . '_' . Carbon::now()->format('YmdHis') . '.pdf');
    }


    // public function downloadBukuBesar(Request $request){
        
    // }

    public function bukuBesar(Request $request)
    {
        if($request->isMethod('post')){
            $tanggalMulai = Carbon::createFromFormat('d-m-Y', trim($request->input('start_date', Carbon::parse(JurnalDetail::where('created_by', auth()->user()->id)->min('tanggal_bukti'))->format('d-m-Y'))))->format('Y-m-d');
            $tanggalSelesai = Carbon::createFromFormat('d-m-Y', trim($request->input('end_date', Carbon::parse(JurnalDetail::where('created_by', auth()->user()->id)->max('tanggal_bukti'))->format('d-m-Y'))))->format('Y-m-d');
            $akun = $request->input('akun', '');

            // da($request->all());


            $query = JurnalDetail::query()->with('coa')
                ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
                ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan', 'jurnal_details.tanggal_bukti')
                ->where('jurnal_headers.created_by', auth()->user()->id);

            if (!empty($akun)) {
                $akun = str_replace('-', '', $akun);
                $query->where('jurnal_details.coa_akun', 'like', '%' . $akun . '%');
            }

            if ($request->has('start_date') || $request->has('end_date')) {
                try {
                    $startDate = Carbon::parse($tanggalMulai)->startOfDay();
                } catch (\Exception $e) {
                    $startDate = Carbon::now()->startOfDay();
                }

                try {
                    $endDate = Carbon::parse($tanggalSelesai)->endOfDay();
                } catch (\Exception $e) {
                    $endDate = Carbon::now()->endOfDay();
                }

                $query->whereBetween('jurnal_details.tanggal_bukti', [$startDate, $endDate]);
            }

            $en = $query->orderBy('jurnal_details.tanggal_bukti')
                ->get()
                ->groupBy('coa_akun');

            $tanggalPertama = JurnalDetail::where('created_by', auth()->user()->id)
                ->orderBy('tanggal_bukti', 'asc')
                ->value('tanggal_bukti');
            $xa = $tanggalPertama ?: Carbon::now()->format('Y-m-d');
            $getPerSaldo = JurnalDetail::where('created_by', auth()->user()->id)
                ->whereBetween('tanggal_bukti', [
                    Carbon::parse($xa)->format('Y-m-d'),
                    Carbon::parse($tanggalMulai)->subDay()->format('Y-m-d')
                ])
                ->selectRaw('coa_akun, SUM(debit) as debit, SUM(credit) as kredit')
                ->groupBy('coa_akun')
                ->orderBy('coa_akun', 'asc')
                ->get()
                ->keyBy('coa_akun');

            $coas = Coa::where('created_by', auth()->user()->id)->get()->keyBy('nomor_akun');
            $jurnalx = [];
            foreach ($en as $coaAkun => $transactions) {
                $coa = $coas->get($coaAkun);

                if($coa->saldo_normal == 'db' || $coa->saldo_normal == 'debit'){
                    $saldoAwal = $coa->saldo_awal_debit;
                    $saldoPer = $coa->saldo_awal_debit + @$getPerSaldo[$coaAkun]['debit'] - @$getPerSaldo[$coaAkun]['kredit'];
                }else{
                    $saldoAwal = $coa->saldo_awal_credit;
                    $saldoPer = $coa->saldo_awal_credit + @$getPerSaldo[$coaAkun]['kredit'] - @$getPerSaldo[$coaAkun]['debit'];
                }

                $saldoKumulatif = $saldoPer;
                
                foreach ($transactions as $transaction) {
                    if($coa->saldo_normal == 'db' || $coa->saldo_normal == 'debit'){
                        $saldoKumulatif += $transaction->debit - $transaction->credit;
                        
                    }else{
                        $saldoKumulatif += $transaction->credit - $transaction->debit;
                    }

                    $transaction->saldo = $saldoKumulatif;
                    $transaction->tanggal_bukti = Carbon::parse($transaction->tanggal_bukti)->format('Y-m-d H:i:s');
                }

                $jurnalx[$coaAkun] = $transactions;
                $jurnalx[$coaAkun]->saldo_per_tanggal = $saldoPer;
            }

            $jurnalx = collect($jurnalx)->sortBy(function($transactions, $coaAkun) {
                return $coaAkun;
            });


            return view('report.bukubesar_download', ['ledgers' => $jurnalx,'tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'akun' => $akun]);
        }

        return view('report.views.template');
    }

    public function arusKas(Request $request)
    {
        if ($request->isMethod('post')) {
            // da($request);
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');

            $jurnal = Jurnal::whereNull('is_deleted')
                        ->with(['details' => function($query) use ($start_date, $end_date) {
                            $query->where('coa_akun', '>', '1')
                                  ->where('tanggal_bukti', '>=', $start_date)
                                  ->where('tanggal_bukti', '<=', $end_date)
                                  ->orderBy('coa_akun');
                        }])
                        ->where('created_by', auth()->user()->id)
                        ->get();

            if ($jurnal->isEmpty()) {
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $coas = Coa::where('created_by', auth()->user()->id)
                        ->where('nomor_akun', 'not like', '111%')
                        ->orderBy('nomor_akun')
                        ->get()
                        ->keyBy('nomor_akun');

            $data = [];
            $totalKas = 0;
            foreach ($jurnal as $entry) {
                foreach ($entry->details as $detail) {
                    $parent = $coas->get(substr($detail->coa_akun, 0, 1));
                    $child = $coas->get($detail->coa_akun);
                    $aruskas = $coas->get(substr($detail->coa_akun, 0, 5));
                    $lv5 = $coas->get(substr($detail->coa_akun, 0, 8));
                    if ($lv5) {
                        if ( $lv5->saldo_normal == 'db' ||  $lv5->saldo_normal == 'debit') {
                            $nilai = $detail->debit - $detail->credit;
                        } else {
                            $nilai = $detail->credit - $detail->debit;
                        }

                        $kategori = $aruskas->arus_kas;

                        if ($kategori) {
                            $data[$aruskas->arus_kas][$aruskas->nama_akun] = ($data[$aruskas->arus_kas][$aruskas->nama_akun] ?? 0) + $nilai;
                        }
                    }
                    if (strpos($detail->coa_akun, '1110') === 0) {
                        $totalKas += $detail->debit - $detail->credit;
                    }
                }
            }

            $getKas = $this->neracaFunc($end_date);
            foreach ($data as $key => $value) {
                $data[$key]['Total'] = array_sum($value);
                $data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'] = $totalKas;
                $data['Total']['Kas dan Setara Kas Awal'] = $getKas[date('Y') - 1][1]['Aset Lancar']['Kas dan Setara Kas'];
                $data['Total']['Kas dan Setara Kas Akhir'] = $getKas[date('Y')][1]['Aset Lancar']['Kas dan Setara Kas'];
                // da($getKas);
            }


            // da($data);

            // foreach ($data as $kas => $value) {
            //     if($value['Jumlah'] == 0){
            //         unset($data[$kas]);
            //     }
            // }

            // da($request);

            return view('report.aruskas', [
                'data' => $data,
                'start_date' => Carbon::parse($start_date)->format('d/m/Y'),
                'end_date' => Carbon::parse($end_date)->format('d/m/Y'),
            ]);

            // $pdf = PDF::loadView('report.aruskas', [
            //     'data' => $data,
            //     'start_date' => $start_date,
            //     'end_date' => $end_date,
            // ]);

            // return $pdf->download('aruskas_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
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
            return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
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
        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $start      = $request->input('start_date');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
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
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
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
            // $pdf = PDF::loadView('report.labarugi', [
            //     'data' => $data,
            //     'tahunSebelumnya' => $tahunSebelumnya,
            //     'kategori' => $kategori,
            //     'labaRugiBersih' => $labaRugiBersih,
            //     'ttd1' => $ttd1,
            //     'ttd2' => $ttd2,
            //     'start' => $start,
            //     'end'   => $end,
            // ]);
            // return $pdf->download('labarugi_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
    }

    public function perubahanEkuitas(Request $request) {
        if($request->isMethod('post')){
            $tahunSebelumnya = date('Y') - 1;
            $tahunSekarang = date('Y');
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');

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
            // $coa = DB::table('coas')
            //         ->select(
            //             DB::raw('SUM(saldo_awal_debit) AS saldo_awal_debit'),
            //             DB::raw('SUM(saldo_awal_credit) AS saldo_awal_credit')
            //         )
            //         ->where('created_by', auth()->user()->id)
            //         ->get();
            // da($coa);

            if($jurnalDulu->isEmpty() && $jurnalSekarang->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $labaRugi = $this->labarugi($request, 1);
            $neraca = $this->neracaFunc($end_date, $labaRugi);

            $result = [];
            // da($neraca);
            foreach ($neraca as $year => $values) {
                foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($values)) as $key => $value) {
                    if ($key === 'Saldo Tahun Berjalan') {
                        $result[$year]['Saldo Tahun Berjalan'] = $value;
                    } elseif (strpos($key, 'Aset Neto') !== false) {
                        $result[$year][$key] = $value;
                    }
                }
                if ($year === 0 && isset($result[date('Y')]['Saldo Tahun Berjalan'])) {
                    $result[0]['Saldo Tahun Berjalan'] = $result[date('Y')]['Saldo Tahun Berjalan'];
                }
            }

            // da($result);
            // da($data[$tahunSekarang][$totalsSekarang['namaAkun']]);
            // da($data);
            return view('report.perubahanekuitas', [
                'data' => $result,
                'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
                'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y'),
            ]);

            // $pdf = PDF::loadView('report.perubahanekuitas', [
            //     'data' => $result,
            //     'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
            //     'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y'),
            // ]);
            // return $pdf->download('perubahanekuitas_' . Carbon::now()->format('YmdHis') . '.pdf');
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

            // da($result);

        return $result;
    }

    private function neracaFunc($tanggal, $labaRugi = null){
        $jurnal = JurnalDetail::where('created_by', auth()->user()->id)
            ->where(function($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })->where('tanggal_bukti', '<=', $tanggal)->orderBy('coa_akun', 'asc')->get()->keyBy('coa_akun');

        $coa = Coa::whereNull('is_deleted')
            ->where(function($query) {
                $query->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('created_by', auth()->user()->id)
            ->orderBy('nomor_akun', 'asc')
            ->get()
            ->keyBy('nomor_akun');

        if(!$jurnal->isEmpty() && !$coa->isEmpty()) {
            $data = [];
            foreach($jurnal as $row) {
                $nomorAkun = $row->coa_akun;
                $parent = $coa->get(substr($nomorAkun, 0, 1));
                $child = $coa->get(substr($nomorAkun, 0, 2));
                $subChild = $coa->get(substr($nomorAkun, 0, 3));
                $grandChild = $coa->get(substr($nomorAkun, 0, 5));
                $detail = $coa->get(substr($nomorAkun, 0, 8));

                $jurnalTotals = DB::table('jurnal_details')
                ->select(DB::raw('SUM(debit) as debit, SUM(credit) as credit'))
                ->where('created_by', auth()->user()->id)
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
                    $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                    $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;
                }
            }

            // da($data);

            foreach($coa as $nomorAkun => $coaDetail) {
                if ($coaDetail->saldo_awal_debit > 0 || $coaDetail->saldo_awal_credit > 0) {
                    $parent = $coa->get(substr($nomorAkun, 0, 1));
                    $child = $coa->get(substr($nomorAkun, 0, 2));
                    $subChild = $coa->get(substr($nomorAkun, 0, 3));

                    $coasTotals = DB::table('coas')
                        ->select(
                            DB::raw('SUM(saldo_awal_debit) AS saldo_awal_debit'),
                            DB::raw('SUM(saldo_awal_credit) AS saldo_awal_credit')
                        )
                        ->where('nomor_akun', 'like', substr($nomorAkun, 0, 4).'%')
                        ->where('created_by', auth()->user()->id)
                        ->first();

                    if($coaDetail->saldo_awal_debit != 0 || $coaDetail->saldo_awal_credit != 0){
                        if($coaDetail->saldo_normal == 'debit' && $coaDetail->saldo_awal_credit != 0){
                            $data[0][$child->nama_akun][$subChild->nomor_akun] = $coaDetail->saldo_awal_debit ?: $coaDetail->saldo_awal_credit;
                        }

                        if($coaDetail->saldo_normal == 'credit' && $coaDetail->saldo_awal_debit != 0){
                            $data[0][$child->nama_akun][$subChild->nomor_akun] = $coaDetail->saldo_awal_credit ?: $coaDetail->saldo_awal_debit;
                        }
                    }

                    if (!isset($data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun]) || $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] == 0) {
                        $saldo = 0;
                        $saldoAwal = 0;
                        if(in_array($coaDetail->saldo_normal, ['debit', 'd', 'db'])) {
                            $saldo = $coasTotals->saldo_awal_debit - $coasTotals->saldo_awal_credit;
                            $saldoAwal = $coasTotals->saldo_awal_debit;
                        } else {
                            $saldo = $coasTotals->saldo_awal_credit - $coasTotals->saldo_awal_debit;
                            $saldoAwal = $coasTotals->saldo_awal_credit;
                        }
                        $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldo;
                        $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] = $saldoAwal;
                    }
                    if(@$parent['golongan'] == 'Liabilitas' || @$parent['golongan'] == 'Ekuitas'){
                        $data[date('Y')]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] ?: $saldo;
                        $data[date('Y') - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nomor_akun] = $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nomor_akun] ?: $saldo;
                    }

                    if($subChild->nomor_akun == '311'){
                        $data[date('Y')]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = $labaRugi;
                        $data[date('Y') - 1]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = 0;
                    }
                }
            }

            foreach($data as $tahun => $rows) {
                foreach($rows as $rowKey => $row) {
                    if($rowKey == '2' || $rowKey == '3' || $rowKey == 2 || $rowKey == 3){
                        unset($data[$tahun][$rowKey]);
                    }
                }
            }
        }



        // da($data);
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

        // da($data);

        return $data;
    }

    public function neraca(Request $request){
        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
            $ttd1 = $request->input('text_input1');
            $ttd2 = $request->input('text_input2');
            $labaRugi = $this->labaRugi($request, 1);
            $data = $this->neracaFunc($end_date, $labaRugi);
            if($data[0]){
                unset($data[0]);
            }


            return view('report.neraca', [
                'data' => $data,
                'periode' => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
                'ttd1' => $ttd1,
                'ttd2' => $ttd2,
            ]);
            // $pdf = PDF::loadView('report.neraca', [
            //     'data' => $data,
            //     'periode' => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
            //     'ttd1' => $ttd1,
            //     'ttd2' => $ttd2,
            // ]);
            // return $pdf->download('neraca_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
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


    public function neracaSaldo(Request $request){

        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
            $jurnal = Jurnal::whereNull('is_deleted')
                            ->with(['details' => function($query) use ($start_date, $end_date) {
                                $query->whereBetween('tanggal_bukti', [$start_date, $end_date])
                                      ->orderBy('coa_akun', 'asc');
                            }])
                            ->whereYear('jurnal_tgl', date('Y'))
                            ->where('created_by', auth()->user()->id)
                            ->get();
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted')->orderBy('nomor_akun', 'asc')->get()->keyBy('nomor_akun');

            if($jurnal->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $data = [];
            foreach ($jurnal as $item) {
                foreach ($item->details as $detail) {
                    $nokun2 = substr($detail->coa_akun, 0, 5);
                    $nokun3 = substr($detail->coa_akun, 0, 3);
                    $nokun1 = substr($detail->coa_akun, 0, 1);

                    $akun3 = $coa->get($nokun3);
                    $akun2 = $coa->get($nokun2);
                    $akun1 = $coa->get($nokun1);
                    $xCoa = $coa->get($detail->coa_akun);

                    if($xCoa->saldo_normal == 'db' || $xCoa->saldo_normal == 'debit'){
                        $saldoAwal = $xCoa->saldo_awal_debit ?: $xCoa->saldo_awal_credit;
                    }else{
                        $saldoAwal = $xCoa->saldo_awal_credit ?: $xCoa->saldo_awal_debit;
                    }

                    if (!isset($data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['debit']) || !isset($data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['kredit'])) {
                        if($xCoa->saldo_normal == 'db' || $xCoa->saldo_normal == 'debit'){
                            $data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['debit'] = $saldoAwal;
                            $data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['kredit'] = 0;
                        }else{
                            $data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['kredit'] = $saldoAwal;
                            $data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['debit'] = 0;
                        }
                        $data[$akun3->nama_akun]['Total'] = 0;
                    }

                    if($akun1->saldo_normal == 'db' || $akun1->saldo_normal == 'debit'){
                        $data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['debit'] += $detail->debit - $detail->credit;
                    }else{
                        $data[$akun3->nama_akun][$akun2->nama_akun][$xCoa->nomor_akun.' - '.$xCoa->nama_akun]['kredit'] += $detail->credit - $detail->debit;
                    }

                    $getChild = $akun2->child;
                    if(!$getChild->isEmpty()){
                        foreach ($getChild as $child) {
                            if($child->nomor_akun !== $xCoa->nomor_akun){
                                if($child->saldo_normal == 'db' || $child->saldo_normal == 'debit'){
                                    if (!isset($data[$akun3->nama_akun][$akun2->nama_akun][$child->nomor_akun.' - '.$child->nama_akun]['debit'])) {
                                        $data[$akun3->nama_akun][$akun2->nama_akun][$child->nomor_akun.' - '.$child->nama_akun]['debit'] = $child->saldo_awal_debit;
                                        $data[$akun3->nama_akun][$akun2->nama_akun][$child->nomor_akun.' - '.$child->nama_akun]['kredit'] = 0;
                                    }
                                }else{
                                    if (!isset($data[$akun3->nama_akun][$akun2->nama_akun][$child->nomor_akun.' - '.$child->nama_akun]['kredit'])) {
                                        $data[$akun3->nama_akun][$akun2->nama_akun][$child->nomor_akun.' - '.$child->nama_akun]['kredit'] = $child->saldo_awal_credit;
                                        $data[$akun3->nama_akun][$akun2->nama_akun][$child->nomor_akun.' - '.$child->nama_akun]['debit'] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $data = $this->sortArrayByKey($data);
            $parentNo = 1;
            $newData = [];
            foreach ($data as $akun1 => &$subcategories) {
                $newAkun1 = $parentNo . '.' . $akun1;
                $childNo = 1;
                $parentTotalDebit = 0;
                $parentTotalKredit = 0;
                foreach ($subcategories as $akun2 => &$accounts) {
                    if ($akun2 !== 'Total') {
                        $newAkun2 = $parentNo . '.' . $childNo . '.' . $akun2;
                        $subTotalDebit = 0;
                        $subTotalKredit = 0;
                        foreach ($accounts as $xCoa => $balance) {
                            if ($xCoa !== 'Total') {
                                $subTotalDebit += $balance['debit'];
                                $subTotalKredit += $balance['kredit'];
                            }
                        }
                        $accounts['Total'] = [
                            'debit' => $subTotalDebit,
                            'kredit' => $subTotalKredit
                        ];
                        $newData[$newAkun1][$newAkun2] = $accounts;
                        $parentTotalDebit += $subTotalDebit;
                        $parentTotalKredit += $subTotalKredit;
                        $childNo++;
                    }
                }
                $newData[$newAkun1]['Total'] = [
                    'debit' => $parentTotalDebit,
                    'kredit' => $parentTotalKredit
                ];
                $parentNo++;
            }

            // da($newData);


            return view('report.neraca_saldo', [
                'data' => $newData,
                'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
                'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y')
            ]);

            // $pdf = PDF::loadView('report.neraca_saldo', [
            //     'data' => $newData,
            //     'tanggal_mulai' => Carbon::parse($start_date)->format('d/m/Y'),
            //     'tanggal_selesai' => Carbon::parse($end_date)->format('d/m/Y')
            // ]);
            // $pdf->setOption('isHtml5ParserEnabled', true);
            // $pdf->setOption('isRemoteEnabled', true);
            // return $pdf->download('neraca_saldo_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
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

    public function neracaPerbandingan(Request $request){
        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');

            $tahunSebelumnya = date('Y') - 1;
            $tahunSekarang = date('Y');
            $jurnalTahunSebelumnya = Jurnal::whereNull('is_deleted')
                                            ->with(['details' => function($query) use ($end_date) {
                                                $query->where(function($query) {
                                                    $query->where('coa_akun', 'like', '1%')
                                                        ->orWhere('coa_akun', 'like', '2%')
                                                        ->orWhere('coa_akun', 'like', '3%');
                                                });
                                            }])
                                           ->where('created_by', auth()->user()->id)
                                           ->whereYear('jurnal_tgl', $tahunSebelumnya)
                                           ->get();
            $jurnalTahunSekarang = Jurnal::whereNull('is_deleted')
                                        ->with(['details' => function($query) use ($end_date) {
                                            $query->where(function($query) {
                                                $query->where('coa_akun', 'like', '1%')
                                                    ->orWhere('coa_akun', 'like', '2%')
                                                    ->orWhere('coa_akun', 'like', '3%');
                                            });
                                        }])
                                         ->where('created_by', auth()->user()->id)
                                         ->whereYear('jurnal_tgl', $tahunSekarang)
                                         ->get();
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted');

            if($jurnalTahunSekarang->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $dataTahunSebelumnya = $this->neracaFunction($jurnalTahunSebelumnya, $coa);
            $dataTahunSekarang = $this->neracaFunction($jurnalTahunSekarang, $coa);

            $dataDahulu = [];

            $data = [
                $tahunSekarang => $dataTahunSekarang
            ];

            if($jurnalTahunSebelumnya->isEmpty()){
                $dataDahulu = array_map(function($section) {
                    return array_map(function($subSection) {
                        return array_map(function($item) {
                            return 0;
                        }, $subSection);
                    }, $section);
                }, $dataTahunSekarang);

                $data[$tahunSebelumnya] = $dataDahulu;
            }else{
                $data[$tahunSebelumnya] = $dataTahunSebelumnya;
            }

            $data = $this->totalNeraca($data);

            $pdf = PDF::loadView('report.neraca_perbandingan', [
                'data' => $data,
                'tahunSebelumnya' => $tahunSebelumnya,
                'tahunSekarang' => $tahunSekarang
            ]);
            return $pdf->download('neraca_perbandingan_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
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
        da($data);

        $data['Liabilitas dan Ekuitas'] = array_merge(
            $data['Liabilitas'] ?? [],
            $data['Ekuitas'] ?? []
        );

        unset($data['Liabilitas'], $data['Ekuitas'], $data['Beban Umum dan Admin'], $data['Pendapatan'], $data['Harga Pokok Penjualan']);

        $data['Liabilitas dan Ekuitas']['Ekuitas']['Saldo Tahun Berjalan'] = $labaBersihDulu;

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

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

    public function transaksi($id){
        $jurnal = Jurnal::with('details')->where(['id' => $id, 'created_by' => auth()->user()->id])->first();
        if($jurnal){
            foreach ($jurnal['details'] as $key => $detail) {
                $dt = Coa::where(['created_by' => auth()->user()->id])->first();
                $child = $dt->where('nomor_akun', $detail['coa_akun'])->first();
                if($child){
                    $jurnal['details'][$key]['nama_akun'] = $child['nama_akun'];
                }
                if (strpos($detail['coa_akun'], '-') !== false) {
                    $parent = substr($detail['coa_akun'], 0, strpos($detail['coa_akun'], '-'));
                    $coa = $dt->where('nomor_akun', $parent)->first();
                    if ($coa) {
                        $detail['parent'] = [
                            'nomor_akun' => $coa['nomor_akun'],
                            'nama_akun' => $coa['parent']['nama_akun']
                        ];
                    }
                }else{
                    $length = strlen($detail['coa_akun']);
                    $parent = substr($detail['coa_akun'], 0, $length-1);
                    $coa = $dt->where('nomor_akun', $parent)->first();
                    if ($coa) {
                        $detail['parent'] = [
                            'nomor_akun' => $coa['nomor_akun'],
                            'nama_akun' => $coa['parent']['nama_akun']
                        ];
                    }
                }
            }
        }
        $pdf = PDF::loadView('report.transaksi', ['jurnal' => $jurnal]);
        return $pdf->download('transaksi_jurnal_' . $id . '_' . Carbon::now()->format('YmdHis') . '.pdf');
    }

    public function downloadBukuBesar(Request $request){
        $tanggalMulai = trim($request->input('tanggal_mulai', Carbon::now()->format('Y-m-d')));
        $tanggalSelesai = trim($request->input('tanggal_selesai', Carbon::now()->format('Y-m-d')));
        $akun = $request->input('akun', '');

        $query = JurnalDetail::query()
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan')
            ->where('jurnal_headers.created_by', auth()->user()->id);

        if (!empty($akun)) {
            $query->where('jurnal_details.coa_akun', 'like', '%' . $akun . '%');
        }

        if ($request->has('tanggal_mulai') || $request->has('tanggal_selesai')) {
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

            $query->whereBetween('jurnal_headers.jurnal_tgl', [$startDate, $endDate]);
        }

        $en = $query->orderBy('jurnal_headers.jurnal_tgl')
            ->get()
            ->groupBy('coa_akun');

        $jurnalx = [];
        foreach ($en as $coaAkun => $transactions) {
            $saldoAwal = 0;
            $saldoKumulatif = $saldoAwal;

            foreach ($transactions as $transaction) {
                $saldoKumulatif += $transaction->debit - $transaction->credit;
                $transaction->saldo = $saldoKumulatif;
            }

            $jurnalx[$coaAkun] = $transactions;
        }

        $pdf = PDF::loadView('report.bukubesar_download', ['ledgers' => $jurnalx,'tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'akun' => $akun]);
        return $pdf->download('buku_besar_'.Carbon::now()->format('YmdHis').'.pdf');
    }

    public function bukuBesar(Request $request)
    {
        $tanggalMulai = trim($request->input('tanggal_mulai', Carbon::now()->format('Y-m-d')));
        $tanggalSelesai = trim($request->input('tanggal_selesai', Carbon::now()->format('Y-m-d')));
        $akun = $request->input('akun', '');

        $query = JurnalDetail::query()
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan')
            ->where('jurnal_headers.created_by', auth()->user()->id);

        if (!empty($akun)) {
            $query->where('jurnal_details.coa_akun', 'like', '%' . $akun . '%');
        }

        if ($request->has('tanggal_mulai') || $request->has('tanggal_selesai')) {
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

            $query->whereBetween('jurnal_headers.jurnal_tgl', [$startDate, $endDate]);
        }

        $en = $query->orderBy('jurnal_headers.jurnal_tgl')
            ->get()
            ->groupBy('coa_akun');

        $ledgers = [];
        foreach ($en as $coaAkun => $transactions) {
            $saldoAwal = 0;
            $saldoKumulatif = $saldoAwal;

            foreach ($transactions as $transaction) {
                $saldoKumulatif += $transaction->debit - $transaction->credit;
                $transaction->saldo = $saldoKumulatif;
            }

            $ledgers[$coaAkun] = $transactions;
        }

        return view('report.bukubesar', compact('ledgers', 'tanggalMulai', 'tanggalSelesai', 'akun'));
    }

    public function arusKas(Request $request){
        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
            $ttd1 = $request->input('text_input1');
            $ttd2 = $request->input('text_input2');
    
            $jurnal = Jurnal::whereNull('is_deleted')
                        ->with(['details' => function($query) {
                            $query->where('coa_akun', '>=', '1');
                        }])
                        ->where('created_by', auth()->user()->id)
                        ->whereBetween('jurnal_tgl', [$start_date, $end_date])
                        ->get();
    
            if($jurnal->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }
    
            $data = [
                'aktifitas_operasional' => ['Jumlah' => 0, 'Detail' => []],
                'aktifitas_pendanaan' => ['Jumlah' => 0, 'Detail' => []],
                'aktifitas_investasi' => ['Jumlah' => 0, 'Detail' => []],
            ];
    
            foreach ($jurnal as $entry) {
                foreach ($entry->details as $detail) {
                    $parent = Coa::where(['nomor_akun' => substr($detail->coa_akun, 0, 1), 'created_by' => auth()->user()->id])->first();
                    $child = Coa::where(['nomor_akun' => $detail->coa_akun, 'created_by' => auth()->user()->id])->first();
    
                    if ($parent->saldo_normal == 'db' || $parent->saldo_normal == 'debit') {
                        $nilai = $detail->debit - $detail->credit;
                    } else {
                        $nilai = $detail->credit - $detail->debit;
                    }

                    // da($parent);
    
                    $kategori = $parent->arus_kas;

                    da($kategori);
    
                    $data[$kategori]['Jumlah'] += $nilai;
                    $data[$kategori]['Detail'][$child->nama_akun] = ($data[$kategori]['Detail'][$child->nama_akun] ?? 0) + $nilai;
                }
            }

            // da($data);
    
            $pdf = PDF::loadView('report.aruskas', [
                'data' => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'ttd1' => $ttd1,
                'ttd2' => $ttd2,
            ]);
            return $pdf->download('aruskas_' . Carbon::now()->format('YmdHis') . '.pdf');
        }
    
        return view('report.views.template');
    }

    public function labaRugi(Request $request)
    {
        if($request->isMethod('post')){


            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
            $ttd1 = $request->input('text_input1');
            $ttd2 = $request->input('text_input2');

            $tahunSebelumnya = date('Y');
            $jurnal = Jurnal::whereNull('is_deleted')
                        ->with(['details' => function($query) {
                            $query->where('coa_akun', '>=', '4');
                        }])
                        ->where('created_by', auth()->user()->id)
                        ->whereBetween('jurnal_tgl', [$start_date, $end_date])
                        ->get();

            if($jurnal->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $kategori = [
                '4' => 'Pendapatan',
                '5' => 'Harga Pokok Penjualan',
                '6' => 'Beban Umum dan Admin'
            ];

            $data = [];
            foreach ($jurnal as $entry) {
                foreach ($entry->details as $detail) {
                    $kategoriAkun = substr($detail->coa_akun, 0, 1);
                    if (array_key_exists($kategoriAkun, $kategori)) {
                        $parent = Coa::where(['nomor_akun' => $kategoriAkun, 'created_by' => auth()->user()->id])->first();
                        $child = Coa::where(['nomor_akun' => $detail->coa_akun, 'created_by' => auth()->user()->id])->first();

                        if ($parent->saldo_normal == 'db' || $parent->saldo_normal == 'debit') {
                            $nilai = $detail->debit - $detail->credit;
                        } else {
                            $nilai = $detail->credit - $detail->debit;
                        }

                        $data[$parent->nama_akun]['Jumlah'] = ($data[$parent->nama_akun]['Jumlah'] ?? 0) + $nilai;
                        $data[$parent->nama_akun]['Detail'][$child->nama_akun] = ($data[$parent->nama_akun]['Detail'][$child->nama_akun] ?? 0) + $nilai;
                    }
                }
            }

            // da($data);

            uksort($data, function($a, $b) use ($kategori) {
                $order = array_flip(array_values($kategori));
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

            $pdf = PDF::loadView('report.labarugi', [
                'data' => $data,
                'tahunSebelumnya' => $tahunSebelumnya,
                'kategori' => $kategori,
                'labaRugiBersih' => $labaRugiBersih,
                'ttd1' => $ttd1,
                'ttd2' => $ttd2,
            ]);
            return $pdf->download('labarugi_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
    }

    public function perubahanEkuitas(Request $request) {
        if($request->isMethod('post')){
            $tahunSebelumnya = date('Y') - 1;
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
    
            $jurnalDulu = Jurnal::whereNull('is_deleted')
                            ->with('details')
                            ->whereYear('jurnal_tgl', $tahunSebelumnya)
                            ->where('created_by', auth()->user()->id)->get();
            $jurnalSekarang = Jurnal::whereNull('is_deleted')
                            ->with('details')
                            ->whereBetween('jurnal_tgl', [$start_date, $end_date])
                            ->where('created_by', auth()->user()->id)->get();
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted')->get();
    
            if($jurnalDulu->isEmpty() && $jurnalSekarang->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }
    
            $data = [];
    
            $totalsDulu = $this->calculateTotals($jurnalDulu, $coa);
            $totalsSekarang = $this->calculateTotals($jurnalSekarang, $coa);
    
            if($jurnalDulu->count() > 0){
                $labaKotorDulu = $totalsDulu['pendapatan'] - $totalsDulu['hpp'];
                $labaBersihDulu = $labaKotorDulu - $totalsDulu['beban'];
    
                $data[$tahunSebelumnya] = [
                    $totalsDulu['namaAkun'] => $totalsDulu['modal'],
                    'Saldo Laba Ditahan' => $labaBersihDulu,
                    'Saldo Tahun Berjalan' => $labaBersihDulu,
                ];
            } else {
                $totalsDulu = [
                    'pendapatan' => 0,
                    'hpp' => 0,
                    'beban' => 0,
                    'modal' => 0,
                    'namaAkun' => $totalsSekarang['namaAkun']
                ];
    
                $labaBersihDulu = 0;
    
                $data[$tahunSebelumnya] = [
                    $totalsDulu['namaAkun'] => $totalsDulu['modal'],
                    'Saldo Tahun Berjalan' => $labaBersihDulu,
                ];
            }
    
            if($jurnalSekarang->count() > 0){
                $labaKotorSekarang = $totalsSekarang['pendapatan'] - $totalsSekarang['hpp'];
                $labaBersihSekarang = $labaKotorSekarang - $totalsSekarang['beban'];
    
                $data[0] = [
                    $totalsSekarang['namaAkun'] => $totalsSekarang['modal'] - $totalsDulu['modal'],
                    'Saldo Tahun Berjalan' => $labaBersihSekarang - $labaBersihDulu,
                ];
    
                $data[$tahunSekarang] = [
                    $totalsSekarang['namaAkun'] => $totalsSekarang['modal'],
                    'Saldo Tahun Berjalan' => $labaBersihSekarang,
                ];
                if ($jurnalDulu->count() > 0) {
                    $data[$tahunSekarang]['Saldo Laba Ditahan'] = $labaBersihSekarang;
                }
    
    
            } else {
                $data['Penambahan / (Pengurangan)'] = [
                    $totalsDulu['namaAkun'] => $totalsDulu['modal'],
                    'Saldo Tahun Berjalan' => $labaBersihDulu,
                ];
    
                unset($data[$tahunSebelumnya]['Saldo Laba Ditahan']);
                $data[$tahunSekarang] = [
                    $totalsDulu['namaAkun'] => 0,
                    'Saldo Tahun Berjalan' => 0,
                ];
            }
    
            // da($data);
    
            $pdf = PDF::loadView('report.perubahanekuitas', [
                'data' => $data,
            ]);
            return $pdf->download('perubahanekuitas_' . Carbon::now()->format('YmdHis') . '.pdf');
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

    public function neraca(Request $request){
        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
            $ttd1 = $request->input('text_input1');
            $ttd2 = $request->input('text_input2');

            $jurnal = Jurnal::whereNull('is_deleted')
                            ->with('details')
                            ->where('created_by', auth()->user()->id)
                            ->whereBetween('jurnal_tgl', [$start_date, $end_date])
                            ->get();
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted');

            if($jurnal->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $data = $this->neracaFunction($jurnal, $coa);

            $totals = [];
            foreach ($data as $category => $subcategories) {
                $categoryTotal = 0;
                foreach ($subcategories as $subcategory => $items) {
                    $subcategoryTotal = array_sum($items);
                    $totals[$category][$subcategory] = $subcategoryTotal;
                    $categoryTotal += $subcategoryTotal;
                }
                $totals[$category]['Total'] = $categoryTotal;
            }

            $pdf = PDF::loadView('report.neraca', [
                'data' => $data,
                'totals' => $totals,
                'ttd1' => $ttd1,
                'ttd2' => $ttd2,
            ]);
            return $pdf->download('neraca_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
    }

    public function neracaSaldo(Request $request){

        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
            $jurnal = Jurnal::whereNull('is_deleted')
                            ->with('details')
                            ->where('created_by', auth()->user()->id)
                            ->whereBetween('jurnal_tgl', [$start_date, $end_date])
                            ->get();
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted');
    
            if($jurnal->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }
    
            $data = [];
            foreach ($jurnal as $item) {
                foreach ($item->details as $detail) {
                    $nokun2 = substr($detail->coa_akun, 0, 4);
                    $nokun3 = substr($detail->coa_akun, 0, 3);
                    $nokun1 = substr($detail->coa_akun, 0, 1);
                    $akun5 = Coa::firstWhere('nomor_akun', $detail->coa_akun);
                    $akun3 = Coa::firstWhere('nomor_akun', $nokun3);
                    $akun2 = Coa::firstWhere('nomor_akun', $nokun2);
                    $akun1 = Coa::firstWhere('nomor_akun', $nokun1)->first();
                    if (!isset($data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nama_akun])) {
                        $data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nama_akun] = 0;
                        $data[$akun3->nama_akun]['Total'] = 0;
                    }
    
                    if($akun1->saldo_normal == 'db' || $akun1->saldo_normal == 'debit'){
                        $data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nama_akun] += $detail->debit - $detail->credit;
                    }else{
                        $data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nama_akun] += $detail->credit - $detail->debit;
                    }
                }
            }
    
            foreach ($data as $akun3 => &$subcategories) {
                foreach ($subcategories as $akun2 => &$accounts) {
                    if ($akun2 !== 'Total') {
                        $subTotal = 0;
                        foreach ($accounts as $akun5 => $balance) {
                            if ($akun5 !== 'Total') {
                                $subTotal += $balance;
                            }
                        }
                        $accounts['Total'] = $subTotal;
                        $subcategories['Total'] = isset($subcategories['Total']) ? $subcategories['Total'] + $subTotal : $subTotal;
                    }
                }
                    }
    
    
    
            $pdf = PDF::loadView('report.neraca_saldo', [
                'data' => $data,
            ]);
            return $pdf->download('neraca_saldo_' . Carbon::now()->format('YmdHis') . '.pdf');
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
            // $tahunSekarang = date('Y');
            $jurnalTahunSebelumnya = Jurnal::whereNull('is_deleted')
                                           ->with('details')
                                           ->where('created_by', auth()->user()->id)
                                           ->whereYear('jurnal_tgl', $tahunSebelumnya)
                                           ->get();
            $jurnalTahunSekarang = Jurnal::whereNull('is_deleted')
                                         ->with('details')
                                         ->where('created_by', auth()->user()->id)
                                         ->whereBetween('jurnal_tgl', [$start_date, $end_date])
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
        foreach ($jurnal as $item) {
            foreach ($item->details as $detail) {
                $nomorAkun = substr($detail->coa_akun, 0, 1);
                if ($nomorAkun == '4') {
                    $totalPendapatanDulu += $detail->credit - $detail->debit;
                } elseif ($nomorAkun == '5') {
                    $totalHPPDulu += $detail->debit - $detail->credit;
                } elseif ($nomorAkun == '6') {
                    $totalBebanDulu += $detail->debit - $detail->credit;
                } elseif($nomorAkun == '3'){
                    $noKunLu = substr($detail->coa_akun, 0 , 4);
                    $namaAkunDulu = $coa->where('nomor_akun', $noKunLu)->first();
                    $totalModalDulu += $detail->debit + $detail->credit;
                }
            }
        }
        $labaKotorDulu = $totalPendapatanDulu - $totalHPPDulu;
        $labaBersihDulu = $labaKotorDulu - $totalBebanDulu;
        $data = [];
        foreach ($jurnal as $item) {
            foreach ($item->details as $detail) {
                $pAkun = substr($detail->coa_akun, 0, 1);
                $cAkun = substr($detail->coa_akun, 0, 2);
                $scAkun = substr($detail->coa_akun, 0, 3);
                $parent = Coa::where('nomor_akun', $pAkun)->first();
                $child = Coa::where('nomor_akun', $cAkun)->first();
                $subChild = Coa::where('nomor_akun', $scAkun)->first();

                if (!isset($data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun])) {
                    $data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun] = 0;
                }
                if($parent->saldo_normal == 'db' || $parent->saldo_normal == 'debit'){
                    $data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun] += $detail->debit - $detail->credit;
                }else{
                    $data[$parent->nama_akun][$child->nama_akun][$subChild->nama_akun] += $detail->credit - $detail->debit;
                }
            }
        }

        $data['Liabilitas dan Ekuitas'] = array_merge(
            $data['Liabilitas'] ?? [],
            $data['Ekuitas'] ?? []
        );

        unset($data['Liabilitas'], $data['Ekuitas'], $data['Beban Umum dan Admin'], $data['Pendapatan'], $data['Harga Pokok Penjualan']);

        $data['Liabilitas dan Ekuitas']['Ekuitas']['Saldo Tahun Berjalan'] = $labaBersihDulu;

        return $data;
    }
}

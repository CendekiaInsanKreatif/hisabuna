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
                $parent = substr($detail['coa_akun'], 0, 3);
                $coa = $dt->where('nomor_akun', $parent)->first();
                if ($coa) {
                    $detail['parent'] = [
                        'nomor_akun' => $coa['nomor_akun'],
                        'nama_akun' => $coa['nama_akun']
                    ];
                }
            }
        }
        $pdf = PDF::loadView('report.transaksi', ['jurnal' => $jurnal]);
        return $pdf->download('transaksi_jurnal_' . $id . '_' . Carbon::now()->format('YmdHis') . '.pdf');
    }

    public function downloadBukuBesar(Request $request){
        $tanggalMulai = Carbon::createFromFormat('d-m-Y', trim($request->input('tanggal_mulai', Carbon::parse(JurnalDetail::where('created_by', auth()->user()->id)->min('tanggal_bukti'))->format('d-m-Y'))))->format('Y-m-d');
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', trim($request->input('tanggal_selesai', Carbon::parse(JurnalDetail::where('created_by', auth()->user()->id)->max('tanggal_bukti'))->format('d-m-Y'))))->format('Y-m-d');
        $akun = $request->input('akun', '');
        

        $query = JurnalDetail::query()->with('coa')
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan', 'jurnal_details.tanggal_bukti')
            ->where('jurnal_headers.created_by', auth()->user()->id);

        if (!empty($akun)) {
            $akun = str_replace('-', '', $akun);
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

            $query->whereBetween('jurnal_details.tanggal_bukti', [$startDate, $endDate]);
        }

        $en = $query->orderBy('jurnal_details.tanggal_bukti')
            ->get()
            ->groupBy('coa_akun');

        $jurnalx = [];
        foreach ($en as $coaAkun => $transactions) {
            $coa = Coa::where('nomor_akun', $coaAkun)->where('created_by', auth()->user()->id)->first();
            if($coa->saldo_normal == 'db' || $coa->saldo_normal == 'debit'){
                $saldoAwal = $coa->saldo_awal_debit;
            }else{
                $saldoAwal = $coa->saldo_awal_credit;
            }
            $saldoKumulatif = $saldoAwal;

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
        }

        $pdf = PDF::loadView('report.bukubesar_download', ['ledgers' => $jurnalx,'tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'akun' => $akun]);
        return $pdf->download('buku_besar_'.Carbon::now()->format('YmdHis').'.pdf');
    }

    public function bukuBesar(Request $request)
    {
        $akun = $request->input('akun', '');
        $query = JurnalDetail::query()->with('coa')
        ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
        ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan')
        ->where('jurnal_headers.created_by', auth()->user()->id);
        
        $tanggalMulai = Carbon::createFromFormat('d-m-Y', trim($request->input('tanggal_mulai', Carbon::parse(JurnalDetail::where('created_by', auth()->user()->id)->min('tanggal_bukti'))->format('d-m-Y'))))->format('Y-m-d');
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', trim($request->input('tanggal_selesai', Carbon::parse(JurnalDetail::where('created_by', auth()->user()->id)->max('tanggal_bukti'))->format('d-m-Y'))))->format('Y-m-d');
        if (!empty($akun)) {
            $akun = str_replace('-', '', $akun);
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

            $query->whereBetween('jurnal_details.tanggal_bukti', [$startDate, $endDate]);
        }

        $en = $query->orderBy('jurnal_details.tanggal_bukti')
            ->get()
            ->groupBy('coa_akun');

        $ledgers = [];
        foreach ($en as $coaAkun => $transactions) {
            $coa = Coa::where('nomor_akun', $coaAkun)->where('created_by', auth()->user()->id)->first();
            if($coa->saldo_normal == 'db' || $coa->saldo_normal == 'debit'){
                $saldoAwal = $coa->saldo_awal_debit;
            }else{
                $saldoAwal = $coa->saldo_awal_credit;
            }
            $saldoKumulatif = $saldoAwal;

            foreach ($transactions as $transaction) {
                if($coa->saldo_normal == 'db' || $coa->saldo_normal == 'debit'){
                    $saldoKumulatif += $transaction->debit - $transaction->credit;
                }else{
                    $saldoKumulatif += $transaction->credit - $transaction->debit;
                }
                $transaction->saldo = $saldoKumulatif;
            }

            $ledgers[$coaAkun] = $transactions;
        }

        return view('report.bukubesar', compact('ledgers', 'tanggalMulai', 'tanggalSelesai', 'akun'));
    }

    public function arusKas(Request $request)
    {
        if ($request->isMethod('post')) {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');

            $jurnal = Jurnal::whereNull('is_deleted')
                        ->with(['details' => function($query) use ($start_date, $end_date) {
                            $query->where('coa_akun', '>=', '1')
                                  ->where('tanggal_bukti', '>=', $start_date)
                                  ->where('tanggal_bukti', '<=', $end_date);
                        }])
                        ->where('created_by', auth()->user()->id)
                        ->get();

            if ($jurnal->isEmpty()) {
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }

            $coas = Coa::where('created_by', auth()->user()->id)->get()->keyBy('nomor_akun');
            
            $data = [
                'aktifitas_operasional' => ['Jumlah' => 0, 'Detail' => []],
                'aktifitas_pendanaan' => ['Jumlah' => 0, 'Detail' => []],
                'aktifitas_investasi' => ['Jumlah' => 0, 'Detail' => []],
            ];

            foreach ($jurnal as $entry) {
                foreach ($entry->details as $detail) {
                    $parent = $coas->get(substr($detail->coa_akun, 0, 1));
                    $child = $coas->get($detail->coa_akun);
                    $aruskas = $coas->get(substr($detail->coa_akun, 0, 5));
                    if ($parent) {
                        if ($parent->saldo_normal == 'db' || $parent->saldo_normal == 'debit') {
                            $nilai = $detail->debit - $detail->credit;
                        } else {
                            $nilai = $detail->credit - $detail->debit;
                        }

                        $kategori = $aruskas->arus_kas;

                        if ($kategori) {
                            $data[$kategori]['Jumlah'] += $nilai;
                            $data[$kategori]['Detail'][$child->nama_akun] = ($data[$kategori]['Detail'][$child->nama_akun] ?? 0) + $nilai;
                        }
                    }
                }
            }

            foreach ($data as $kas => $value) {
                if($value['Jumlah'] == 0){
                    unset($data[$kas]);
                }
            }

            // da($data);

            $pdf = PDF::loadView('report.aruskas', [
                'data' => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);

            return $pdf->download('aruskas_' . Carbon::now()->format('YmdHis') . '.pdf');
        }

        return view('report.views.template');
    }


    public function labaRugi(Request $request, $n = 0)
    {
        if($request->isMethod('post')){
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d H:i:s');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d H:i:s');
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
            // da($data);

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
            if($n == 1){
                return $labaRugiBersih;
            }

            // da($data);
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
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted')->get();
    
            if($jurnalDulu->isEmpty() && $jurnalSekarang->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }
    
            $data = [];
    
            $totalsDulu = $this->calculateTotals($jurnalDulu, $coa);
            $totalsSekarang = $this->calculateTotals($jurnalSekarang, $coa);
            $totalsDulu['tahun'] = $tahunSebelumnya;
            $totalsSekarang['tahun'] = $tahunSekarang;
    
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
            // da($data[$tahunSekarang][$totalsSekarang['namaAkun']]);
            // da($data);
            $pdf = PDF::loadView('report.perubahanekuitas', [
                'tahunSebelumnya' => $tahunSebelumnya,
                'tahunSekarang' => $tahunSekarang,
                'totalsDulu' => $totalsDulu,
                'totalsSekarang' => $totalsSekarang,
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

    private function neracaFunc($tanggal, $labaRugi){
        $jurnal = JurnalDetail::where('created_by', auth()->user()->id)
            ->where(function($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })->where('tanggal_bukti', '<=', $tanggal)->get();

        $coa = Coa::whereNull('is_deleted')
            ->where(function($query) {
                $query->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('created_by', auth()->user()->id)
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
                    ->select(
                        DB::raw('SUM(debit) AS debit'),
                        DB::raw('SUM(credit) AS credit')
                    )
                    ->where('created_by', auth()->user()->id)
                    ->where('coa_akun', 'like', substr($nomorAkun, 0, 4).'%')
                    ->first();

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
                    $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] = $saldo;
                    $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] = $saldoAwal;
                }
            }

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
                    
                    if (!isset($data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun]) || $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] == 0) {
                        $saldo = 0;
                        $saldoAwal = 0;
                        if(in_array($coaDetail->saldo_normal, ['debit', 'd', 'db'])) {
                            $saldo = $coasTotals->saldo_awal_debit - $coasTotals->saldo_awal_credit;
                            $saldoAwal = $coasTotals->saldo_awal_debit;
                        } else {
                            $saldo = $coasTotals->saldo_awal_credit - $coasTotals->saldo_awal_debit;
                            $saldoAwal = $coasTotals->saldo_awal_credit;
                        }
                        $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] = $saldo;
                        $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] = $saldoAwal;
                    }
                    if(@$parent['golongan'] == 'Liabilitas' || @$parent['golongan'] == 'Ekuitas'){
                        $data[date('Y')]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nama_akun] = $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] ?: $saldo;
                        $data[date('Y') - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nama_akun] = $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] ?: $saldo;
                    }

                    if($subChild->nomor_akun == '311'){
                        $data[date('Y')]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = $labaRugi;
                        $data[date('Y') - 1]['Liabilitas dan Ekuitas'][$child->nama_akun]['Saldo Tahun Berjalan'] = 0;
                    }
                }
            }

            // da($data);


            foreach($data as $tahun => $rows) {
                foreach($rows as $rowKey => $row) {
                    if($rowKey == '2' || $rowKey == '3' || $rowKey == 2 || $rowKey == 3){
                        unset($data[$tahun][$rowKey]);
                    }
                }
            }

            // foreach($data as $tahun => $rows) {
            //     foreach($rows as $parentKey => $parentRows) {
            //         $parentTotal = 0;
            //         foreach($parentRows as $childKey => $childRows) {
            //             $childTotal = 0;
            //             foreach($childRows as $subChildKey => $saldo) {
            //                 if (is_numeric($saldo)) {
            //                     $childTotal += $saldo;
            //                 }
            //             }
            //             $data[$tahun][$parentKey]['Jumlah '.$childKey] = $childTotal;
            //             $parentTotal += $childTotal;
            //         }
            //         $data[$tahun]['Jumlah '.$parentKey] = $parentTotal;
            //     }
            // }
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

            $pdf = PDF::loadView('report.neraca', [
                'data' => $data,
                'periode' => Carbon::parse($request->input('end_date'))->translatedFormat('j F Y'),
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
                            ->with(['details' => function($query) use ($start_date, $end_date) {
                                $query->where(function($query) {
                                    $query->where('coa_akun', 'like', '1%')
                                          ->orWhere('coa_akun', 'like', '2%')
                                          ->orWhere('coa_akun', 'like', '3%');
                                })->whereBetween('tanggal_bukti', [$start_date, $end_date]);
                            }])
                            ->whereYear('jurnal_tgl', date('Y'))
                            ->where('created_by', auth()->user()->id)
                            ->get();
            $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted');
    
            if($jurnal->isEmpty()){
                return redirect()->back()->with('message', 'Data tidak ditemukan')->with('color', 'red');
            }
    
            $data = [];
            foreach ($jurnal as $item) {
                foreach ($item->details as $detail) {
                    $nokun2 = substr($detail->coa_akun, 0, 5);
                    $nokun3 = substr($detail->coa_akun, 0, 3);
                    $nokun1 = substr($detail->coa_akun, 0, 1);

                    $akun5 = Coa::firstWhere('nomor_akun', $detail->coa_akun);
                    $akun3 = Coa::firstWhere('nomor_akun', $nokun3);
                    $akun2 = Coa::firstWhere('nomor_akun', $nokun2);
                    $akun1 = Coa::firstWhere('nomor_akun', $nokun1)->first();

                    $xCoa = Coa::where('nomor_akun', $detail->coa_akun)->where('created_by', auth()->user()->id)->whereNull('is_deleted')->first();

                    if($xCoa->saldo_normal == 'db' || $xCoa->saldo_normal == 'debit'){
                        $saldoAwal = $xCoa->saldo_awal_debit;
                    }else{
                        $saldoAwal = $xCoa->saldo_awal_credit;
                    }
                    
                    if (!isset($data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nomor_akun.' - '.$akun5->nama_akun])) {
                        $data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nomor_akun.' - '.$akun5->nama_akun] = $saldoAwal;
                        $data[$akun3->nama_akun]['Total'] = 0;
                    }
    
                    if($akun1->saldo_normal == 'db' || $akun1->saldo_normal == 'debit'){
                        $data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nomor_akun.' - '.$akun5->nama_akun] += $detail->debit - $detail->credit;
                    }else{
                        $data[$akun3->nama_akun][$akun2->nama_akun][$akun5->nomor_akun.' - '.$akun5->nama_akun] += $detail->credit - $detail->debit;
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
    
                    
            // dd($data);
    
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

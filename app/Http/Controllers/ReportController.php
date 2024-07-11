<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Saldo;
use App\Models\User;
use PDF;

class ReportController extends Controller
{
    public function daftarJurnal()
    {
        $jurnal = Jurnal::with('details')->where('created_by', auth()->user()->id)->get();
        $view = view('report.daftarjurnal', ['jurnal' => $jurnal])->render();
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

    public function downloadBukuBesar(){
        $query = JurnalDetail::query()
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan')
            ->where('jurnal_headers.created_by', auth()->user()->id);

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

        $pdf = PDF::loadView('report.bukubesar_download', ['ledgers' => $jurnalx]);
        return $pdf->download('buku_besar_'.Carbon::now()->format('YmdHis').'.pdf');
    }

    public function bukuBesar(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfMonth()->toDateString());
        $akun = $request->input('akun', '');

        $query = JurnalDetail::query()
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan')
            ->where('jurnal_headers.created_by', auth()->user()->id);


            if (!empty($akun)) {
                $query->where('jurnal_details.coa_akun', 'like', '%' . $akun . '%');
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

    public function labaRugi(Request $request)
    {
        $tahunSebelumnya = date('Y') - 1;

        $jurnal = Jurnal::whereNull('is_deleted')
                        ->with(['details' => function($query) {
                            $query->where('coa_akun', '>=', '4');
                        }])
                        ->where('created_by', auth()->user()->id)
                        ->whereYear('jurnal_tgl', $tahunSebelumnya)
                        ->get();

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
            'labaRugiBersih' => $labaRugiBersih
        ]);
        return $pdf->download('labarugi_' . Carbon::now()->format('YmdHis') . '.pdf');
    }
}

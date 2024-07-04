<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Saldo;
use App\Models\User;

class ReportController extends Controller
{
    public function bukuBesar(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfMonth()->toDateString());
        $akun = $request->input('akun', '');

        $query = JurnalDetail::query()
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->select('jurnal_headers.jurnal_tgl', 'jurnal_details.coa_akun', 'jurnal_details.debit', 'jurnal_details.credit', 'jurnal_details.keterangan')
            ->where('jurnal_headers.created_by', auth()->user()->id);
            // ->whereBetween('jurnal_headers.jurnal_tgl', [$tanggalMulai, $tanggalSelesai]);


            if (!empty($akun)) {
                $query->where('jurnal_details.coa_akun', 'like', '%' . $akun . '%');
            }

            $entries = $query->orderBy('jurnal_headers.jurnal_tgl')
            ->get()
            ->groupBy('coa_akun');

            // da($entries);
        $ledgers = [];
        foreach ($entries as $coaAkun => $transactions) {
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

    public function neraca(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfYear()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfYear()->toDateString());

        $saldoAkhir = Saldo::whereBetween('periode_saldo', [$tanggalMulai, $tanggalSelesai])->where('created_by', auth()->user()->id)->get();
        $jurnalDetails = JurnalDetail::with('jurnal')
                                     ->whereHas('jurnal', function($query) use ($tanggalMulai, $tanggalSelesai) {
                                         $query->whereBetween('jurnal_tgl', [$tanggalMulai, $tanggalSelesai]);
                                     })
                                     ->where('created_by', auth()->user()->id)
                                     ->get(['coa_akun', 'debit', 'credit']);

        $saldoAkhirPerAkun = $saldoAkhir->pluck('saldo_awal_debit', 'coa_akun')
                                        ->mapWithKeys(function ($item, $key) use ($saldoAkhir) {
                                            return [$key => $item - $saldoAkhir->where('coa_akun', $key)->first()->saldo_awal_kredit];
                                        });

        $jurnalDetails->each(function ($jurnal) use (&$saldoAkhirPerAkun) {
            $saldoAkhirPerAkun[$jurnal->coa_akun] = ($saldoAkhirPerAkun[$jurnal->coa_akun] ?? 0) + $jurnal->debit - $jurnal->credit;
        });

        // da($saldoAkhirPerAkun);

        $neraca = $saldoAkhirPerAkun->map(function ($saldo, $akun) {
            $coa = Coa::where('nomor_akun', $akun)->where('created_by', auth()->user()->id)->with(['parent.parent.parent.parent'])->first();
            // da($coa);
            return [
                'parent' => optional($coa->parent->parent->parent)->parent->nama_akun,
                'subparent' => optional($coa->parent->parent)->parent->nama_akun,
                'coa' => [
                    'nomor_akun' => optional($coa->parent)->parent->nomor_akun ?? 'Tidak Diketahui',
                    'nama_akun' => optional($coa->parent)->parent->nama_akun ?? 'Tidak Diketahui',
                    'saldo' => $saldo
                ]
            ];
        })->values()->all();

        $result = [];
        foreach ($neraca as $item) {
            $key = $item['parent'] . '_' . $item['subparent'];

            if (!isset($result[$key])) {
                $result[$key] = $item;
                $result[$key]['coa'] = [$item['coa']];
            } else {
                $result[$key]['coa'][] = $item['coa'];
            }
        }

        $result = array_values($result);

        // da($result);



        return view('report.neraca', compact('result', 'tanggalMulai', 'tanggalSelesai'));
    }
}

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

    public function perubahanEkuitas() {
        $tahunSebelumnya = date('Y') - 1;
        $tahunSekarang = date('Y');

        $jurnalDulu = Jurnal::whereNull('is_deleted')
                        ->with('details')
                        ->whereYear('jurnal_tgl', $tahunSebelumnya)
                        ->where('created_by', auth()->user()->id)->get();
        $jurnalSekarang = Jurnal::whereNull('is_deleted')
                        ->with('details')
                        ->whereYear('jurnal_tgl', $tahunSekarang)
                        ->where('created_by', auth()->user()->id)->get();
        $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted')->get();

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
        $ttd1 = $request->input('ttd1', date('Y'));
        $ttd2 = $request->input('ttd2', date('m'));

        $tanggal = date('Y');
        $jurnal = Jurnal::whereNull('is_deleted')
                        ->with('details')
                        ->where('created_by', auth()->user()->id)
                        ->whereYear('jurnal_tgl', $tanggal)
                        ->get();
        $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted');

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
        ]);
        return $pdf->download('neraca_' . Carbon::now()->format('YmdHis') . '.pdf');
    }

    public function neracaPerbandingan(Request $request){
        $ttd1 = $request->input('ttd1', date('Y'));
        $ttd2 = $request->input('ttd2', date('m'));

        $tahunSebelumnya = date('Y') - 1;
        $tahunSekarang = date('Y');
        $jurnalTahunSebelumnya = Jurnal::whereNull('is_deleted')
                                       ->with('details')
                                       ->where('created_by', auth()->user()->id)
                                       ->whereYear('jurnal_tgl', $tahunSebelumnya)
                                       ->get();
        $jurnalTahunSekarang = Jurnal::whereNull('is_deleted')
                                     ->with('details')
                                     ->where('created_by', auth()->user()->id)
                                     ->whereYear('jurnal_tgl', $tahunSekarang)
                                     ->get();
        $coa = Coa::where('created_by', auth()->user()->id)->whereNull('is_deleted');

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

        // da($data);
        $data['Liabilitas dan Ekuitas']['Ekuitas']['Saldo Tahun Berjalan'] = $labaBersihDulu;

        return $data;
    }
}

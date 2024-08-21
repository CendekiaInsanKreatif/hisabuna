<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Report_Controller;
use App\Http\Controllers\ArusKasController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;



use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Imports\JurnalDetailImport;
use App\Imports\MultipleJurnal;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('jurnal.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //  Main Route
    Route::resource('coas', CoaController::class);
    Route::resource('jurnal', JurnalController::class);

    // Arus Kas
    Route::get('arus-kas', function(){
        return view('arus-kas.index');
    })->name('arus-kas.index');

    Route::match(['put', 'patch'], 'arus-kas/{id}', function(Request $request, $id) {
        Coa::where('id', $id)->update([
            'arus_kas' => $request->input('value'),
        ]);

        return response()->json(['message' => 'Berhasil Update Aruskas'], 200);
    })->name('arus-kas.update');
    
    Route::get('saldo-awal', function(){
        return view('saldo-awal.index');
    })->name('saldo-awal.index');

    Route::match(['put', 'patch'], 'saldo-awal', function(Request $request) {
        $ids = $request->input('id');
        $debitValues = $request->input('saldo_awal_debit');
        $creditValues = $request->input('saldo_awal_credit');
        $sumDebit = array_sum(array_map(function($value) {
            return (int) str_replace('.', '', $value);
        }, $debitValues));
        $sumCredit = array_sum(array_map(function($value) {
            return (int) str_replace('.', '', $value);
        }, $creditValues));
    
        DB::beginTransaction();
    
        try {
            if ($sumDebit !== $sumCredit) {
                DB::rollBack();
                return redirect()->route('saldo-awal.index')
                    ->with('message', 'Gagal Update Saldo Awal: Debit dan Kredit tidak sama')
                                       ->with('color', 'red');
            }

            foreach ($ids as $index => $id) {
                $debit = (int) str_replace('.', '', $debitValues[$index]);
                $credit = (int) str_replace('.', '', $creditValues[$index]);
    
                Coa::where('id', $id)->update([
                    'saldo_awal_debit' => $debit,
                    'saldo_awal_credit' => $credit,
                ]);
            }
    
            DB::commit();
            return redirect()->route('saldo-awal.index')
                ->with('message', 'Berhasil Update Saldo Awal')
                ->with('color', 'green');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('saldo-awal.index')
                ->with('message', 'Gagal Update Saldo Awal: Terjadi kesalahan')
                ->with('color', 'red');
        }
    })->name('saldo-awal.update');


    //  Custom Route
    Route::post('coas/import', [CoaController::class, 'import'])->name('coas.import');
    Route::post('coas/export', [CoaController::class, 'export'])->name('coas.export');
    Route::post('jurnal/import', [JurnalController::class, 'import'])->name('jurnal.import');
    Route::post('/jurnal/import-html', [JurnalController::class, 'importHtml'])->name('jurnal.import.html');
    Route::post('jurnal/sample/export', [JurnalController::class, 'sampleExport'])->name('jurnal.sample.export');
    Route::get('jurnal/lampiran', function() {
        return "fafa";
    })->name('jurnal.lampiran');

    // Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('report')->group(function () {
        // Report Controller
        Route::get('daftarjurnal', [ReportController::class, 'daftarJurnal'])->name('report.daftarjurnal');
        Route::get('transaksi/{id}', [ReportController::class, 'transaksi'])->name('report.transaksi');
        Route::get('bukubesar', [ReportController::class, 'bukuBesar'])->name('report.bukubesar');
        Route::get('bukubesar/download', [ReportController::class, 'downloadBukuBesar'])->name('report.bukubesar.download');

        // Report View
        Route::get('labarugi', [ReportController::class, 'labaRugi'])->name('report.views.labarugi');
        Route::get('labarugidownloadpdf', [ReportController::class, 'labaRugiDownloadPDF'])->name('labarugidownloadpdf');
        Route::get('perubahanekuitas', [ReportController::class, 'perubahanEkuitas'])->name('report.views.perubahanekuitas');
        Route::get('neraca', [ReportController::class, 'neraca'])->name('report.views.neraca');
        Route::get('neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('report.views.neracasaldo');
        Route::get('neraca-perbandingan', [ReportController::class, 'neracaPerbandingan'])->name('report.views.neracaperbandingan');
        Route::get('aruskas', [ReportController::class, 'arusKas'])->name('report.views.aruskas');

        // Report PDF
        Route::post('labarugi', [ReportController::class, 'labaRugi'])->name('report.labarugi');
        Route::post('perubahanekuitas', [ReportController::class, 'perubahanEkuitas'])->name('report.perubahanekuitas');
        Route::post('neraca', [ReportController::class, 'neraca'])->name('report.neraca');
        Route::post('neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('report.neracasaldo');
        Route::post('neraca-perbandingan', [ReportController::class, 'neracaPerbandingan'])->name('report.neracaperbandingan');
        Route::post('aruskas', [ReportController::class, 'arusKas'])->name('report.aruskas');
        
        Route::get('print-coa', [CoaController::class, 'printCoa'])->name('report.print-coa');
    });

    // API Rouye
    Route::prefix('api')->group(function () {
        Route::get('labarugi', [Report_Controller::class, 'labaRugi'])->name('api.labarugi');
        Route::get('perubahanekuitas', [Report_Controller::class, 'perubahanEkuitas'])->name('api.perubahanekuitas');
        Route::get('neraca', [Report_Controller::class, 'neraca'])->name('api.neraca');
        Route::get('neraca-saldo', [Report_Controller::class, 'neracaSaldo'])->name('api.neracasaldo');
        Route::get('neraca-perbandingan', [Report_Controller::class, 'neracaPerbandingan'])->name('api.neracaperbandingan');
        Route::get('aruskas', [Report_Controller::class, 'arusKas'])->name('api.aruskas');

        Route::get('coas', function () {
            $coa = Coa::whereNull('is_deleted')
                    ->where('created_by', auth()->user()->id)
                    ->orderBy('nomor_akun')
                      ->orderBy('level')
                      ->get();
            return response()->json($coa);
        });
        
        Route::get('arus-kas', function () {
            $arusKas = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->where('level', '=', 4)->orderBy('nomor_akun', 'asc')->get();
            return response()->json($arusKas);
        });

        Route::get('saldo-awal', function () {
            $saldoAwal = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->where('level', '=', 5)->orderBy('nomor_akun', 'asc')->get();
            return response()->json($saldoAwal);
        });

        Route::post('saldo-awal', function (Request $request) {
            $input = $request->all();

            foreach ($input as $key => $value) {
                Coa::where('id', $key)->update([
                    'saldo_awal_debit' => $value['saldo_awal_debit'] ?? 0,
                    'saldo_awal_credit' => $value['saldo_awal_credit'] ?? 0,
                ]);
            }

            return view('saldo-awal.index')->with('message', 'Berhasil Update Saldo Awal')->with('color', 'green');
        });

        Route::get('/uploadsample', function(Request $request){
            DB::beginTransaction();
            try {
                $dataJurnal = Jurnal::create([
                    'jenis' => 'JV',
                    'no_urut_transaksi' => 1,
                    'no_transaksi' => '1',
                    'jurnal_tgl' => now(),
                    'subtotal' => 5360178622,
                    'keterangan' => 'Jurnal Umum Import Sample',
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ]);

                if($dataJurnal){
                    $path = storage_path('app/dummy/jurnal_sample.xlsx');
                    $import = new JurnalDetailImport;
                    $data = Excel::toCollection($import, $path);
                    $count = count($data[0]);

                    $rCount = 0;
                    foreach ($data[0] as $row) {
                        $no_akun = str_replace('-', '', $row['akun_coa']);
                        $coa = Coa::where('nomor_akun', $no_akun)->where('created_by', Auth::user()->id)->first();
                        
                        if ($coa) {
                            $debit = (int) ($row['debit'] ?? 0);
                            $credit = (int) ($row['kredit'] ?? 0);
                    
                            $dtal = JurnalDetail::create([
                                'jurnal_id' => $dataJurnal->id,
                                'coa_akun' => $coa->nomor_akun,
                                'debit' => $debit,
                                'credit' => $credit,
                                'keterangan' => $row['keterangan'],
                                'tanggal_bukti' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_bukti']))->format('Y-m-d H:i:s'),
                                'created_by' => Auth::user()->id,
                                'created_at' => now()
                            ]);
                    
                            if ($dtal) {
                                $saldo_normal = strtolower($coa->saldo_normal);
                    
                                if ($saldo_normal == 'debit' || $saldo_normal == 'd' || $saldo_normal == 'db') {
                                    if ($debit < 0) {
                                        $new_balance = $coa->saldo_awal_debit - abs($debit) - $credit;
                                    } else {
                                        $new_balance = $coa->saldo_awal_debit + $debit - $credit;
                                    }
                                    $coa->increment('saldo_berjalan_debit', $new_balance);
                                } else {
                                    if ($credit < 0) {
                                        $new_balance = $coa->saldo_awal_credit - abs($credit) - $debit;
                                    } else {
                                        $new_balance = $coa->saldo_awal_credit + $credit - $debit;
                                    }
                                    $coa->increment('saldo_berjalan_credit', $new_balance);
                                }
                            }
                        }else{
                            if($rCount < $count){
                            }else{
                                DB::rollBack();
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Akun ' . $no_akun . ' tidak ditemukan'
                                ], 404);
                            }
                        }

                        $rCount++;
                    }                    
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengupload sample'
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload sample: ' . $e->getMessage()
                ], 500);
            }
        });

        // Route::get('multiple-jurnal', function(){
        //     try{
        //         $path = storage_path('app/dummy/multiple_jurnal.xlsx');
        //         $import = new MultipleJurnal;
        //         $data = Excel::toArray($import, $path);


        //         $gabunganJurnal = [];
        //         foreach ($data[1] as $header) {
        //             $transno = $header['transno'];
        //             $details = [];

        //             foreach ($data[2] as $detail) {
        //                 if ($detail['id'] == $transno) {
        //                     $details[] = $detail;
        //                 }
        //             }

        //             $header['details'] = $details;
        //             $gabunganJurnal[] = $header;
        //         }

        //         foreach ($gabunganJurnal as $jurnal) {
        //             da($jurnal);
        //         }




        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Berhasil Upload Multiple Jurnal'
        //         ], 200);
        //     } catch (\Exception $e) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Gagal Upload Multiple Jurnal: ' . $e->getMessage()
        //         ], 500);
        //     }
        // });

        Route::get('coa-update', function () {
            $coa = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get();
            return response()->json($coa);
        });

        Route::get('jurnal', function () {
            $jurnal = Jurnal::with(['details.coa'])
                ->whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json($jurnal);
        });

        Route::get('files', function () {
            Storage::disk('ftp')->put('test.txt', 'Hello World');
        });
    });



    Route::prefix('view')->group(function () {
        Route::get('daftarjurnal', function () {
            $jurnal = Jurnal::with('details')->where('created_by', auth()->user()->id)->get();

            return view('report.daftarjurnal', compact('jurnal'));
        });

        Route::get('transaksi', function () {
            return view('report.transaksi');
        });
    });

    Route::get('test', function(){
        $coa = array(
            1 => array(
                11 => array(
                    111 => 3275700553,
                    112 => 1563906500
                ),
                12 => array(
                    121 => 175895462
                )
            ),
            2 => array(
                21 => array(
                    211 => 97408680,
                    219 => 1563906500
                )
            ),
            3 => array(
                31 => array(
                    311 => 3443796333
                )
            )
        );

        $data = array(
            1 => array(
                11 => array(
                    115 => -19,
                    116 => 20482279,
                    111 => 3208721950
                )
            ),
            2 => array(
                21 => array(
                    211 => 158895109
                )
            )
        );

        $result = [];
        $total = 0;
        foreach ($coa as $key1 => $value1) {
            $result[2024][$key1] = [];
            foreach ($value1 as $key2 => $value2) {
                $sum = 0;
                $result[2024][$key1][$key2] = [];
                foreach ($value2 as $key3 => $value3) {
                    if (isset($data[$key1][$key2][$key3])) {
                        $result[2024][$key1][$key2][$key3] = $data[$key1][$key2][$key3];
                    } else {
                        $result[2024][$key1][$key2][$key3] = $value3;
                    }
                    $sum += $result[2024][$key1][$key2][$key3];
                }
                if (isset($data[$key1][$key2])) {
                    foreach ($data[$key1][$key2] as $key3 => $value3) {
                        if (!isset($result[2024][$key1][$key2][$key3])) {
                            $result[2024][$key1][$key2][$key3] = $value3;
                            $sum += $value3;
                        }
                    }
                }
                $result[2024][$key1]["Jumlah $key2"] = $sum;
                $total += $sum;
            }
            $result[2024]["Jumlah $key1"] = $total;
        }
        $total = 0;
        foreach ($coa as $key1 => $value1) {
            $result[2023][$key1] = [];
            foreach ($value1 as $key2 => $value2) {
                $sum = 0;
                $result[2023][$key1][$key2] = [];
                foreach ($value2 as $key3 => $value3) {
                    $result[2023][$key1][$key2][$key3] = $value3;
                    $sum += $value3;
                }
                if (isset($data[$key1][$key2])) {
                    foreach ($data[$key1][$key2] as $key3 => $value3) {
                        if (!isset($result[2023][$key1][$key2][$key3])) {
                            $result[2023][$key1][$key2][$key3] = 0;
                        }
                    }
                }
                $result[2023][$key1]["Jumlah $key2"] = $sum;
                $total += $sum;
            }
            $result[2023]["Jumlah $key1"] = $total;
        }
        return $result;
    });

    Route::get('hitung-neraca', function(){
        $jurnal = JurnalDetail::where('created_by', auth()->user()->id)
            ->where(function($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })->get();

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
                            $saldo = $coasTotals->saldo_awal_debit;
                            $saldoAwal = $coasTotals->saldo_awal_debit;
                        } else {
                            $saldo = $coasTotals->saldo_awal_credit;
                            $saldoAwal = $coasTotals->saldo_awal_credit;
                        }
                        $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] = $saldo;
                        $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] = $saldoAwal;
                    }
                    if(@$parent['golongan'] == 'Liabilitas' || @$parent['golongan'] == 'Ekuitas'){
                        $data[date('Y')]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nama_akun] = $data[date('Y')][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] ?: $saldo;
                        $data[date('Y') - 1]['Liabilitas dan Ekuitas'][$child->nama_akun][$subChild->nama_akun] = $data[date('Y') - 1][$parent->nomor_akun][$child->nama_akun][$subChild->nama_akun] ?: $saldo;
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
        
        da($data);
        return $data;
    });

});

require __DIR__.'/auth.php';

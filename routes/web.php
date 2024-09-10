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
use Illuminate\Validation\Rules;
use Alert;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Imports\JurnalDetailImport;
use App\Imports\MultipleJurnal;
use Illuminate\Http\Request;
use App\Models\User;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if(auth()->user()->is_active == 0){
        Auth::guard('web')->logout();
        return redirect()->route('login')->with('message', 'Akun anda tidak aktif')->with('color', 'red');
    }

    return view('jurnal.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //  Main Route
    Route::resource('coas', CoaController::class);
    Route::resource('jurnal', JurnalController::class);
    // Route::get('cekTrial')

    // Users
    Route::get('users', function(){
        return view('users.index');
    })->name('users.index');

    Route::post('users/store', function(Request $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'no_telp' => $request->no_telp,
            'periode' => $request->periode,
            'profile' => $request->profile,
            'password' => Hash::make($request->password),
            'roles' => 'user',
            'is_active' => 1,
            'company_name' => $request->company_name,
        ]);

        if ($request->hasFile('image')) {
            $lampiranFile = $request->file('image');
            $filePath = 'profiles/' . $user->company_name;
            $fileName = $user->id . '.' . $lampiranFile->getClientOriginalExtension();
            $tempPath = $lampiranFile->getPathName();
        
            try {
                $imagick = new Imagick($tempPath);
                $imagick->setImageCompressionQuality(30);
                $compressedImagePath = storage_path('app/public/' . $filePath . '/' . $fileName);
                $directoryPath = storage_path('app/public/' . $filePath);
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }
                $imagick->writeImage($compressedImagePath);
                $imagick->clear();
                $imagick->destroy();
                $user->company_logo = $filePath . '/' . $fileName;
                $user->save();
            } catch (ImagickException $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        Alert::success('Sukses!', 'Berhasil Tambah User');
        return redirect()->route('users.index');
    })->name('users.store');

    Route::get('users/{id}', function($id){
        $users = User::where('id', $id)->get();
        return view('users.show', compact('users'));
    })->name('users.show');

    Route::put('users/{id}', function(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update($request->except(['password', 'password_confirmation']));

        return redirect()->route('users.index')->with('message', 'Berhasil Update Pengguna')->with('color', 'green');
    })->name('users.update');

    Route::delete('users/{id}', function($id) {
        $user = User::findOrFail($id);
        $user->is_active = 0; // Mengubah status is_active menjadi 0
        $user->save(); // Menyimpan perubahan ke database

        return redirect()->route('users.index')->with('message', 'Berhasil Nonaktifkan Pengguna')->with('color', 'green');
    })->name('users.destroy');

    Route::match(['put', 'patch'], 'users', function(Request $request) {
        $user = User::findOrFail($id);
        $user->update([
            'status' => $request->input('status'),
        ]);

        return response()->json(['message' => 'Berhasil Update Status Pengguna'], 200);
    })->name('users.update');

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
        // da($request->all());
        try {
            // if ($sumDebit !== $sumCredit) {
            //     DB::rollBack();
            //     return redirect()->route('saldo-awal.index')
            //         ->with('message', 'Gagal Update Saldo Awal: Debit dan Kredit tidak sama')
            //                            ->with('color', 'red');
            // }

            foreach ($ids as $index => $id) {
                $debit = (int) str_replace('.', '', $debitValues[$index]);
                $credit = (int) str_replace('.', '', $creditValues[$index]);
    
                Coa::where('id', $id)->update([
                    'saldo_awal_debit' => $debit,
                    'saldo_awal_credit' => $credit,
                ]);
            }
    
            DB::commit();
            Alert::success('Sukses!', 'Berhasil Update Saldo Awal');
            return redirect()->route('saldo-awal.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Oops!', 'Gagal Update Saldo Awal: Terjadi kesalahan');
            return redirect()->route('saldo-awal.index');
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
    Route::get('/cekTrial',[JurnalController::class, 'cekTrial'])->name('cekTrial');

    // Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('report')->group(function () {
        // Report Controller
        Route::get('daftarjurnal', [ReportController::class, 'daftarJurnal'])->name('report.daftarjurnal');
        Route::get('transaksi/{id}', [ReportController::class, 'transaksi'])->name('report.transaksi');
        
        // Report View
        Route::get('labarugi', [ReportController::class, 'labaRugi'])->name('report.views.labarugi');
        Route::get('labaRugiView', [ReportController::class, 'labaRugiView'])->name('labaRugiView');
        Route::get('perubahanekuitas', [ReportController::class, 'perubahanEkuitas'])->name('report.views.perubahanekuitas');
        Route::get('neraca', [ReportController::class, 'neraca'])->name('report.views.neraca');
        Route::get('neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('report.views.neracasaldo');
        Route::get('neraca-perbandingan', [ReportController::class, 'neracaPerbandingan'])->name('report.views.neracaperbandingan');
        Route::get('aruskas', [ReportController::class, 'arusKas'])->name('report.views.aruskas');
        Route::get('bukubesar', [ReportController::class, 'bukuBesar'])->name('report.views.bukubesar');
        Route::get('mutasi-saldo', [ReportController::class, 'mutasiSaldo'])->name('report.views.mutasisaldo');
        
        // Report PDF
        Route::post('labarugi', [ReportController::class, 'labaRugi'])->name('report.labarugi');
        Route::post('labarugidownloadpdf', [ReportController::class, 'labaRugiDownloadPDF'])->name('report.labarugiprint');
        Route::post('perubahanekuitas', [ReportController::class, 'perubahanEkuitas'])->name('report.perubahanekuitas');
        Route::post('neraca', [ReportController::class, 'neraca'])->name('report.neraca');
        Route::post('neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('report.neracasaldo');
        Route::post('neraca-perbandingan', [ReportController::class, 'neracaPerbandingan'])->name('report.neracaperbandingan');
        Route::post('aruskas', [ReportController::class, 'arusKas'])->name('report.aruskas');
        Route::post('bukubesar', [ReportController::class, 'bukuBesar'])->name('report.bukubesar');
        Route::post('mutasi-saldo', [ReportController::class, 'mutasiSaldo'])->name('report.mutasisaldo');
        
        Route::get('print-coa', [CoaController::class, 'printCoa'])->name('report.print-coa');
        Route::get('preview-coa', [CoaController::class, 'previewCoa'])->name('report.preview-coa');
    });

    // API Rouye
    Route::prefix('api')->group(function () {
        Route::get('users', function(){
            if(auth()->user()->roles == 'superadmin'){
                $user = User::where('id', '!=', auth()->user()->id)->orderBy('name', 'asc')->get();
                return response()->json($user);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses'
                ], 403);
            }
        });

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

            Alert::success('Sukses!', 'Berhasil Update Saldo Awal');
            return redirect()->route('saldo-awal.index');
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
});

require __DIR__.'/auth.php';

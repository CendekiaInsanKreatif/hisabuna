<?php

use App\Http\Controllers\CoaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Report_Controller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use App\Imports\JurnalDetailImport;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/test-mail', function () {
        $to = request('to', 'jikurto01@gmail.com');
        Mail::to($to)->send(new TestMail());
        return "OK — terkirim ke {$to}";
    });

    Route::get('upgrade', function () {
        return view('upgrade.index');
    })->name('upgrade.index');

    Route::get('/subscription/upgrade', [SubscriptionController::class, 'showUpgradePage'])->name('subscription.upgrade');
    Route::get('/subscription/token', [SubscriptionController::class, 'getSnapToken']);
    Route::get('/subscription/success', [SubscriptionController::class, 'paymentSuccess']);
    Route::get('/subscription/renew', [SubscriptionController::class, 'renew'])->name('subscription.renew');
    Route::get('/payment/details/{order}', [SubscriptionController::class, 'show'])
        ->name('payment.details');

    // 27 mei 2025
    Route::get('/invoice/preview/{orderId}', [SubscriptionController::class, 'previewInvoice'])
        ->name('invoice.preview')
        ->middleware('auth');

    Route::get('/invoices/{invoice}/download', function (App\Models\Invoice $invoice) {
        $path = storage_path('app/'.$invoice->file_path);

        if (! file_exists($path)) {
            abort(404, 'Invoice tidak tersedia');
        }

        return response()->download($path, "invoice_{$invoice->order_id}.pdf");
    })->name('invoice.download')->middleware('auth');

    Route::get('/invoice', function () {
        $invoices = Auth::user()->invoices()->latest()->get();

        return view('invoices.index', compact('invoices'));
    })->name('invoices.index')->middleware('auth');

    //  Main Route
    Route::resource('coas', CoaController::class);
    Route::get('jurnal/data', [JurnalController::class, 'getData'])->name('jurnal.data');
    Route::get('jurnal/totals', [JurnalController::class, 'getTotals'])->name('jurnal.totals');
    Route::get('jurnal/months', [JurnalController::class, 'getAvailableMonths'])->name('jurnal.months');
    Route::resource('jurnal', JurnalController::class);

    // AJAX Data Route untuk optimisasi frontend

    // Export Routes untuk Jurnal
    Route::get('jurnal/export/all', [JurnalController::class, 'exportAllJurnal'])->name('jurnal.export.all');
    Route::get('jurnal/export/date/{startDate?}/{endDate?}', [JurnalController::class, 'exportJurnalByDate'])->name('jurnal.export.date');
    Route::post('jurnal/export', [JurnalController::class, 'exportJurnal'])->name('jurnal.export');
    // Route::get('cekTrial')

    // Users
    Route::get('users', function () {
        return view('users.index');
    })->name('users.index');

    Route::post('users/store', function (Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'required|string|max:15',
            'no_telp' => 'nullable|string|max:15',
            'periode' => 'required|date',
            'profile' => 'required|string|in:trial,standard,pro,enterprise',
            'password' => 'required|string|min:6',
            'company_name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'no_hp' => $validatedData['no_hp'],
            'no_telp' => $validatedData['no_telp'],
            'periode' => $validatedData['periode'],
            'profile' => $validatedData['profile'],
            'password' => Hash::make($validatedData['password']),
            'roles' => 'user',
            'is_active' => 1,
            'company_name' => $validatedData['company_name'],
        ]);

        if ($request->hasFile('image')) {
            $lampiranFile = $request->file('image');
            $filePath = 'profiles/'.$user->company_name;
            $fileName = $user->id.'.'.$lampiranFile->getClientOriginalExtension();
            $tempPath = $lampiranFile->getPathName();

            try {
                $imagick = new Imagick($tempPath);
                $imagick->setImageCompressionQuality(30);
                $compressedImagePath = storage_path('app/public/'.$filePath.'/'.$fileName);
                $directoryPath = storage_path('app/public/'.$filePath);
                if (! file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }
                $imagick->writeImage($compressedImagePath);
                $imagick->clear();
                $imagick->destroy();
                $user->company_logo = $filePath.'/'.$fileName;
                $user->save();
            } catch (ImagickException $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        Alert::success('Sukses!', 'Berhasil Tambah User');

        return redirect()->route('users.index');
    })->name('users.store');

    Route::get('users/{id}', function ($id) {
        $users = User::where('id', $id)->get();

        return view('users.show', compact('users'));
    })->name('users.show');

    // Route::put('users/{id}', function(Request $request, $id) {
    //     $user = User::findOrFail($id);
    //     $user->update($request->except(['password', 'password_confirmation']));

    //     return redirect()->route('users.index')->with('message', 'Berhasil Update Pengguna')->with('color', 'green');
    // })->name('users.update');

    Route::delete('users/{id}', function ($id) {
        $user = User::findOrFail($id);
        $user->is_deleted = 1;
        $user->save();

        return redirect()->route('users.index')->with('message', 'Berhasil Nonaktifkan Pengguna')->with('color', 'green');
    })->name('users.destroy');

    Route::put('users/{id}', function (Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update($request->except(['password', 'password_confirmation']));

        Alert::success('Sukses!', 'Berhasil Update Pengguna');

        return redirect()->route('users.index');
    })->name('users.update');

    // Arus Kas
    Route::get('arus-kas', function () {
        return view('arus-kas.index');
    })->name('arus-kas.index');

    Route::get('arus-kas/data', function () {
        $arusKas = Coa::whereNull('is_deleted')
            ->where('created_by', Auth::user()->id)
            ->where('level', '=', 4)
            ->orderBy('nomor_akun', 'asc')
            ->get();

        return response()->json($arusKas);
    })->name('arus.kas.data');

    Route::match(['put', 'patch'], 'arus-kas/{id}', function (Request $request, $id) {
        try {
            $coa = Coa::where('id', $id)
                ->where('created_by', Auth::user()->id)
                ->first();

            if (! $coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan',
                ], 404);
            }

            $coa->update([
                'arus_kas' => $request->input(in_array($request->input('_method'), ['PUT', 'PATCH']) ? 'arus_kas' : 'value'),
            ]);

            Alert::success('Sukses!', 'Berhasil Update Arus Kas');

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Update Arus Kas',
                'title' => 'Sukses!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data',
                'title' => 'Error!',
            ], 500);
        }
    })->name('arus-kas.update');

    Route::get('saldo-awal', function () {
        return view('saldo-awal.index');
    })->name('saldo-awal.index');

    Route::match(['put', 'patch'], 'saldo-awal', function (Request $request) {
        $ids = $request->input('id');
        $debitValues = $request->input('saldo_awal_debit');
        $creditValues = $request->input('saldo_awal_credit');
        $sumDebit = array_sum(array_map(function ($value) {
            return (int) str_replace('.', '', $value);
        }, $debitValues));
        $sumCredit = array_sum(array_map(function ($value) {
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
    Route::get('jurnal/lampiran', function () {
        return 'fafa';
    })->name('jurnal.lampiran');
    Route::get('/cekTrial', [JurnalController::class, 'cekTrial'])->name('cekTrial');
    Route::get('/totalJurnal', [JurnalController::class, 'totalJurnal'])->name('totalJurnal');
    Route::post('/printReport', [ReportController::class, 'printJurnalFilter'])->name('printReport');
    Route::post('/filterCoa', [CoaController::class, 'filterCoa'])->name('filterCoa');
    Route::post('/filterCoaLevel', [CoaController::class, 'filterCoaLevel'])->name('filterCoaLevel');
    Route::delete('/deleteCoa/{id}', [CoaController::class, 'destroy'])->name('deleteCoa');
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
        Route::post('perubahanekuitas', [ReportController::class, 'perubahanEkuitas'])->name('report.perubahanekuitas');
        Route::post('neraca-perbandingan', [ReportController::class, 'neraca'])->name('report.neraca');
        Route::post('neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('report.neracasaldo');
        Route::post('aruskas', [ReportController::class, 'arusKas'])->name('report.aruskas');
        Route::post('bukubesar', [ReportController::class, 'bukuBesar'])->name('report.bukubesar');
        Route::post('mutasi-saldo', [ReportController::class, 'mutasiSaldo'])->name('report.mutasisaldo');
        // Route::post('labarugidownloadpdf', [ReportController::class, 'labaRugiDownloadPDF'])->name('report.labarugiprint');
        Route::post('neraca', [ReportController::class, 'neracaPerbandingan'])->name('report.neracaperbandingan');

        Route::get('print-coa', [CoaController::class, 'printCoa'])->name('report.print-coa');
        Route::get('preview-coa', [CoaController::class, 'previewCoa'])->name('report.preview-coa');
    });

    // API Rouye
    Route::prefix('api')->group(function () {
        Route::get('users', function () {
            if (Auth::user()->roles == 'superadmin') {
                $user = User::where('id', '!=', Auth::user()->id)->where('is_deleted', '!=', 1)->orderBy('name', 'asc')->get();

                return response()->json($user);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses',
                ], 403);
            }
        });

        Route::get('/jurnalBy/{id}', [JurnalController::class, 'getByID'])->name('jurnal.byID');
        Route::get('labarugi', [Report_Controller::class, 'labaRugi'])->name('api.labarugi');
        Route::get('perubahanekuitas', [Report_Controller::class, 'perubahanEkuitas'])->name('api.perubahanekuitas');
        Route::get('neraca', [Report_Controller::class, 'neraca'])->name('api.neraca');
        Route::get('neraca-saldo', [Report_Controller::class, 'neracaSaldo'])->name('api.neracasaldo');
        Route::get('neraca-perbandingan', [Report_Controller::class, 'neracaPerbandingan'])->name('api.neracaperbandingan');
        Route::get('aruskas', [Report_Controller::class, 'arusKas'])->name('api.aruskas');

        Route::get('coas', function () {
            $coa = Coa::whereNull('is_deleted')
                ->where('created_by', Auth::user()->id)
                ->orderBy('nomor_akun')
                ->orderBy('level')
                ->get();

            return response()->json($coa);
        });

        Route::get('arus-kas', function () {
            $arusKas = Coa::whereNull('is_deleted')->where('created_by', Auth::user()->id)->where('level', '=', 4)->orderBy('nomor_akun', 'asc')->get();

            return response()->json($arusKas);
        });

        Route::get('saldo-awal', function () {
            $saldoAwal = Coa::whereNull('is_deleted')->where('created_by', Auth::user()->id)->where('level', '=', 5)->orderBy('nomor_akun', 'asc')->get();

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

        Route::get('/uploadsample', function (Request $request) {
            DB::beginTransaction();
            try {
                $periode = Auth::user()->periode;
                $tanggal_dibuat = $periode.'-'.date('m-d');
                $dataJurnal = Jurnal::create([
                    'jenis' => 'JV',
                    'no_urut_transaksi' => 1,
                    'no_transaksi' => '1',
                    'jurnal_tgl' => $tanggal_dibuat,
                    'subtotal' => 5360178622,
                    'keterangan' => 'Jurnal Umum Import Sample',
                    'created_by' => Auth::user()->id,
                    'created_at' => $tanggal_dibuat,
                ]);

                if ($dataJurnal) {
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
                            $periode = Auth::user()->periode;
                            $tanggal_dibuat = $periode.'-'.date('m-d');
                            $dtal = JurnalDetail::create([
                                'jurnal_id' => $dataJurnal->id,
                                'coa_akun' => $coa->nomor_akun,
                                'debit' => $debit,
                                'credit' => $credit,
                                'keterangan' => $row['keterangan'],
                                'tanggal_bukti' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_bukti']))->format('Y-m-d H:i:s'),
                                'created_by' => Auth::user()->id,
                                'created_at' => $tanggal_dibuat,
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
                        } else {
                            if ($rCount < $count) {
                            } else {
                                DB::rollBack();

                                return response()->json([
                                    'success' => false,
                                    'message' => 'Akun '.$no_akun.' tidak ditemukan',
                                ], 404);
                            }
                        }

                        $rCount++;
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengupload sample',
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload sample: '.$e->getMessage(),
                ], 500);
            }
        });

        Route::get('coa-update', function () {
            $coa = Coa::whereNull('is_deleted')->where('created_by', Auth::user()->id)->get();

            return response()->json($coa);
        });

        Route::get('jurnal', function () {

            // $jurnal = Cache::remember('jurnal_' . Auth::user()->id, now()->addMinutes(10), function () {
            // return Jurnal::with('details.coa')
            // ->whereNull('is_deleted')
            // ->where('created_by', Auth::user()->id)
            // ->orderByRaw('CAST(no_urut_transaksi AS UNSIGNED) DESC')
            // ->get();
            // });

            // Fixed Error view Jurnal (dede)
            $jurnal = Jurnal::with([
                'details' => function ($query) {
                    $query->select('id', 'jurnal_id', 'coa_akun', 'debit', 'credit', 'tanggal_bukti', 'lampiran', 'created_by');
                },
                'details.coa' => function ($query) {
                    $query->select('nomor_akun', 'nama_akun', 'created_by');
                },
            ])
                ->whereNull('is_deleted')
                ->where('created_by', Auth::user()->id)
                ->orderByRaw('CAST(no_urut_transaksi AS UNSIGNED) DESC')
                ->get();

            return response()->json($jurnal);
        });

        Route::get('files', function () {
            Storage::disk('ftp')->put('test.txt', 'Hello World');
        });
    });

    Route::prefix('view')->group(function () {
        Route::get('daftarjurnal', function () {
            $jurnal = Jurnal::with('details')->where('created_by', Auth::user()->id)->get();

            // da($jurnal);
            return view('report.daftarjurnal', compact('jurnal'));
        });

        Route::get('transaksi', function () {
            return view('report.transaksi');
        });
    });

    Route::get('all-report', function () {

        $coa = Coa::whereNull('is_deleted')->where('level', 5)->where('created_by', Auth::user()->id)->get();

        return view('report.template', compact('coa'));
    })->name('report.template');

    // Route::get('test-neraca', function(){

    // });

});

require __DIR__.'/auth.php';

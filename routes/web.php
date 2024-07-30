<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Report_Controller;
use App\Http\Controllers\ArusKasController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Models\Coa;
use App\Models\Jurnal;
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

    Route::match(['put', 'patch'], 'saldo-awal/{id}', function(Request $request, $id) {

        if(strpos($request->input('saldo_awal_debit'), '.') !== false){
            $debit = str_replace('.', '', $request->input('saldo_awal_debit'));
            $credit = str_replace('.', '', $request->input('saldo_awal_credit'));
        }else{
            $debit = $request->input('saldo_awal_debit');
            $credit = $request->input('saldo_awal_credit');
        }

        Coa::where('id', $id)->update([
            'saldo_awal_debit' => $debit,
            'saldo_awal_credit' => $credit,
        ]);

        return redirect()->route('saldo-awal.index')->with('message', 'Berhasil Update Saldo Awal')->with('color', 'green');
    })->name('saldo-awal.update');


    //  Custom Route
    Route::post('coas/import', [CoaController::class, 'import'])->name('coas.import');
    Route::post('coas/export', [CoaController::class, 'export'])->name('coas.export');
    Route::post('jurnal/import', [JurnalController::class, 'import'])->name('jurnal.import');
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
            // da($coa);
            return response()->json($coa);
        });
        
        Route::get('arus-kas', function () {
            $arusKas = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get();
            return response()->json($arusKas);
        });

        Route::get('saldo-awal', function () {
            $saldoAwal = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->orderBy('saldo_awal_debit', 'desc')->get();
            return response()->json($saldoAwal);
        });

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

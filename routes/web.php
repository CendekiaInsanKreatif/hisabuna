<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use App\Models\Coa;
use App\Models\Jurnal;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('coas', CoaController::class);
    Route::resource('jurnal', JurnalController::class);
    Route::post('coas/import', [CoaController::class, 'import'])->name('coas.import');
    Route::post('coas/export', [CoaController::class, 'export'])->name('coas.export');

    Route::post('jurnal/import', [JurnalController::class, 'import'])->name('jurnal.import');
    Route::post('jurnal/sample/export', [JurnalController::class, 'sampleExport'])->name('jurnal.sample.export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('report')->group(function () {
        Route::get('daftarjurnal', [ReportController::class, 'daftarJurnal'])->name('report.daftarjurnal');
        Route::get('transaksi/{id}', [ReportController::class, 'transaksi'])->name('report.transaksi');
        Route::get('bukubesar', [ReportController::class, 'bukuBesar'])->name('report.bukubesar');
        Route::get('bukubesar/download', [ReportController::class, 'downloadBukuBesar'])->name('report.bukubesar.download');
        Route::get('labarugi', [ReportController::class, 'labaRugi'])->name('report.labarugi');
        Route::get('perubahanekuitas', [ReportController::class, 'perubahanEkuitas'])->name('report.perubahanekuitas');
        Route::get('neraca', [ReportController::class, 'neraca'])->name('report.neraca');
        Route::get('neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('report.neracasaldo');
        Route::get('neraca-perbandingan', [ReportController::class, 'neracaPerbandingan'])->name('report.neracaperbandingan');
        Route::get('arus-kas', [ReportController::class, 'arusKas'])->name('report.aruskas');
    });

    Route::prefix('api')->group(function () {
        Route::get('coas', function () {
            $coa = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get();
            return response()->json($coa);
        });

        Route::get('jurnal', function () {
            $jurnal = Jurnal::with(['details.coa'])
                ->whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->orderBy('jurnal_tgl', 'desc')
                ->get();
            return response()->json($jurnal);
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

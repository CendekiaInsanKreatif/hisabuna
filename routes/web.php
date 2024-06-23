<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CoaController;
use Illuminate\Support\Facades\Route;

use App\Models\Coa;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('coas', CoaController::class);
    Route::post('coas/import', [CoaController::class, 'import'])->name('coas.import');
    Route::post('coas/export', [CoaController::class, 'export'])->name('coas.export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('api')->group(function () {
        Route::get('coas', function () {
            $coa = Coa::whereNull('is_deleted')->get();
            return response()->json($coa);
        });
    });
});

require __DIR__.'/auth.php';

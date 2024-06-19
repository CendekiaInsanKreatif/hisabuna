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
    Route::get('/listCoa',[CoaController::class,'list'])->name('listCoa');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('api')->group(function () {
        Route::get('coas', function () {
            $coa = Coa::all()->toArray();
            return response()->json($coa);
        });
    });
});

require __DIR__.'/auth.php';

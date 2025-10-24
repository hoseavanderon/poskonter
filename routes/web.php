<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PembukuanController;

/*
|--------------------------------------------------------------------------
| Web Routes - Tahap 1 (Menampilkan Produk di POS)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', [PosController::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/transactions/today', [App\Http\Controllers\PosController::class, 'today'])->name('pos.today');
    Route::get('/pos/digital-data', [PosController::class, 'digitalData'])->name('pos.digital.data');
    Route::post('/pos/digital/checkout', [PosController::class, 'digitalCheckout'])->name('pos.digital.checkout');
    Route::get('/digital-transactions', [PosController::class, 'getDigitalTransactions'])->name('digital.transactions');
    Route::delete('/pos/transactions/{transaction}', [PosController::class, 'deleteTransaction'])->name('pos.transaction.delete');
    Route::delete('/digital-transactions/{transaction}', [PosController::class, 'deleteDigitalTransaction'])
        ->name('digital.transaction.delete');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/pembukuan', [PembukuanController::class, 'index'])->name('pembukuan');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PembukuanController;
use App\Http\Controllers\InventoryHistoryController;
use App\Http\Controllers\StokBarangController;
use App\Http\Controllers\BarangMasukController;

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
    Route::get('/pos/transactions/today', [PosController::class, 'today'])->name('pos.today');
    Route::get('/pos/digital-data', [PosController::class, 'digitalData'])->name('pos.digital.data');
    Route::post('/pos/digital/checkout', [PosController::class, 'digitalCheckout'])->name('pos.digital.checkout');
    Route::get('/digital-transactions', [PosController::class, 'getDigitalTransactions'])->name('digital.transactions');
    Route::delete('/pos/transactions/{transaction}', [PosController::class, 'deleteTransaction'])->name('pos.transaction.delete');
    Route::delete('/digital-transactions/{transaction}', [PosController::class, 'deleteDigitalTransaction'])
        ->name('digital.transaction.delete');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/data', [RiwayatController::class, 'getData'])->name('riwayat.data');
    Route::get('/riwayat/years', [RiwayatController::class, 'getAvailableYears'])->name('riwayat.years');
    Route::get('/riwayat/data-range', [RiwayatController::class, 'getDataRange']);

    Route::get('/pembukuan', [PembukuanController::class, 'index'])->name('pembukuan');
    Route::post('/cashbook', [PembukuanController::class, 'store'])->name('cashbook.store');
    Route::delete('/cashbook/{id}', [PembukuanController::class, 'destroy'])->name('cashbook.destroy');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::post('/customers/pay-debt/{id}', [CustomerController::class, 'payDebt'])->name('customers.payDebt');

    Route::get('/history-inventory', [InventoryHistoryController::class, 'index'])->name('history_inventory');
    Route::get('/history-inventory/data', [InventoryHistoryController::class, 'getData'])->name('history_inventory.data');

    Route::get('/stokbarang', [StokBarangController::class, 'index'])->name('stokbarang');
    Route::get('/stok-barang/data', [StokBarangController::class, 'getData'])->name('stok.data');

    Route::get('/barangmasuk', [BarangMasukController::class, 'index'])->name('barangmasuk');
    Route::get('/api/suppliers', [BarangMasukController::class, 'searchSupplier']);
    Route::get('/api/products', [BarangMasukController::class, 'searchProduct']);
    Route::get('/api/attribute-values/{productId}', [BarangMasukController::class, 'getAttributeValues']);
    Route::get('/api/harga-values/{productId}', [BarangMasukController::class, 'getHargaValues']);
    Route::post('/barangmasuk/submit', [BarangMasukController::class, 'store'])->name('barangmasuk.store');
});

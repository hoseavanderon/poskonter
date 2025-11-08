<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PembukuanController;
use App\Http\Controllers\InventoryHistoryController;
use App\Http\Controllers\StokBarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\CetakBarcodeController;

// Redirect ke login saat akses root
Route::get('/', function () {
    return redirect()->route('login');
});

// =======================================================
// 🧩 MIDDLEWARE LOGIN (auth + verified)
// =======================================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // ===================================================
    // 💼 Admin
    // ===================================================
    Route::get('/admin-pos', [AdminController::class, 'index'])->name('admin-pos');

    // ===================================================
    // 💼 POS & TRANSAKSI
    // ===================================================
    Route::get('/', [PosController::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/transactions/today', [PosController::class, 'today'])->name('pos.today');
    Route::get('/pos/digital-data', [PosController::class, 'digitalData'])->name('pos.digital.data');
    Route::post('/pos/digital/checkout', [PosController::class, 'digitalCheckout'])->name('pos.digital.checkout');
    Route::get('/digital-transactions', [PosController::class, 'getDigitalTransactions'])->name('digital.transactions');
    Route::delete('/pos/transactions/{transaction}', [PosController::class, 'deleteTransaction'])->name('pos.transaction.delete');
    Route::delete('/digital-transactions/{transaction}', [PosController::class, 'deleteDigitalTransaction'])->name('digital.transaction.delete');
    Route::get('/pos/close-book-data', [PosController::class, 'getCloseBookData'])->name('pos.closebook.data');
    Route::post('/cashbook/store', [PosController::class, 'pembukuanStore'])->name('cashbook.store');
    Route::get('/pos/load-more', [PosController::class, 'loadMoreProducts'])->name('pos.loadMore');

    // ===================================================
    // 📜 RIWAYAT TRANSAKSI
    // ===================================================
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/data', [RiwayatController::class, 'getData'])->name('riwayat.data');
    Route::get('/riwayat/years', [RiwayatController::class, 'getAvailableYears'])->name('riwayat.years');
    Route::get('/riwayat/data-range', [RiwayatController::class, 'getDataRange']);

    // ===================================================
    // 📘 PEMBUKUAN
    // ===================================================
    Route::get('/pembukuan', [PembukuanController::class, 'index'])->name('pembukuan');
    Route::post('/cashbook', [PembukuanController::class, 'store'])->name('cashbooks.store');
    Route::delete('/cashbook/{id}', [PembukuanController::class, 'destroy'])->name('cashbook.destroy');

    // ===================================================
    // 👥 CUSTOMER / LANGGANAN
    // ===================================================
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::post('/customers/pay-debt/{id}', [CustomerController::class, 'payDebt'])->name('customers.payDebt');

    // ===================================================
    // 📦 HISTORY STOK
    // ===================================================
    Route::get('/history-inventory', [InventoryHistoryController::class, 'index'])->name('history_inventory');
    Route::get('/history-inventory/data', [InventoryHistoryController::class, 'getData'])->name('history_inventory.data');

    // ===================================================
    // 🏷️ STOK BARANG
    // ===================================================
    Route::get('/stokbarang', [StokBarangController::class, 'index'])->name('stokbarang');
    Route::get('/stok-barang/data', [StokBarangController::class, 'getData'])->name('stok.data');

    // ===================================================
    // 🚚 BARANG MASUK
    // ===================================================
    Route::get('/barangmasuk', [BarangMasukController::class, 'index'])->name('barangmasuk');
    Route::get('/api/suppliers', [BarangMasukController::class, 'searchSupplier']);
    Route::get('/api/products', [BarangMasukController::class, 'searchProduct']);
    Route::get('/api/attribute-values/{productId}', [BarangMasukController::class, 'getAttributeValues']);
    Route::get('/api/harga-values/{productId}', [BarangMasukController::class, 'getHargaValues']);
    Route::post('/barangmasuk/submit', [BarangMasukController::class, 'store'])->name('barangmasuk.store');

    // ===================================================
    // 🚚 BARANG MASUK
    // ===================================================
    Route::get('/cetakbarcode', [CetakBarcodeController::class, 'index'])->name('cetakbarcode');
    Route::post('/cetakbarcode/print', [CetakBarcodeController::class, 'print'])->name('cetakbarcode.print');
});

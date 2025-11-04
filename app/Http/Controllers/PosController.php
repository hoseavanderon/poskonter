<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\DigitalAdminRule;
use App\Models\Device;
use App\Models\App;
use App\Models\DigitalCategory;
use App\Models\DigitalProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Cashbook;

class PosController extends Controller
{
    public function index()
    {
        $outletId = Auth::user()->outlet_id ?? 1;

        $productsRaw = Product::with([
            'category:id,kode_category,name',
            'attributeValues:id,product_id,product_attribute_id,attribute_value,stok',
        ])
            ->where('outlet_id', $outletId)
            ->get();

        $products = $productsRaw->map(function ($p) {
            $rawPrice = $p->jual ?? $p->price ?? $p->harga_jual ?? $p->harga ?? null;
            $price = 0;

            if (!is_null($rawPrice)) {
                $clean = preg_replace('/[^\d]/', '', (string) $rawPrice);
                $price = $clean === '' ? 0 : (int) $clean;
            }

            return [
                'id' => $p->id,
                'name' => $p->name ?? '(Tanpa Nama)',
                'code' => $p->barcode ?? '-',
                'price' => $price,
                'stock' => (int) ($p->attributeValues->sum('stok') ?? 0),
                'category_id' => $p->category_id,
                'category_name' => $p->category?->name ?? '(Tanpa Kategori)',
                'category_code' => $p->category?->kode_category ?? '',
                'attribute_values' => $p->attributeValues->map(fn($attr) => [
                    'id' => $attr->id,
                    'product_attribute_id' => $attr->product_attribute_id,
                    'attribute_value' => $attr->attribute_value ?? '(Tidak ada)',
                    'stok' => (int) $attr->stok,
                ])->values(),
            ];
        })->values();

        $categories = Category::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name ?? '(Tanpa Nama)',
            ])
            ->toArray();

        $customers = class_exists(Customer::class)
            ? Customer::where('outlet_id', $outletId)
            ->orderBy('name')
            ->get(['id', 'name'])
            : collect([]);

        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|numeric|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'cart.*.product_attribute_value_id' => 'nullable|exists:product_attribute_values,id',
            'subtotal' => 'required|numeric',
            'dibayar' => 'required|numeric',
            'kembalian' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $isHutang = !empty($data['customer_id']);

        // ===============================
        // 💡 Generate nomor nota harian
        // ===============================
        $tanggal = now()->format('Ymd');
        $outletId = Auth::user()->outlet_id ?? 1;

        $todayCount = \App\Models\Transaction::withTrashed()
            ->where('outlet_id', $outletId)
            ->whereDate('created_at', today())
            ->count() + 1;

        // Format: TRX-<OutletID>-YYYYMMDD-XXX
        $nomorNota = 'TRX-' . str_pad($outletId, 2, '0', STR_PAD_LEFT) . '-' .
            $tanggal . '-' .
            str_pad($todayCount, 3, '0', STR_PAD_LEFT);

        // ===============================
        // Simpan transaksi utama
        // ===============================

        $transaction = \App\Models\Transaction::create([
            'subtotal' => $data['subtotal'],
            'dibayar' => $data['dibayar'],
            'kembalian' => $data['kembalian'],
            'nomor_nota' => $nomorNota,
            'outlet_id' => $outletId,
            'is_lunas' => $isHutang ? 0 : 1,
            'customer_id' => $data['customer_id'] ?? null,
            'paid_at' => $isHutang ? null : now(),
        ]);
        // ===============================
        // Simpan detail transaksi & kurangi stok
        // ===============================
        foreach ($data['cart'] as $item) {
            \App\Models\DetailTransaction::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'harga_satuan' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);

            $product = \App\Models\Product::find($item['id']);

            // 🧩 Cek apakah produk punya varian (product_attribute_value_id)
            if (!empty($item['product_attribute_value_id'])) {
                $variant = \App\Models\ProductAttributeValue::find($item['product_attribute_value_id']);

                if ($variant) {
                    $stokAwal = $variant->stok;
                    $qtyKeluar = $item['qty'];

                    // ✅ Kurangi stok varian
                    $variant->decrement('stok', $qtyKeluar);
                    $variant->update(['last_sale_date' => now()]);

                    // ✅ Catat history stok keluar
                    \App\Models\InventoryHistory::create([
                        'product_id' => $product->id,
                        'product_attribute_value_id' => $variant->id,
                        'type' => 'OUT',
                        'pcs' => $qtyKeluar,
                        'keterangan' => 'Penjualan tanggal ' . now()->translatedFormat('d F Y'),
                        'outlet_id' => $outletId,
                    ]);

                    \Log::info('✅ Kurangi stok varian', [
                        'variant_id' => $variant->id,
                        'stok_awal' => $stokAwal,
                        'qty_keluar' => $qtyKeluar,
                        'stok_akhir' => $variant->fresh()->stok,
                    ]);
                } else {
                    \Log::warning('⚠️ Varian tidak ditemukan', ['id' => $item['product_attribute_value_id']]);
                }
            } else {
                // ⚠️ Jika tidak ada varian, log saja agar tahu
                \Log::warning('Produk tanpa varian tidak dikurangi stok', [
                    'product_id' => $item['id'],
                    'product_name' => $product->name ?? '(Tanpa Nama)',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $isHutang ? 'Transaksi berhasil dicatat sebagai utang pelanggan.' : 'Transaksi tunai berhasil disimpan.',
            'data' => $transaction,
        ]);
    }

    public function today()
    {
        $today = now()->startOfDay();
        $outletId = Auth::user()->outlet_id ?? 1;

        $transactions = \App\Models\Transaction::with([
            'details.product.category',
            'customer:id,name',
        ])
            ->where('outlet_id', $outletId)
            ->whereDate('created_at', $today)
            ->orderByDesc('id')
            ->get();

        $summary = [
            'total_penjualan' => $transactions->sum('subtotal'),
            'jumlah_transaksi' => $transactions->count(),
            'total_produk_terjual' => $transactions->flatMap->details->sum('qty'),
        ];

        $categorySummary = [];
        foreach ($transactions as $trx) {
            foreach ($trx->details as $detail) {
                $catName = $detail->product->category->name ?? 'Tanpa Kategori';
                $categorySummary[$catName] = ($categorySummary[$catName] ?? 0) + $detail->qty;
            }
        }

        $summary['categories'] = collect($categorySummary)
            ->map(fn($pcs, $name) => ['name' => $name, 'pcs' => $pcs])
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'transactions' => $transactions->map(fn($trx) => [
                'id' => $trx->id,
                'nomor_nota' => $trx->nomor_nota,
                'subtotal' => (int)$trx->subtotal,
                'dibayar' => (int)$trx->dibayar,
                'kembalian' => (int)$trx->kembalian,
                'created_at' => $trx->created_at->format('H:i'),
                'customer_id' => $trx->customer_id,
                'customer' => $trx->customer ? [
                    'id' => $trx->customer->id,
                    'name' => $trx->customer->name,
                ] : null,
                'paid_at' => $trx->paid_at
                    ? ($trx->paid_at instanceof \Carbon\Carbon
                        ? $trx->paid_at->format('Y-m-d H:i:s')
                        : (string)$trx->paid_at)
                    : null,
                'details' => $trx->details->map(fn($d) => [
                    'product' => $d->product->name ?? '',
                    'qty' => $d->qty,
                    'subtotal' => (int)$d->subtotal,
                ]),
            ])->values()->toArray(),
            'summary' => $summary,
        ]);
    }

    public function digitalData()
    {
        $outletId = Auth::user()->outlet_id ?? 1;

        try {
            $devices = Device::with([
                'apps' => function ($q) {
                    $q->select('apps.id', 'apps.name', 'apps.description', 'apps.logo');
                }
            ])
                ->where('outlet_id', $outletId)
                ->get(['id', 'name', 'notes', 'icon', 'outlet_id']);

            $apps = App::select('id', 'name', 'description', 'logo')->get();

            $categories = DigitalCategory::select('id', 'name')->get();

            $brands = \App\Models\DigitalBrand::select('id', 'name', 'logo')
                ->orderBy('name')
                ->get();

            $products = DigitalProduct::with([
                'category:id,name',
                'app:id,name,logo',
                'digitalBrands:id,name,logo'
            ])
                ->select(
                    'id',
                    'digital_category_id',
                    'name',
                    'code',
                    'base_price',
                    'is_fixed',
                    'app_id'
                )
                ->get();

            $rules = DigitalAdminRule::with('category:id,name')
                ->select(
                    'id',
                    'digital_category_id',
                    'min_nominal',
                    'max_nominal',
                    'admin_fee'
                )
                ->get();

            return response()->json([
                'success' => true,
                'devices' => $devices,
                'apps' => $apps,
                'categories' => $categories,
                'brands' => $brands,
                'products' => $products,
                'rules' => $rules,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data digital',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function digitalCheckout(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'app_id' => 'required|exists:apps,id',
            'digital_brand_id' => 'nullable|exists:digital_brands,id',
            'digital_product_id' => 'required|exists:digital_products,id',
            'customer_id' => 'nullable|exists:customers,id',
            'nominal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'dibayar' => 'required|numeric|min:0',
            'kembalian' => 'required|numeric',
            'total' => 'required|numeric|min:0',
        ]);

        try {
            $outletId = Auth::user()->outlet_id ?? 1;
            $isHutang = !empty($validated['customer_id']);

            // Simpan ke tabel digital_transactions
            $transaction = \App\Models\DigitalTransaction::create([
                'digital_product_id' => $validated['digital_product_id'],
                'digital_brand_id' => $validated['digital_brand_id'] ?? null,
                'device_id' => $validated['device_id'],
                'app_id' => $validated['app_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'nominal' => $validated['nominal'],
                'harga_jual' => $validated['harga_jual'],
                'subtotal' => $validated['subtotal'],
                'dibayar' => $validated['dibayar'],
                'kembalian' => $validated['kembalian'],
                'total' => $validated['total'],
                'outlet_id' => $outletId,
                'paid_at' => $isHutang ? null : now(),
            ]);

            // =======================================
            // 💡 Generate nomor nota DIGITAL unik
            // =======================================
            $tanggal = now()->format('Ymd');

            // Hitung total transaksi digital hari ini (dengan trashed)
            $todayCount = \App\Models\DigitalTransaction::withTrashed()
                ->where('outlet_id', $outletId)
                ->whereDate('created_at', today())
                ->count() + 1;

            // Format: DIG-<OutletID>-YYYYMMDD-XXX
            do {
                $nomorNota = 'DIG-' . str_pad($outletId, 2, '0', STR_PAD_LEFT) . '-' .
                    $tanggal . '-' .
                    str_pad($todayCount, 3, '0', STR_PAD_LEFT);
                $todayCount++;
            } while (\App\Models\DigitalTransaction::withTrashed()->where('nomor_nota', $nomorNota)->exists());

            $transaction->nomor_nota = $nomorNota;
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => $isHutang
                    ? 'Transaksi digital berhasil dicatat sebagai utang pelanggan.'
                    : 'Transaksi digital tunai berhasil disimpan.',
                'data' => $transaction,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi digital.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDigitalTransactions(Request $request)
    {
        $outletId = Auth::user()->outlet_id ?? 1; // ✅ outlet yang sedang login
        $appId = $request->input('app_id');

        // Jika app_id dikirim → tampilkan detail transaksi dari 1 app
        if ($appId) {
            $transactions = \App\Models\DigitalTransaction::with(['product', 'app'])
                ->where('app_id', $appId)
                ->where('outlet_id', $outletId) // ✅ filter berdasarkan outlet login
                ->whereDate('created_at', today())
                ->latest()
                ->get();

            $summaryTotal = $transactions->sum('subtotal');

            $formatted = $transactions->map(fn($trx) => [
                'id' => $trx->id,
                'nomor_nota' => $trx->nomor_nota,
                'app' => $trx->app->name ?? '-',
                'product' => $trx->product->name ?? '-',
                'subtotal' => (int) $trx->subtotal,
                'total' => (int) $trx->total,
                'created_at' => $trx->created_at->format('H:i'),
                'details' => [[
                    'product' => $trx->product->name ?? '-',
                    'qty' => 1,
                    'subtotal' => (int) $trx->subtotal,
                ]],
            ]);

            return response()->json([
                'success' => true,
                'summary_total' => $summaryTotal,
                'transactions' => $formatted,
            ]);
        }

        // Jika tidak ada app_id → kirim rekap total & jumlah transaksi per app
        $today = now()->startOfDay();

        $summaryByApp = \App\Models\DigitalTransaction::selectRaw('app_id, SUM(subtotal) as total, COUNT(*) as count')
            ->where('outlet_id', $outletId) // ✅ hanya outlet yang login
            ->whereDate('created_at', $today)
            ->groupBy('app_id')
            ->get()
            ->mapWithKeys(fn($row) => [
                $row->app_id => [
                    'total' => (int) $row->total,
                    'count' => (int) $row->count,
                ]
            ]);

        return response()->json([
            'success' => true,
            'summary_per_app' => $summaryByApp,
        ]);
    }

    public function deleteTransaction($id)
    {
        try {
            $transaction = \App\Models\Transaction::withTrashed()
                ->with('details.product.attributeValues')
                ->findOrFail($id);

            // 🧩 Soft delete detail transaksi + kembalikan stok
            if ($transaction->details()->exists()) {
                foreach ($transaction->details as $detail) {
                    $attr = $detail->product->attributeValues->first();
                    if ($attr) {
                        $attr->increment('stok', $detail->qty);
                        $attr->update(['last_restock_date' => now()]);

                        // Catat retur di inventory_histories
                        \App\Models\InventoryHistory::create([
                            'product_id' => $detail->product_id,
                            'product_attribute_value_id' => $attr->id,
                            'type' => 'IN',
                            'pcs' => $detail->qty,
                            'keterangan' => 'RETUR: Pembatalan transaksi ' . $transaction->nomor_nota,
                            'outlet_id' => \Illuminate\Support\Facades\Auth::user()->outlet_id ?? 1,
                        ]);
                    }
                }

                // Soft delete semua detail
                $transaction->details()->delete();
            }

            // 🧩 Soft delete transaksi utama
            $transaction->delete();

            // ✅ Tambahkan response JSON di akhir
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus dan stok dikembalikan.',
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDigitalTransaction($id)
    {
        try {
            $trx = \App\Models\DigitalTransaction::findOrFail($id);
            $trx->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi digital berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi digital.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCloseBookData()
    {
        $today = now()->toDateString();
        $outletId = Auth::user()->outlet_id ?? 1; // ✅ Ambil outlet_id user login

        try {
            // === Barang (produk fisik)
            $barangTotal = DB::table('detail_transaction')
                ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
                ->where('transactions.outlet_id', $outletId) // ✅ Filter outlet
                ->whereDate('transactions.created_at', $today)
                ->sum('detail_transaction.subtotal');

            // === Digital per App (tanpa tarik & transfer)
            $digitalPerApp = DB::table('digital_transactions')
                ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
                ->where('digital_transactions.outlet_id', $outletId) // ✅ Filter outlet
                ->whereDate('digital_transactions.created_at', $today)
                ->whereNotIn('digital_transactions.digital_product_id', [112, 113, 114, 115, 116])
                ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
                ->groupBy('apps.name')
                ->get();

            // === Total Transfer (digital_product_id = 6)
            $totalTransfer = DB::table('digital_transactions')
                ->where('outlet_id', $outletId) // ✅ Filter outlet
                ->where('digital_product_id', [112, 114, 115])
                ->whereDate('created_at', $today)
                ->sum('subtotal');

            $totalTarik = DB::table('digital_transactions')
                ->where('outlet_id', $outletId)
                ->whereIn('digital_product_id', [113, 116]) // ← pakai whereIn
                ->whereDate('created_at', $today)
                ->sum('subtotal');

            // === Utang dari transaksi fisik
            $utangFisik = DB::table('transactions')
                ->join('customers', 'customers.id', '=', 'transactions.customer_id')
                ->where('transactions.outlet_id', $outletId) // ✅ Filter outlet
                ->whereDate('transactions.created_at', $today)
                ->whereNotNull('transactions.customer_id')
                ->whereNull('transactions.paid_at')
                ->select('customers.name', 'transactions.subtotal')
                ->get();

            // === Utang dari transaksi digital
            $utangDigital = DB::table('digital_transactions')
                ->join('customers', 'customers.id', '=', 'digital_transactions.customer_id')
                ->where('digital_transactions.outlet_id', $outletId) // ✅ Filter outlet
                ->whereDate('digital_transactions.created_at', $today)
                ->whereNotNull('digital_transactions.customer_id')
                ->whereNull('digital_transactions.paid_at')
                ->select('customers.name', 'digital_transactions.subtotal')
                ->get();

            // === Gabungkan utang fisik + digital
            $utangList = $utangFisik->merge($utangDigital)
                ->groupBy('name')
                ->map(fn($items) => [
                    'name' => $items->first()->name,
                    'subtotal' => $items->sum('subtotal'),
                ])
                ->values();

            // === Hitung total penjualan dan total akhir
            $totalDigital = $digitalPerApp->sum('total');
            $totalPenjualan = $barangTotal + $totalDigital;
            $totalUtang = $utangList->sum('subtotal');
            $totalSetelahUtang = max(0, $totalPenjualan - $totalUtang);

            // 💡 Grand total = totalSetelahUtang (tidak termasuk transfer)
            $grandTotal = $totalSetelahUtang;

            return response()->json([
                'tanggal' => now()->translatedFormat('d F Y'),
                'barangTotal' => $barangTotal,
                'digitalPerApp' => $digitalPerApp,
                'utangList' => $utangList,
                'totalPenjualan' => $totalPenjualan,
                'totalUtang' => $totalUtang,
                'totalSetelahUtang' => $totalSetelahUtang,
                'grandTotal' => $grandTotal,
                'totalTransfer' => $totalTransfer,
                'totalTarik' => $totalTarik,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tutup buku',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function pembukuanStore(Request $request)
    {
        try {
            // 🧭 Log awal saat request diterima
            \Log::info('📘 [PembukuanStore] Request diterima', [
                'raw_body' => $request->getContent(),
            ]);

            // pastikan JSON body dikonversi ke array request
            $request->merge(json_decode($request->getContent(), true));

            \Log::info('📘 [PembukuanStore] Setelah decode JSON', [
                'merged_request' => $request->all(),
            ]);

            // 🧾 Validasi data
            $validated = $request->validate([
                'deskripsi' => 'required|string|max:255',
                'type' => 'required|string|in:IN,OUT',
                'nominal' => 'required|numeric|min:0',
                'cashbook_category_id' => 'required|integer',
                'cashbook_wallet_id' => 'required|integer',
                'outlet_id' => 'required|integer',
            ]);

            \Log::info('📘 [PembukuanStore] Data validasi berhasil', $validated);

            // 💾 Simpan data ke tabel cashbook
            $cashbook = \App\Models\Cashbook::create($validated);

            \Log::info('✅ [PembukuanStore] Data berhasil disimpan', [
                'cashbook_id' => $cashbook->id,
                'nominal' => $cashbook->nominal,
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Data pembukuan berhasil disimpan.',
                'data' => $cashbook,
            ]);
        } catch (\Throwable $th) {
            \Log::error('💥 [PembukuanStore] Gagal menyimpan pembukuan', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan saat menyimpan ke pembukuan.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

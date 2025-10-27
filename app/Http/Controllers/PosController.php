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

class PosController extends Controller
{
    public function index()
    {
        $productsRaw = Product::with([
            'category:id,kode_category,name',
            'attributeValues:id,product_id,product_attribute_id,attribute_value,stok',
        ])->get();

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
            ? Customer::orderBy('name')->get(['id', 'name'])
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

            // 🧩 Jika produk punya varian yang dipilih
            if (!empty($item['variant_id'])) {
                $variant = \App\Models\ProductAttributeValue::find($item['variant_id']);
                if ($variant) {
                    $variant->decrement('stok', $item['qty']);
                    $variant->update(['last_sale_date' => now()]);

                    // Catat history
                    \App\Models\InventoryHistory::create([
                        'product_id' => $product->id,
                        'product_attribute_value_id' => $variant->id,
                        'type' => 'OUT',
                        'pcs' => $item['qty'],
                        'keterangan' => 'Penjualan ' . now()->translatedFormat('d F Y'),
                        'outlet_id' => Auth::user()->outlet_id ?? 1,
                    ]);
                }
            } else {
                // Produk tanpa varian
                if ($product && isset($product->stok)) {
                    $product->decrement('stok', $item['qty']);
                }
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

        $transactions = \App\Models\Transaction::with([
            'details.product.category',
            'customer:id,name', // ambil nama pelanggan
        ])
            ->whereDate('created_at', $today)
            ->orderByDesc('id')
            ->get();

        // ringkasan dasar
        $summary = [
            'total_penjualan' => $transactions->sum('subtotal'),
            'jumlah_transaksi' => $transactions->count(),
            'total_produk_terjual' => $transactions->flatMap->details->sum('qty'),
        ];

        // kumpulkan kategori yang terjual hari ini
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
                'subtotal' => (int) $trx->subtotal,
                'dibayar' => (int) $trx->dibayar,
                'kembalian' => (int) $trx->kembalian,
                'created_at' => $trx->created_at->format('H:i'),
                'customer_id' => $trx->customer_id, // penting untuk status utang/tunai
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
                    'subtotal' => (int) $d->subtotal,
                ]),
            ])->values()->toArray(), // <-- ini kuncinya
            'summary' => $summary,
        ]);
    }

    public function digitalData()
    {
        $outletId = Auth::user()->outlet_id ?? 1;

        try {
            // === 1️⃣ Ambil devices (berdasarkan outlet) ===
            $devices = Device::with([
                'apps' => function ($q) {
                    $q->select('apps.id', 'apps.name', 'apps.description', 'apps.logo');
                }
            ])
                ->where('outlet_id', $outletId)
                ->get(['id', 'name', 'notes', 'icon', 'outlet_id']);

            // === 2️⃣ Ambil semua apps ===
            $apps = App::select('id', 'name', 'description', 'logo')->get();

            // === 3️⃣ Ambil semua kategori digital ===
            $categories = DigitalCategory::select('id', 'name')->get();

            // === 4️⃣ Ambil semua brand digital ===
            $brands = \App\Models\DigitalBrand::select('id', 'name', 'logo')
                ->orderBy('name')
                ->get();

            // === 5️⃣ Ambil semua produk digital (relasi ke kategori, brand, dan app) ===
            $products = DigitalProduct::with([
                'category:id,name',
                'app:id,name,logo',
                'digitalBrands:id,name,logo' // ✅ relasi many-to-many
            ])
                ->select(
                    'id',
                    'digital_category_id',
                    'name',
                    'code',
                    'type',
                    'base_price',
                    'is_fixed',
                    'app_id'
                )
                ->get();

            // === 6️⃣ Ambil semua aturan admin fee ===
            $rules = DigitalAdminRule::with('category:id,name')
                ->select(
                    'id',
                    'digital_category_id',
                    'min_nominal',
                    'max_nominal',
                    'admin_fee'
                )
                ->get();

            // === ✅ Return data lengkap ===
            return response()->json([
                'success' => true,
                'devices' => $devices,
                'apps' => $apps,
                'categories' => $categories,
                'brands' => $brands, // 🆕 tambahkan ke response
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
        $appId = $request->input('app_id');

        // Jika app_id dikirim → tampilkan detail transaksi dari 1 app
        if ($appId) {
            $transactions = \App\Models\DigitalTransaction::with(['product', 'app'])
                ->where('app_id', $appId)
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
}

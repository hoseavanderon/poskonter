<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\Cashbook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    public function index()
    {
        // Tidak kirim data besar — hanya view kosong
        return view('barang_masuk.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            foreach ($data['products'] as $item) {
                // 🔹 1. Cek apakah kombinasi product + attribute_value sudah ada
                $attrValue = ProductAttributeValue::where('product_id', $item['product_id'])
                    ->where('attribute_value', $item['attribute_value'])
                    ->first();

                if ($attrValue) {
                    // Jika attribute value sudah ada → tambahkan stok
                    $attrValue->increment('stok', (int) $item['pcs']);
                } else {
                    // 🔹 Cari ID attribute utama produk (misalnya “Warna”, “Expired Date”, dsb)
                    $attributeId = \App\Models\ProductAttribute::where('name', $item['product_attribute'])
                        ->value('id');

                    // Jika tidak ditemukan, aman: lewati saja tanpa error
                    if (!$attributeId) {
                        \Log::warning("Product attribute '{$item['product_attribute']}' tidak ditemukan untuk product_id {$item['product_id']}");
                        continue;
                    }

                    // 🔹 Jika attribute_value baru → buat baris baru
                    ProductAttributeValue::create([
                        'product_id' => $item['product_id'],
                        'product_attribute_id' => $attributeId,
                        'attribute_value' => $item['attribute_value'],
                        'stok' => $item['pcs'],
                        'outlet_id' => \Illuminate\Support\Facades\Auth::user()->outlet_id ?? null,
                        'last_restock_date' => now(),
                    ]);
                }

                // 🔹 2. Cek dan update harga modal produk bila berbeda
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $hargaBaru = (int) preg_replace('/[^\d]/', '', $item['harga'] ?? 0);

                    if ($hargaBaru && $hargaBaru !== (int) $product->modal) {
                        $product->update(['modal' => $hargaBaru]);
                    }
                }
            }

            // 🔹 3. Jika dicentang, masukkan ke pembukuan
            if (!empty($data['addToBookkeeping'])) {
                $supplierName = $data['supplier']['name'] ?? 'Tanpa Supplier';

                \App\Models\Cashbook::create([
                    'outlet_id' => \Illuminate\Support\Facades\Auth::user()->outlet_id ?? null,
                    'type' => 'OUT',
                    'nominal' => $data['subtotal'],
                    'deskripsi' => "Nota dari {$supplierName}", // ✅ FIX: gunakan kolom deskripsi
                    'cashbook_category_id' => 2,
                    'cashbook_wallet_id' => 1,
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Barang masuk berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json([
                'message' => 'Gagal menyimpan data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // 🔍 API: cari supplier
    public function searchSupplier(Request $request)
    {
        $query = $request->input('q');
        $suppliers = Supplier::select('id', 'name', 'no_wa')
            ->when($query, fn($q) => $q->where('name', 'like', "%$query%"))
            ->limit(10)
            ->get();

        return response()->json($suppliers);
    }

    // 🔍 API: cari produk
    public function searchProduct(Request $request)
    {
        $query = $request->input('q');

        $products = Product::select('id', 'name')
            ->with(['attributeValues.productAttribute:id,name,data_type'])
            ->when($query, fn($q) => $q->where('name', 'like', "%$query%"))
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $attribute = $product->attributeValues->first()?->productAttribute;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'attribute_name' => $attribute->name ?? '-',
                    'data_type' => $attribute->data_type ?? 'text',
                ];
            });

        return response()->json($products);
    }

    // 🔍 API: ambil attribute_value berdasarkan product dan attribute
    public function getAttributeValues($productId)
    {
        $values = ProductAttributeValue::where('product_id', $productId)
            ->pluck('attribute_value');

        return response()->json($values);
    }

    public function getHargaValues($productId)
    {
        // Ambil daftar harga modal untuk produk tertentu
        $hargaList = \App\Models\Product::where('id', $productId)
            ->pluck('modal') // 🟢 ambil dari kolom 'modal'
            ->unique()
            ->filter()
            ->values();

        return response()->json($hargaList);
    }
}

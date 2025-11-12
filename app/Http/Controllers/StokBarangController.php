<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shelf;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Auth;

class StokBarangController extends Controller
{
    public function index()
    {
        return view('stok_barang.index');
    }

    public function getData()
    {
        $outletId = Auth::user()->outlet_id; // ✅ Ambil outlet ID dari user login

        // 🔹 Ambil semua shelf + produk milik outlet yang sedang login
        $shelves = \App\Models\Shelf::with(['products' => function ($q) {
            $q->select('id', 'shelf_id', 'name', 'barcode', 'minimal_stok', 'outlet_id');
        }])
            ->where('outlet_id', $outletId) // ✅ filter berdasarkan outlet login
            ->get();

        foreach ($shelves as $shelf) {
            // 🔸 Tentukan ikon shelf (gunakan fallback jika file tidak ada)
            $iconName = $shelf->icon ?? 'archive-box';
            $iconPath = resource_path("views/components/heroicon-o-{$iconName}.blade.php");
            if (!file_exists($iconPath)) {
                $iconName = 'archive-box';
            }
            $shelf->icon_component = $iconName;

            foreach ($shelf->products as $product) {
                $attributeValues = \App\Models\ProductAttributeValue::where('product_id', $product->id)
                    ->join('product_attributes', 'product_attribute_values.product_attribute_id', '=', 'product_attributes.id')
                    ->select(
                        'product_attributes.name as name',
                        'product_attribute_values.attribute_value as value',
                        'product_attribute_values.stok as stok'
                    )
                    ->get();

                $product->stok = $attributeValues->sum('stok');
                $product->attributes = $attributeValues;
            }

            // 🔹 Hitung low stock
            $shelf->lowStock = $shelf->products->filter(fn($p) => $p->stok < $p->minimal_stok)->count();
        }

        return response()->json($shelves);
    }
}

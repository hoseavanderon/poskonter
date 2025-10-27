<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shelf;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;

class StokBarangController extends Controller
{
    public function index()
    {
        return view('stok_barang.index');
    }

   public function getData()
    {
        $shelves = \App\Models\Shelf::with(['products' => function ($q) {
            $q->select('id', 'shelf_id', 'name', 'barcode', 'minimal_stok');
        }])->get();

        foreach ($shelves as $shelf) {
            // Ambil nama ikon dari database
            $iconName = $shelf->icon ?? 'archive-box';

            // Lokasi file Blade Heroicon
            $iconPath = resource_path("views/components/heroicon-o-{$iconName}.blade.php");

            // Fallback jika file ikon tidak ada
            if (!file_exists($iconPath)) {
                $iconName = 'archive-box';
            }

            // Simpan nama ikon yang valid untuk Alpine
            $shelf->icon_component = $iconName;

            // Produk & atribut
            foreach ($shelf->products as $product) {
                $attributes = \App\Models\ProductAttributeValue::where('product_id', $product->id)
                    ->join('product_attributes', 'product_attribute_values.product_attribute_id', '=', 'product_attributes.id')
                    ->select('product_attributes.name as name', 'product_attribute_values.attribute_value as value')
                    ->get();

                $latest = \App\Models\ProductAttributeValue::where('product_id', $product->id)
                    ->orderByDesc('updated_at')
                    ->first();

                $product->stok = $latest->stok ?? 0;
                $product->attributes = $attributes;
            }

            // Hitung low stock
            $shelf->lowStock = $shelf->products->filter(fn ($p) => $p->stok < $p->minimal_stok)->count();
        }

        return response()->json($shelves);
    }

}

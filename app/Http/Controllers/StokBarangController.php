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
        $outletId = Auth::user()->outlet_id;

        $shelves = Shelf::with(['products' => function ($q) {
            $q->select('id', 'shelf_id', 'name', 'barcode', 'minimal_stok', 'outlet_id', 'jual');
        }])
            ->where('outlet_id', $outletId)
            ->get();

        foreach ($shelves as $shelf) {
            // Icon
            $iconName = $shelf->icon ?? 'archive-box';
            $iconPath = resource_path("views/components/heroicon-o-{$iconName}.blade.php");
            if (!file_exists($iconPath)) {
                $iconName = 'archive-box';
            }
            $shelf->icon_component = $iconName;

            foreach ($shelf->products as $product) {
                // Ambil attribute + value + stok
                $rawAttributes = ProductAttributeValue::where('product_id', $product->id)
                    ->join('product_attributes', 'product_attribute_values.product_attribute_id', '=', 'product_attributes.id')
                    ->select(
                        'product_attributes.name as attribute_name',
                        'product_attribute_values.attribute_value as value',
                        'product_attribute_values.stok as stok'
                    )
                    ->get();

                // Group by attribute_name → biar jadi seperti { "Warna": [ {value, stok}, ... ] }
                $grouped = $rawAttributes->groupBy('attribute_name')->map(function ($items, $key) {
                    return [
                        'name' => $key,
                        'values' => $items->map(function ($i) {
                            return [
                                'value' => $i->value,
                                'stok' => (int)$i->stok,
                            ];
                        })->values()
                    ];
                })->values();

                $product->stok = $rawAttributes->sum('stok');
                $product->attributes = $grouped;
            }

            $shelf->lowStock = $shelf->products->filter(fn($p) => $p->stok < $p->minimal_stok)->count();
        }

        return response()->json($shelves);
    }
}

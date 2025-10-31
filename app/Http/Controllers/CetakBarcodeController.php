<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CetakBarcodeController extends Controller
{
    public function index()
    {
        // 🔹 Ambil outlet_id dari user yang sedang login
        $outletId = Auth::user()->outlet_id;

        // 🔹 Ambil produk yang hanya milik outlet tersebut
        $products = DB::table('product_attribute_values')
            ->join('products', 'products.id', '=', 'product_attribute_values.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.outlet_id', $outletId) // ✅ filter berdasarkan outlet user login
            ->select(
                'product_attribute_values.id',
                'products.id as product_id',
                'products.name',
                'products.barcode',
                'products.jual',
                'brands.name as brand',
                'categories.name as category',
                'product_attribute_values.attribute_value as variant',
                'product_attribute_values.stok as stock'
            )
            ->orderBy('products.name', 'asc')
            ->get();

        // 🔹 Kirim ke view
        return view('cetak_barcode.index', [
            'products' => $products
        ]);
    }
}

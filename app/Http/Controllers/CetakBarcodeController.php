<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function print(Request $request)
    {
        // Ambil data JSON dari form
        $items = json_decode($request->input('items', '[]'), true);

        if (!is_array($items) || empty($items)) {
            return back()->with('error', 'Data item tidak valid atau kosong.');
        }

        $labels = [];

        // 🔹 Ambil data produk
        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            $qty = (int) ($item['qty'] ?? 0);
            if (!$id || $qty <= 0) continue;

            $product = DB::table('product_attribute_values')
                ->join('products', 'products.id', '=', 'product_attribute_values.product_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->where('product_attribute_values.id', $id)
                ->select(
                    'products.name',
                    'products.barcode',
                    'products.jual',
                    'brands.name as brand',
                    'categories.name as category',
                    'product_attribute_values.attribute_value as variant'
                )
                ->first();

            if (!$product) continue;

            for ($i = 0; $i < $qty; $i++) {
                $labels[] = clone $product;
            }
        }

        if (!count($labels)) {
            return back()->with('error', 'Tidak ada produk untuk dicetak.');
        }

        // 🔹 Buat Barcode Base64 untuk preview

        $d = new \Milon\Barcode\DNS1D();
        $d->setStorPath(storage_path('framework/barcodes'));

        foreach ($labels as &$lbl) {
            try {
                $lbl->barcode_svg = $d->getBarcodeSVG($lbl->barcode, 'C128', 1, 25, 'black', false);
            } catch (\Exception $e) {
                $lbl->barcode_svg = null;
            }
        }

        // === MODE 1: PREVIEW DI BROWSER ===
        if ($request->has('preview')) {
            // Bagi per 2 label satu baris
            $chunks = array_chunk($labels, 2);

            return view('cetak_barcode.print', [
                'chunks' => $chunks,
                'pageWidth' => 60, // 2x30mm
                'pageHeight' => 15
            ]);
        }

        // === MODE 2: EXPORT .PRN UNTUK PRINTER TSPL ===
        $tspl = "";

        foreach ($labels as $index => $lbl) {
            if ($index === 0) {
                $tspl .= "SIZE 60 mm, 15 mm\n";
                $tspl .= "GAP 0 mm, 0 mm\n";
                $tspl .= "DIRECTION 1\n";
                $tspl .= "CLS\n";
            }

            // Kolom kiri atau kanan
            $x = ($index % 2 == 0) ? 10 : 310;
            $y = 10;

            $name = addslashes($lbl->name); // hindari karakter spesial error di TSPL

            // Cetak teks dan barcode
            $tspl .= "TEXT {$x},10,\"3\",0,1,1,\"{$name}\"\n";
            $tspl .= "BARCODE {$x},40,\"128\",60,1,0,2,2,\"{$lbl->barcode}\"\n";
            $tspl .= "TEXT {$x},120,\"3\",0,1,1,\"Rp " . number_format($lbl->jual, 0, ',', '.') . "\"\n";
            $tspl .= "TEXT {$x},140,\"3\",0,1,1,\"{$lbl->barcode}\"\n";

            // Setiap 2 label = 1 baris
            if ($index % 2 == 1 || $index == count($labels) - 1) {
                $tspl .= "PRINT 1,1\nCLS\n";
            }
        }

        // Simpan & kirim file TSPL
        $filePath = storage_path('app/label.prn');
        file_put_contents($filePath, $tspl);

        return response()->download($filePath, 'label.prn')->deleteFileAfterSend(true);
    }
}

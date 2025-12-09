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

        // Ambil produk sesuai input (tetap clone supaya aman)
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

        // Normalisasi: hapus item kosong & yang tidak punya barcode
        $labels = array_values(array_filter($labels, function ($l) {
            return !empty($l) && !empty($l->barcode);
        }));

        if (!count($labels)) {
            return back()->with('error', 'Tidak ada produk untuk dicetak.');
        }

        // Buat Barcode SVG (Milon)
        $d = new \Milon\Barcode\DNS1D();
        $d->setStorPath(storage_path('framework/barcodes'));

        foreach ($labels as &$lbl) {
            try {
                // tweak scale (3rd param) & height (4th param) sesuai kebutuhan
                $lbl->barcode_svg = $d->getBarcodeSVG($lbl->barcode, 'C128', 1.0, 30, 'black', false);
            } catch (\Exception $e) {
                $lbl->barcode_svg = null;
            }
        }
        unset($lbl);

        // CONFIG: columns & page size (mm)
        $columns = 3;
        $pageWidth = 100;
        $pageHeight = 15;

        // Build rows (robust chunking)
        $rows = [];
        for ($i = 0; $i < count($labels); $i += $columns) {
            $slice = array_slice($labels, $i, $columns);
            if (count($slice) > 0) {
                $rows[] = $slice;
            }
        }

        // Logging untuk debug
        \Log::info('BarcodePrint: labels=' . count($labels) . ' rows=' . count($rows) . ' columns=' . $columns);

        // === MODE PREVIEW BROWSER ===
        if ($request->has('preview')) {
            // buat chunks sesuai rows (view expects $chunks)
            $chunks = $rows;
            return view('cetak_barcode.print', [
                'chunks' => $chunks,
                'pageWidth' => $pageWidth,
                'pageHeight' => $pageHeight,
                'columns' => $columns,
            ]);
        }

        // === MODE EXPORT .PRN TSPL ===
        // Buat PRN unik agar tidak ditimpa job sebelumnya
        $uniqueName = 'label_' . uniqid();
        $filePath = storage_path('app/' . $uniqueName . '.prn');

        // Header TSPL: CLS sekali di awal (lebih stabil)
        $tspl = "";
        $tspl .= "CLS\n";
        $tspl .= "SIZE {$pageWidth} mm, {$pageHeight} mm\n";
        $tspl .= "GAP 0 mm, 0 mm\n";
        $tspl .= "DIRECTION 1\n";

        // Koordinat X untuk kolom: start offset dan step antar kolom (kalibrasi mungkin dibutuhkan)
        $xStart = 10;
        $xStep = 330; // adjust jika perlu (± nilai ini sampai pas)
        $baseY = 10;

        foreach ($rows as $rowIndex => $row) {
            // Draw each column in this row (tanpa CLS di sini)
            foreach ($row as $colIndex => $lbl) {
                $x = $xStart + ($colIndex * $xStep);
                $y = $baseY;

                $name = addslashes($lbl->name ?? '');
                $barcode = $lbl->barcode ?? '';
                $priceText = "Rp " . number_format($lbl->jual ?? 0, 0, ',', '.');

                // TEXT & BARCODE per label (sesuaikan ukuran & offset jika perlu)
                $tspl .= "TEXT {$x},{$y},\"3\",0,1,1,\"{$name}\"\n";
                $tspl .= "BARCODE {$x}," . ($y + 30) . ",\"128\",60,1,0,2,2,\"{$barcode}\"\n";
                $tspl .= "TEXT {$x}," . ($y + 100) . ",\"3\",0,1,1,\"{$priceText}\"\n";
                $tspl .= "TEXT {$x}," . ($y + 120) . ",\"3\",0,1,1,\"{$barcode}\"\n";
            }

            // Cetak sekali untuk seluruh kolom di row ini
            $tspl .= "PRINT 1,1\n";
        }

        // Simpan file PRN unik
        file_put_contents($filePath, $tspl);
        \Log::info('BarcodePrint: generated_prn=' . $filePath);

        // Return file download dan hapus setelah dikirim
        return response()->download($filePath, $uniqueName . '.prn')->deleteFileAfterSend(true);
    }
}

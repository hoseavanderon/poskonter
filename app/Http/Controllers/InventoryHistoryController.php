<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InventoryHistoryController extends Controller
{
    public function index()
    {
        return view('history_inventory.index');
    }

    public function getData(Request $request)
    {
        $barcode = $request->get('barcode');
        $range = $request->get('range');

        if (!$barcode) {
            return response()->json(['details' => []]);
        }

        // Ambil outlet ID dari user yang login
        $outletId = Auth::user()->outlet_id;

        // Ambil produk dari barcode (hanya milik outlet ini)
        $product = Product::where('barcode', $barcode)
            ->where('outlet_id', $outletId)
            ->first();

        if (!$product) {
            return response()->json([
                'product_name' => null,
                'details' => [],
            ]);
        }

        // Default ke hari ini
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        // ✅ Fix parsing range
        if ($range) {
            if (str_contains($range, ' - ')) {
                [$from, $to] = explode(' - ', $range);
            } elseif (str_contains($range, ' to ')) {
                [$from, $to] = explode(' to ', $range);
            } else {
                $from = $to = $range;
            }

            $startDate = Carbon::parse(trim($from))->startOfDay();
            $endDate = Carbon::parse(trim($to))->endOfDay();
        }

        // ✅ Ambil data histori berdasarkan outlet yang login
        $histories = InventoryHistory::with('productAttributeValue.productAttribute')
            ->where('product_id', $product->id)
            ->where('outlet_id', $outletId) // ⬅️ tambahkan ini
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'date_time' => $item->created_at->format('Y-m-d H:i:s'),
                    'attribute_name' => $item->productAttributeValue->productAttribute->name ?? null,
                    'batch_code' => $item->productAttributeValue->attribute_value ?? '-',
                    'in_qty' => $item->type === 'IN' ? $item->pcs : null,
                    'out_qty' => $item->type === 'OUT' ? $item->pcs : null,
                    'note' => $item->keterangan,
                ];
            });

        return response()->json([
            'product_name' => $product->name,
            'details' => $histories,
        ]);
    }
}

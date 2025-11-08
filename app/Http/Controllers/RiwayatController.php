<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $data = $this->fetchDataByDate($today);

        $data['empty'] = (
            $data['categories']->isEmpty() &&
            $data['productTransactions']->isEmpty() &&
            $data['digitalTransactions']->isEmpty()
        );

        return view('riwayat.index', [
            'defaultData' => $data,
        ]);
    }

    public function getData(Request $request)
    {
        $tanggal = $request->query('tanggal', Carbon::today()->format('Y-m-d'));
        $data = $this->fetchDataByDate($tanggal);

        $data['empty'] = (
            $data['categories']->isEmpty() &&
            $data['productTransactions']->isEmpty() &&
            $data['digitalTransactions']->isEmpty()
        );

        return response()->json($data);
    }

    public function getAvailableYears()
    {
        $outletId = Auth::user()->outlet_id;

        $yearsProduct = DB::table('transactions')
            ->where('outlet_id', $outletId)
            ->select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year');

        $yearsDigital = DB::table('digital_transactions')
            ->where('outlet_id', $outletId)
            ->select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year');

        $years = $yearsProduct
            ->union($yearsDigital)
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        return response()->json($years);
    }

    private function fetchDataByDate($tanggal)
    {
        $outletId = Auth::user()->outlet_id;

        // --- CATEGORY SUMMARY
        $categorySummary = DB::table('detail_transaction')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $tanggal)
            ->whereNull('transactions.deleted_at')
            ->where('transactions.outlet_id', $outletId)
            ->select('categories.name', DB::raw('SUM(detail_transaction.qty) as total_pcs'))
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        // --- PRODUCT TRANSACTIONS
        $productTransactions = DB::table('transactions')
            ->where('transactions.outlet_id', $outletId)
            ->leftJoin('detail_transaction', 'detail_transaction.transaction_id', '=', 'transactions.id')
            ->leftJoin('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereDate('transactions.created_at', $tanggal)
            ->whereNull('transactions.deleted_at')
            ->select([
                'transactions.id as transaction_id',
                'transactions.subtotal as total',
                'transactions.created_at',
                DB::raw("GROUP_CONCAT(products.name SEPARATOR '||') as product_names"),
                DB::raw("GROUP_CONCAT(detail_transaction.qty SEPARATOR '||') as product_qtys"),
                DB::raw("GROUP_CONCAT(detail_transaction.subtotal SEPARATOR '||') as product_amounts"),
            ])
            ->groupBy('transactions.id', 'transactions.subtotal', 'transactions.created_at')
            ->orderByDesc('transactions.created_at')
            ->get()
            ->map(function ($t) {
                $names = explode('||', $t->product_names ?? '');
                $qtys = explode('||', $t->product_qtys ?? '');
                $amounts = explode('||', $t->product_amounts ?? '');

                $details = [];
                foreach ($names as $i => $name) {
                    if (!$name) continue;
                    $details[] = [
                        'name' => $name,
                        'qty' => $qtys[$i] ?? 0,
                        'amount' => $amounts[$i] ?? 0,
                    ];
                }

                return [
                    'transaction_id' => $t->transaction_id,
                    'total' => $t->total,
                    'date' => date('Y-m-d', strtotime($t->created_at)),
                    'details' => $details,
                ];
            })
            ->values();

        // --- DIGITAL TRANSACTIONS
        $digitalTransactions = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereDate('digital_transactions.created_at', $tanggal)
            // 🧹 Filter soft delete untuk semua tabel yang pakai SoftDeletes
            ->whereNull('digital_transactions.deleted_at')
            ->select([
                'apps.id as app_id',
                'apps.name as app_name',
                'digital_products.name as product_name',
                'digital_products.digital_category_id',
                'digital_transactions.nominal as qty',
                'digital_transactions.subtotal as amount',
                DB::raw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d %H:%i') as datetime"),
            ])
            ->orderBy('apps.name')
            ->orderByDesc('digital_transactions.created_at')
            ->get()
            ->groupBy('app_name')
            ->map(function ($transactions, $appName) {
                $appId = $transactions->first()->app_id ?? null;

                return [
                    'name' => $appName,
                    'app_id' => $appId,
                    'transactions' => $transactions->map(function ($t) {
                        return [
                            'name' => $t->product_name,
                            'qty' => $t->qty,
                            'amount' => $t->amount,
                            'datetime' => $t->datetime,
                            'category_id' => $t->digital_category_id,
                        ];
                    })->values(),
                    'total' => $transactions->sum('amount'),
                ];
            })
            ->values();



        // --- TOTALS
        $barangTotal = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereNull('transactions.deleted_at')
            ->whereDate('transactions.created_at', $tanggal)
            ->sum('detail_transaction.subtotal');

        $digitalPerApp = DB::table('digital_transactions')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->whereNotIn('digital_transactions.digital_product_id', [112, 113, 114, 115, 116])
            ->whereNull('digital_transactions.deleted_at')
            ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
            ->groupBy('apps.name')
            ->orderByDesc('total')
            ->get();

        $totalTarik = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereIn('digital_product_id', [113, 116]) // ← gunakan whereIn untuk banyak ID
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->sum('subtotal');

        $totalTransfer = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereIn('digital_product_id', [112, 114, 115]) // ← ambil semua ID Transfer Bank
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->sum('subtotal');

        // --- UTANG
        $utangFisik = DB::table('transactions')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereDate('transactions.created_at', $tanggal)
            ->whereNotNull('transactions.customer_id')
            ->whereNull('transactions.paid_at')
            ->select('customers.name', 'transactions.subtotal')
            ->get();

        $utangDigital = DB::table('digital_transactions')
            ->join('customers', 'customers.id', '=', 'digital_transactions.customer_id')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->whereNotNull('digital_transactions.customer_id')
            ->whereNull('digital_transactions.paid_at')
            ->select('customers.name', 'digital_transactions.subtotal')
            ->get();

        $utangList = $utangFisik->merge($utangDigital)
            ->groupBy('name')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->name,
                    'subtotal' => $items->sum('subtotal'),
                ];
            })
            ->values();

        // --- TOTAL AKHIR
        $totalProduk = $productTransactions->sum('total');
        $totalDigital = $digitalTransactions->sum('total');
        $total = $totalProduk + $totalDigital;
        $extra = 0;

        $empty = (
            $categorySummary->isEmpty() &&
            $productTransactions->isEmpty() &&
            $digitalTransactions->isEmpty() &&
            $digitalPerApp->isEmpty() &&
            $utangList->isEmpty()
        );

        return [
            'categories' => $categorySummary,
            'productTransactions' => $productTransactions,
            'digitalTransactions' => $digitalTransactions,
            'total' => $total,
            'extra' => $extra,
            'barangTotal' => $totalProduk,
            'digitalPerApp' => $digitalPerApp,
            'totalTransfer' => $totalTransfer,
            'totalTarik' => $totalTarik,
            'utangList' => $utangList,
            'empty' => $empty,
        ];
    }

    public function getDataRange(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $data = $this->fetchDataBetweenDates($from, $to);

        return response()->json($data);
    }

    private function fetchDataBetweenDates($from, $to)
    {
        $outletId = Auth::user()->outlet_id;
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        // --- CATEGORY SUMMARY
        $categorySummary = DB::table('detail_transaction')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereNull('transactions.deleted_at')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->select('categories.name', DB::raw('SUM(detail_transaction.qty) as total_pcs'))
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        // --- PRODUCT TRANSACTIONS
        $productTransactions = DB::table('transactions')
            ->where('transactions.outlet_id', $outletId)
            ->leftJoin('detail_transaction', 'detail_transaction.transaction_id', '=', 'transactions.id')
            ->leftJoin('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->select([
                'transactions.id as transaction_id',
                'transactions.subtotal as total',
                'transactions.created_at',
                DB::raw("GROUP_CONCAT(products.name SEPARATOR '||') as product_names"),
                DB::raw("GROUP_CONCAT(detail_transaction.qty SEPARATOR '||') as product_qtys"),
                DB::raw("GROUP_CONCAT(detail_transaction.subtotal SEPARATOR '||') as product_amounts"),
            ])
            ->groupBy('transactions.id', 'transactions.subtotal', 'transactions.created_at')
            ->orderByDesc('transactions.created_at')
            ->get()
            ->map(function ($t) {
                $names = explode('||', $t->product_names ?? '');
                $qtys = explode('||', $t->product_qtys ?? '');
                $amounts = explode('||', $t->product_amounts ?? '');

                $details = [];
                foreach ($names as $i => $name) {
                    if (!$name) continue;
                    $details[] = [
                        'name' => $name,
                        'qty' => $qtys[$i] ?? 0,
                        'amount' => $amounts[$i] ?? 0,
                    ];
                }

                return [
                    'transaction_id' => $t->transaction_id,
                    'total' => $t->total,
                    'date' => date('Y-m-d', strtotime($t->created_at)),
                    'details' => $details,
                ];
            })
            ->values();

        // --- DIGITAL TRANSACTIONS
        $digitalTransactions = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNull('digital_transactions.deleted_at')
            ->select([
                'apps.id as app_id', // 🆕 tambahkan app_id
                'apps.name as app_name',
                'digital_products.name as product_name',
                'digital_products.digital_category_id', // 🆕 tambahkan kategori digital
                'digital_transactions.nominal as qty',
                'digital_transactions.subtotal as amount',
                DB::raw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d %H:%i') as datetime"),
            ])
            ->orderBy('apps.name')
            ->orderByDesc('digital_transactions.created_at')
            ->get()
            ->groupBy('app_name')
            ->map(function ($transactions, $appName) {
                // ambil app_id dari salah satu transaksi (semua dalam grup punya app_id sama)
                $appId = $transactions->first()->app_id ?? null;

                return [
                    'name' => $appName,
                    'app_id' => $appId, // 🆕 kirim ke frontend
                    'transactions' => $transactions->map(function ($t) {
                        return [
                            'name' => $t->product_name,
                            'qty' => $t->qty,
                            'amount' => $t->amount,
                            'datetime' => $t->datetime,
                            'category_id' => $t->digital_category_id, // 🆕 kirim kategori ke frontend
                        ];
                    })->values(),
                    'total' => $transactions->sum('amount'),
                ];
            })
            ->values();


        // --- TOTALS
        $barangTotal = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereNull('transactions.deleted_at')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->sum('detail_transaction.subtotal');

        $digitalPerApp = DB::table('digital_transactions')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotIn('digital_transactions.digital_product_id', [112, 113, 114, 115, 116])
            ->whereNull('digital_transactions.deleted_at')
            ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
            ->groupBy('apps.name')
            ->orderByDesc('total')
            ->get();

        $totalTarik = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereIn('digital_product_id', [113, 116])
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->sum('subtotal');

        $totalTransfer = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereIn('digital_product_id', [112, 114, 115])
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->sum('subtotal');

        // --- UTANG
        $utangFisik = DB::table('transactions')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereBetween('transactions.created_at', [$from, $to])
            ->whereNotNull('transactions.customer_id')
            ->whereNull('transactions.paid_at')
            ->select('customers.name', 'transactions.subtotal')
            ->get();

        $utangDigital = DB::table('digital_transactions')
            ->join('customers', 'customers.id', '=', 'digital_transactions.customer_id')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotNull('digital_transactions.customer_id')
            ->whereNull('digital_transactions.paid_at')
            ->select('customers.name', 'digital_transactions.subtotal')
            ->get();

        $utangList = $utangFisik->merge($utangDigital)
            ->groupBy('name')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->name,
                    'subtotal' => $items->sum('subtotal'),
                ];
            })
            ->values();

        // --- TOTAL AKHIR
        $totalProduk = $productTransactions->sum('total');
        $totalDigital = $digitalTransactions->sum('total');
        $total = $totalProduk + $totalDigital;
        $extra = 0;

        $empty = (
            $categorySummary->isEmpty() &&
            $productTransactions->isEmpty() &&
            $digitalTransactions->isEmpty() &&
            $digitalPerApp->isEmpty() &&
            $utangList->isEmpty()
        );

        return [
            'categories' => $categorySummary,
            'productTransactions' => $productTransactions,
            'digitalTransactions' => $digitalTransactions,
            'total' => $total,
            'extra' => $extra,
            'barangTotal' => $totalProduk,
            'digitalPerApp' => $digitalPerApp,
            'totalTransfer' => $totalTransfer,
            'totalTarik' => $totalTarik,
            'utangList' => $utangList,
            'empty' => $empty,
        ];
    }
}

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
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select([
                'apps.name as app_name',
                'digital_products.name as product_name',
                'digital_transactions.nominal as qty',
                'digital_transactions.subtotal as amount',
                DB::raw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d %H:%i') as datetime")
            ])
            ->orderBy('apps.name')
            ->orderByDesc('digital_transactions.created_at')
            ->get()
            ->groupBy('app_name')
            ->map(function ($transactions, $appName) {
                return [
                    'name' => $appName,
                    'transactions' => $transactions->map(function ($t) {
                        return [
                            'name' => $t->product_name,
                            'qty' => $t->qty,
                            'amount' => $t->amount,
                            'datetime' => $t->datetime,
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
            ->whereDate('transactions.created_at', $tanggal)
            ->sum('detail_transaction.subtotal');

        $digitalPerApp = DB::table('digital_transactions')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->where('digital_transactions.outlet_id', $outletId)
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
            ->groupBy('apps.name')
            ->orderByDesc('total')
            ->get();

        $totalTarik = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->where('digital_product_id', 5)
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->sum('subtotal');

        $totalTransfer = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->where('digital_product_id', 6)
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

        // --- CATEGORY SUMMARY (produk fisik)
        $categorySummary = DB::table('detail_transaction')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereBetween('transactions.created_at', [$from, $to])
            ->select('categories.name', DB::raw('SUM(detail_transaction.qty) as total_pcs'))
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        // --- PRODUCT TRANSACTIONS (group per nota)
        $productTransactions = DB::table('transactions')
            ->where('transactions.outlet_id', $outletId)
            ->leftJoin('detail_transaction', 'detail_transaction.transaction_id', '=', 'transactions.id')
            ->leftJoin('products', 'products.id', '=', 'detail_transaction.product_id')
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

        // --- DIGITAL TRANSACTIONS (kecuali tarik tunai & transfer)
        $digitalTransactions = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select([
                'apps.name as app_name',
                'digital_products.name as product_name',
                'digital_transactions.nominal as qty',
                'digital_transactions.subtotal as amount',
                DB::raw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d') as date")
            ])
            ->orderByDesc('digital_transactions.created_at')
            ->get()
            ->groupBy('app_name')
            ->map(function ($items, $appName) {
                return [
                    'name' => $appName,
                    'transactions' => $items->map(fn($i) => [
                        'name' => $i->product_name,
                        'qty' => $i->qty,
                        'amount' => $i->amount,
                    ]),
                    'total' => $items->sum('amount'),
                    'open' => false,
                ];
            })
            ->values();

        // --- TOTAL BARANG
        $barangTotal = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->where('transactions.outlet_id', $outletId)
            ->whereBetween('transactions.created_at', [$from, $to])
            ->sum('detail_transaction.subtotal');

        // --- TOTAL DIGITAL PER APP
        $digitalPerApp = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
            ->groupBy('apps.name')
            ->orderByDesc('total')
            ->get();

        // --- TOTAL TARIK & TRANSFER
        $totalTarik = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->where('digital_product_id', 5)
            ->whereBetween('created_at', [$from, $to])
            ->sum('subtotal');

        $totalTransfer = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->where('digital_product_id', 6)
            ->whereBetween('created_at', [$from, $to])
            ->sum('subtotal');

        // --- UTANG FISIK
        $utangFisik = DB::table('transactions')
            ->where('transactions.outlet_id', $outletId)
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->whereNotNull('transactions.customer_id')
            ->whereNull('transactions.paid_at')
            ->select('customers.name', 'transactions.subtotal')
            ->get();

        // --- UTANG DIGITAL
        $utangDigital = DB::table('digital_transactions')
            ->where('digital_transactions.outlet_id', $outletId)
            ->join('customers', 'customers.id', '=', 'digital_transactions.customer_id')
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotNull('digital_transactions.customer_id')
            ->whereNull('digital_transactions.paid_at')
            ->select('customers.name', 'digital_transactions.subtotal')
            ->get();

        // --- GABUNGKAN UTANG
        $utangList = $utangFisik->merge($utangDigital)
            ->groupBy('name')
            ->map(fn($items) => [
                'name' => $items->first()->name,
                'subtotal' => $items->sum('subtotal'),
            ])
            ->values();

        // --- TOTAL AKHIR
        $totalProduk = $productTransactions->sum('total');
        $totalDigital = $digitalPerApp->sum('total');
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

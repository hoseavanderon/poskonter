<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Jika tidak ada data, beri indikator kosong
        if (
            $data['categories']->isEmpty() &&
            $data['productTransactions']->isEmpty() &&
            $data['digitalTransactions']->isEmpty()
        ) {
            $data['empty'] = true;
        } else {
            $data['empty'] = false;
        }

        return response()->json($data);
    }

    public function getAvailableYears()
    {
        $yearsProduct = DB::table('transactions')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year');

        $yearsDigital = DB::table('digital_transactions')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year');

        $years = $yearsProduct
            ->union($yearsDigital)
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values();

        // Fallback: jika kosong, kembalikan tahun sekarang
        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        return response()->json($years);
    }

    private function fetchDataByDate($tanggal)
    {
        // --- CATEGORY SUMMARY (produk fisik)
        $categorySummary = DB::table('detail_transaction')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $tanggal)
            ->select('categories.name', DB::raw('SUM(detail_transaction.qty) as total_pcs'))
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        // --- PRODUCT TRANSACTIONS (grouped per transaction)
        $productTransactions = DB::table('transactions')
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

        // --- DIGITAL TRANSACTIONS (grouped by app, kecuali tarik tunai & transfer)
        $digitalTransactions = DB::table('digital_transactions')
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select([
                'apps.name as app_name',
                'digital_products.name as product_name',
                'digital_transactions.nominal as qty',
                'digital_transactions.subtotal as amount',
                DB::raw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d') as date")
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
                            'date' => $t->date,
                        ];
                    })->values(),
                    'total' => $transactions->sum('amount'),
                ];
            })
            ->values();

        // --- TOTAL BARANG (semua item transaksi fisik)
        $barangTotal = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $tanggal)
            ->sum('detail_transaction.subtotal');

        // --- TOTAL DIGITAL PER APP (kecuali tarik tunai & transfer)
        $digitalPerApp = DB::table('digital_transactions')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
            ->groupBy('apps.name')
            ->orderByDesc('total')
            ->get();

        // --- TOTAL TARIK (digital_product_id = 5)
        $totalTarik = DB::table('digital_transactions')
            ->where('digital_product_id', 5)
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->sum('subtotal');

        // --- TOTAL TRANSFER (digital_product_id = 6)
        $totalTransfer = DB::table('digital_transactions')
            ->where('digital_product_id', 6)
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->sum('subtotal');

        // --- UTANG DARI TRANSAKSI FISIK (customer_id != null dan paid_at null)
        $utangFisik = DB::table('transactions')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->whereDate('transactions.created_at', $tanggal)
            ->whereNotNull('transactions.customer_id')
            ->whereNull('transactions.paid_at')
            ->select('customers.name', 'transactions.subtotal')
            ->get();

        // --- UTANG DARI TRANSAKSI DIGITAL (customer_id != null dan paid_at null)
        $utangDigital = DB::table('digital_transactions')
            ->join('customers', 'customers.id', '=', 'digital_transactions.customer_id')
            ->whereDate('digital_transactions.created_at', $tanggal)
            ->whereNotNull('digital_transactions.customer_id')
            ->whereNull('digital_transactions.paid_at')
            ->select('customers.name', 'digital_transactions.subtotal')
            ->get();

        // --- GABUNGKAN UTANG FISIK + DIGITAL
        $utangList = $utangFisik->merge($utangDigital)
            ->groupBy('name')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->name,
                    'subtotal' => $items->sum('subtotal'),
                ];
            })
            ->values();

        // --- HITUNG TOTAL UTAMA (tanpa tarik tunai & transfer)
        $totalProduk = $productTransactions->sum('total');
        $totalDigital = $digitalTransactions->sum('amount');
        $total = $totalProduk + $totalDigital; // ⬅️ hanya barang + digital biasa
        $extra = 0;

        // --- TANDAI JIKA DATA KOSONG
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
            'total' => $total, // ⬅️ tidak termasuk tarik & transfer
            'extra' => $extra,
            'barangTotal' => $totalProduk,
            'digitalPerApp' => $digitalPerApp,
            'totalTransfer' => $totalTransfer, // ⬅️ dipisah
            'totalTarik' => $totalTarik,       // ⬅️ dipisah
            'utangList' => $utangList,
            'empty' => $empty,
        ];
    }

    private function fetchDataBetweenDates($from, $to)
    {
        // Gunakan Carbon agar jam-nya lengkap (00:00:00 - 23:59:59)
        $from = \Carbon\Carbon::parse($from)->startOfDay();
        $to = \Carbon\Carbon::parse($to)->endOfDay();

        // --- CATEGORY SUMMARY (produk fisik)
        $categorySummary = DB::table('detail_transaction')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->select('categories.name', DB::raw('SUM(detail_transaction.qty) as total_pcs'))
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        // --- PRODUCT TRANSACTIONS (grouped per transaction)
        $productTransactions = DB::table('transactions')
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

        // --- DIGITAL TRANSACTIONS (kecuali tarik tunai & transfer, grouped by app)
        $digitalTransactions = DB::table('digital_transactions')
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

        // --- TOTAL BARANG (semua item transaksi fisik)
        $barangTotal = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->sum('detail_transaction.subtotal');

        // --- TOTAL DIGITAL PER APP (kecuali tarik tunai & transfer)
        $digitalPerApp = DB::table('digital_transactions')
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotIn('digital_transactions.digital_product_id', [5, 6])
            ->select('apps.name', DB::raw('SUM(digital_transactions.subtotal) as total'))
            ->groupBy('apps.name')
            ->orderByDesc('total')
            ->get();

        // --- TOTAL TARIK (digital_product_id = 5)
        $totalTarik = DB::table('digital_transactions')
            ->where('digital_product_id', 5)
            ->whereBetween('created_at', [$from, $to])
            ->sum('subtotal');

        // --- TOTAL TRANSFER (digital_product_id = 6)
        $totalTransfer = DB::table('digital_transactions')
            ->where('digital_product_id', 6)
            ->whereBetween('created_at', [$from, $to])
            ->sum('subtotal');

        // --- UTANG FISIK + DIGITAL
        $utangFisik = DB::table('transactions')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->whereNotNull('transactions.customer_id')
            ->whereNull('transactions.paid_at')
            ->select('customers.name', 'transactions.subtotal')
            ->get();

        $utangDigital = DB::table('digital_transactions')
            ->join('customers', 'customers.id', '=', 'digital_transactions.customer_id')
            ->whereBetween('digital_transactions.created_at', [$from, $to])
            ->whereNotNull('digital_transactions.customer_id')
            ->whereNull('digital_transactions.paid_at')
            ->select('customers.name', 'digital_transactions.subtotal')
            ->get();

        // Gabungkan dan kelompokkan
        $utangList = $utangFisik->merge($utangDigital)
            ->groupBy('name')
            ->map(fn($items) => [
                'name' => $items->first()->name,
                'subtotal' => $items->sum('subtotal'),
            ])
            ->values();

        // --- TOTAL UTAMA (tanpa tarik & transfer)
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

    public function getDataRange(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $data = $this->fetchDataBetweenDates($from, $to);

        return response()->json($data);
    }
}

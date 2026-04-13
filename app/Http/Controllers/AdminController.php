<?php

namespace App\Http\Controllers;

use App\Models\DigitalTransaction;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        return view('admindashboard.index');
    }

    public function getData(Request $request)
    {
        $period = $request->get('period', 'day');
        [$startDate, $endDate] = $this->getDateRange($period);

        $user = $request->user();
        $outletIds = $this->getOutletIds($request, $user->id);
        $excludedProducts = $this->getExcludedProducts();

        // 🔥 MAIN QUERY (DINAMIS)
        $fisik = $this->getFisikQuery($outletIds)
            ->whereBetween('transactions.created_at', [$startDate, $endDate]);

        $digital = $this->getDigitalQuery($outletIds, $excludedProducts)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // 🔥 CALCULATION
        $totalSales = $fisik->sum('detail_transaction.subtotal')
            + $digital->sum('subtotal');

        $totalItems = $fisik->sum('detail_transaction.qty');
        $totalDigitalTransactions = $digital->count();

        $totalProfit = $this->getProfitQuery($outletIds)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->selectRaw('SUM(detail_transaction.subtotal - (products.modal * detail_transaction.qty)) as profit')
            ->value('profit') ?? 0;

        // 🔥 GROWTH (COMPARE KE PERIODE SEBELUMNYA)
        [$prevStart, $prevEnd] = $this->getPreviousRange($period);

        $prevFisik = $this->getFisikQuery($outletIds)
            ->whereBetween('transactions.created_at', [$prevStart, $prevEnd])
            ->sum('detail_transaction.subtotal');

        $prevDigital = $this->getDigitalQuery($outletIds, $excludedProducts)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('subtotal');

        $prevSales = $prevFisik + $prevDigital;

        $growth = $prevSales > 0
            ? round((($totalSales - $prevSales) / $prevSales) * 100, 1)
            : ($totalSales > 0 ? 100 : 0);

        return response()->json([
            'todaySales' => $totalSales,
            'todayProfit' => $totalProfit,
            'totalTransactions' => $totalItems + $totalDigitalTransactions,
            'totalItems' => $totalItems,
            'totalDigitalTransactions' => $totalDigitalTransactions,
            'monthlySales' => $totalSales,
            'growth' => $growth,
        ]);
    }

    public function getDailyProfitDebug(Request $request)
    {
        $user = $request->user();
        $outletIds = $this->getOutletIds($request, $user->id);

        $startDate = now()->startOfMonth();
        $endDate = now();

        $data = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->selectRaw("
            DATE(transactions.created_at) as date,
            SUM(detail_transaction.subtotal) as subtotal,
            SUM(products.modal * detail_transaction.qty) as modal,
            SUM(detail_transaction.subtotal - (products.modal * detail_transaction.qty)) as profit
        ")
            ->groupBy(DB::raw('DATE(transactions.created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'subtotal' => (int) $item->subtotal,
                    'modal' => (int) $item->modal,
                    'profit' => (int) $item->profit,
                    'formatted' => $item->date . ' => (' .
                        number_format($item->subtotal, 0, ',', '.') . ') - (' .
                        number_format($item->modal, 0, ',', '.') . ') = (' .
                        number_format($item->profit, 0, ',', '.') . ')'
                ];
            });

        return response()->json($data);
    }

    // =========================
    // 🔥 HELPER FUNCTIONS
    // =========================

    private function getOutletIds(Request $request, $userId)
    {
        $selectedOutlet = $request->get('outlet', 'all');

        if ($selectedOutlet === 'all') {
            return Outlet::where('owner_id', $userId)->pluck('id');
        }

        return [$selectedOutlet];
    }

    private function getExcludedProducts()
    {
        return [
            112,
            114,
            115,
            119,
            123,
            124,
            125,
            127,
            128,
            129,
            251,
            203,
            204,
            205,
            206,
            207,
            208,
            209,
            210,
            211,
            212,
            213,
            214,
            215,
            216,
            217,
            218,
            219,
            220,
            221,
            222,
            223,
            224,
            225,
            226,
            227,
            228,
            229,
            230,
            231,
            232,
            233,
            234,
            235,
            236,
            237,
            238,
            239,
            259,
            113,
            116,
            120
        ];
    }

    private function getFisikQuery($outletIds)
    {
        return DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at');
    }

    private function getProfitQuery($outletIds)
    {
        return DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at');
    }

    private function getDigitalQuery($outletIds, $excludedProducts)
    {
        return DigitalTransaction::query()
            ->whereIn('outlet_id', $outletIds)
            ->whereNotIn('digital_product_id', $excludedProducts);
    }

    private function getTodayGrowth($outletIds, $excludedProducts)
    {
        $now = now();

        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $yesterdayStart = $now->copy()->subDay()->startOfDay();
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();

        // 🔥 TODAY (full day)
        $todayFisik = $this->getFisikQuery($outletIds)
            ->whereBetween('transactions.created_at', [$todayStart, $todayEnd])
            ->sum('detail_transaction.subtotal');

        $todayDigital = $this->getDigitalQuery($outletIds, $excludedProducts)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('subtotal');

        $todaySales = $todayFisik + $todayDigital;

        // 🔥 YESTERDAY (full day)
        $yesterdayFisik = $this->getFisikQuery($outletIds)
            ->whereBetween('transactions.created_at', [$yesterdayStart, $yesterdayEnd])
            ->sum('detail_transaction.subtotal');

        $yesterdayDigital = $this->getDigitalQuery($outletIds, $excludedProducts)
            ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
            ->sum('subtotal');

        $yesterdaySales = $yesterdayFisik + $yesterdayDigital;

        // 🔥 GROWTH
        if ($yesterdaySales > 0) {
            return round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1);
        }

        return $todaySales > 0 ? 100 : 0;
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'year':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->startOfDay(), now()->endOfDay()];
        }
    }

    private function getPreviousRange($period)
    {
        switch ($period) {
            case 'week':
                return [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
            case 'month':
                return [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
            case 'year':
                return [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()];
            default:
                return [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()];
        }
    }
}

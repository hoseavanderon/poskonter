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
        $today = now()->toDateString();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $user = $request->user();
        $outletIds = $this->getOutletIds($request, $user->id);
        $excludedProducts = $this->getExcludedProducts();

        // 🔥 TODAY
        $fisikToday = $this->getFisikQuery($outletIds)
            ->whereDate('transactions.created_at', $today);

        $digitalToday = $this->getDigitalQuery($outletIds, $excludedProducts)
            ->whereDate('created_at', $today);

        $todaySales = $fisikToday->sum('detail_transaction.subtotal')
            + $digitalToday->sum('subtotal');

        $totalItems = $fisikToday->sum('detail_transaction.qty');
        $totalDigitalTransactions = $digitalToday->count();

        // 🔥 MONTHLY
        $fisikMonthly = $this->getFisikQuery($outletIds)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->sum('detail_transaction.subtotal');

        $digitalMonthly = $this->getDigitalQuery($outletIds, $excludedProducts)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('subtotal');

        $profitToday = $this->getProfitQuery($outletIds)
            ->whereDate('transactions.created_at', $today)
            ->selectRaw('SUM(detail_transaction.subtotal - (products.modal * detail_transaction.qty)) as profit')
            ->value('profit') ?? 0;

        return response()->json([
            'todaySales' => $todaySales,
            'todayProfit' => $profitToday,
            'totalTransactions' => $totalItems + $totalDigitalTransactions,
            'totalItems' => $totalItems,
            'totalDigitalTransactions' => $totalDigitalTransactions,
            'monthlySales' => $fisikMonthly + $digitalMonthly,
        ]);
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
            112,114,115,119,123,124,125,127,128,129,
            251,203,204,205,206,207,208,209,210,211,
            212,213,214,215,216,217,218,219,220,221,
            222,223,224,225,226,227,228,229,230,231,
            232,233,234,235,236,237,238,239,259,113,
            116,120
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
}
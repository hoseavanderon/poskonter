<?php

namespace App\Http\Controllers;

use App\Models\DigitalTransaction;
use App\Models\Outlet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // 🔥 ambil user login (owner)
        $user = Auth::user();

        // 🔥 ambil semua outlet milik owner
        $outletIds = Outlet::where('owner_id', $user->id)
            ->pluck('id');

        // ❌ digital product yang bukan sales
        $excludedProducts = [
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

        // 🧾 fisik
        $fisik = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $today)
            ->whereIn('transactions.outlet_id', $outletIds)
            ->sum('detail_transaction.subtotal');

        // 💳 digital
        $digital = DigitalTransaction::whereDate('created_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->whereNotIn('digital_product_id', $excludedProducts)
            ->sum('subtotal');

        // 🔥 TODAY SALES
        $todaySales = $fisik + $digital;

        // 🔥 TOTAL TRANSACTIONS
        $totalTransactions = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $today)
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at')
            ->sum('detail_transaction.qty');
        +DigitalTransaction::whereDate('created_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->whereNotIn('digital_product_id', $excludedProducts)
            ->count();

        // 🔥 PER OUTLET
        $outletTransactions = Outlet::where('owner_id', $user->id)
            ->get()
            ->map(function ($outlet) use ($today, $excludedProducts) {

                // 🧾 FISIK
                $fisikCount = Transaction::whereDate('created_at', $today)
                    ->where('outlet_id', $outlet->id)
                    ->count();

                $fisikTotal = Transaction::whereDate('created_at', $today)
                    ->where('outlet_id', $outlet->id)
                    ->sum('subtotal');

                // 💳 DIGITAL
                $digitalQuery = DigitalTransaction::whereDate('created_at', $today)
                    ->where('outlet_id', $outlet->id)
                    ->whereNotIn('digital_product_id', $excludedProducts);

                $digitalCount = $digitalQuery->count();
                $digitalTotal = $digitalQuery->sum('subtotal');

                return [
                    'name' => $outlet->name,

                    'fisik_count' => $fisikCount,
                    'fisik_total' => $fisikTotal,

                    'digital_count' => $digitalCount,
                    'digital_total' => $digitalTotal,

                    'total_count' => $fisikCount + $digitalCount,
                    'total_amount' => $fisikTotal + $digitalTotal,
                ];
            });

        return view('admindashboard.index', [
            'todaySales' => $todaySales,
            'totalTransactions' => $totalTransactions,
            'outletTransactions' => $outletTransactions,
        ]);
    }
}

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

        $user = Auth::user();

        $outlets = Outlet::where('owner_id', request()->user()->id)->get();

        $selectedOutlet = request()->get('outlet', 'all');

        if ($selectedOutlet === 'all') {
            $outletIds = Outlet::where('owner_id', $user->id)->pluck('id');
        } else {
            $outletIds = [$selectedOutlet];
        }

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

        $fisik = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $today)
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at')
            ->sum('detail_transaction.subtotal');

        $totalItems = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $today)
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at')
            ->sum('detail_transaction.qty');

        $digitalQuery = DigitalTransaction::whereDate('created_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->whereNotIn('digital_product_id', $excludedProducts);

        $digital = $digitalQuery->sum('subtotal');
        $totalDigitalTransactions = $digitalQuery->count();

        $todaySales = $fisik + $digital;

        $totalTransactions = $totalItems + $totalDigitalTransactions;

        view()->share('outlets', $outlets);

        return view('admindashboard.index', [
            'todaySales' => $todaySales,
            'totalTransactions' => $totalTransactions,
            'totalItems' => $totalItems,
            'totalDigitalTransactions' => $totalDigitalTransactions,
        ]);
    }
}

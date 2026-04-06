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
        return view('admindashboard.index');
    }

    public function getData(Request $request)
    {
        $today = now()->toDateString();
        $user = $request->user();

        $selectedOutlet = $request->get('outlet', 'all');

        // 🔥 ambil outlet
        if ($selectedOutlet === 'all') {
            $outletIds = Outlet::where('owner_id', $user->id)->pluck('id');
        } else {
            $outletIds = [$selectedOutlet];
        }

        // 🔥 samakan dengan index()
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

        // 🔥 FISIK
        $baseQuery = DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereDate('transactions.created_at', $today)
            ->whereIn('transactions.outlet_id', $outletIds)
            ->whereNull('transactions.deleted_at');

        $fisik = (clone $baseQuery)->sum('detail_transaction.subtotal');
        $totalItems = (clone $baseQuery)->sum('detail_transaction.qty');

        // 🔥 DIGITAL (SAMA PERSIS DENGAN INDEX)
        $digitalQuery = DigitalTransaction::whereDate('created_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->whereNotIn('digital_product_id', $excludedProducts);

        $digital = $digitalQuery->sum('subtotal');
        $totalDigitalTransactions = $digitalQuery->count();

        return response()->json([
            'todaySales' => $fisik + $digital,
            'totalTransactions' => $totalItems + $totalDigitalTransactions,
            'totalItems' => $totalItems,
            'totalDigitalTransactions' => $totalDigitalTransactions,
        ]);
    }
}

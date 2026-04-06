<?php

namespace App\Http\Controllers;

use App\Models\DigitalTransaction;
use App\Models\Outlet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

        // 🔥 TODAY SALES (FILTER PER OUTLET)
        $todaySales =
            Transaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->sum('subtotal')
            +
            DigitalTransaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->sum('subtotal');

        // 🔥 TOTAL TRANSACTIONS
        $totalTransactions =
            Transaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->count()
            +
            DigitalTransaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->whereIn('outlet_id', $outletIds)
            ->count();

        return view('admindashboard.index', [
            'todaySales' => $todaySales,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}

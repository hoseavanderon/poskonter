<?php

namespace App\Http\Controllers;

use App\Models\DigitalTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // 🔥 TODAY SALES
        $todaySales =
            Transaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->sum('subtotal')
            +
            DigitalTransaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->sum('subtotal');

        // 🔥 TOTAL TRANSACTIONS TODAY
        $totalTransactions =
            Transaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->count()
            +
            DigitalTransaction::whereNotNull('paid_at')
            ->whereDate('paid_at', $today)
            ->count();

        return view('admindashboard.index', [
            'todaySales' => $todaySales,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\DigitalTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $outlet = Outlet::find(Auth::user()->outlet_id);

        // Ambil semua customer milik outlet
        $customers = Customer::with(['attributes' => function ($q) {
            $q->select('id', 'customer_id', 'attribute_value', 'attribute_notes');
        }])
            ->where('outlet_id', $outlet->id)
            ->get();

        /**
         * Ambil utang dari transaksi fisik
         */
        $transactionsDebt = Transaction::whereNull('paid_at')
            ->whereNotNull('customer_id')
            ->where('outlet_id', $outlet->id)
            ->with(['details.product:id,name'])
            ->get(['id', 'customer_id', 'nomor_nota', 'subtotal', 'created_at'])
            ->map(function ($t) {
                $t->type = 'physical';
                return $t;
            });

        /**
         * Ambil utang dari transaksi digital
         * Sekarang ikut dengan kategori digital
         */
        $digitalDebt = DigitalTransaction::whereNull('paid_at')
            ->whereNotNull('customer_id')
            ->where('outlet_id', $outlet->id)
            ->with(['product.category:id,name'])
            ->get(['id', 'customer_id', 'nomor_nota', 'subtotal', 'created_at'])
            ->map(function ($d) {
                $d->type = 'digital';
                return $d;
            });

        // Gabungkan semua utang ke masing-masing customer
        $customers->map(function ($customer) use ($transactionsDebt, $digitalDebt) {
            $customer->debts = collect()
                ->merge($transactionsDebt->where('customer_id', $customer->id))
                ->merge($digitalDebt->where('customer_id', $customer->id))
                ->sortByDesc('created_at')
                ->values();
            return $customer;
        });

        if (request()->wantsJson()) {
            return response()->json([
                'outlet' => $outlet,
                'customers' => $customers,
            ]);
        }

        return view('customers.index', compact('outlet', 'customers'));
    }

    public function payDebt(Request $request, $id)
    {
        if ($request->type === 'physical') {
            $debt = Transaction::find($id);
        } elseif ($request->type === 'digital') {
            $debt = DigitalTransaction::find($id);
        } else {
            return response()->json(['error' => 'Tipe transaksi tidak valid'], 400);
        }

        if (!$debt) {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        $debt->update([
            'paid_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}

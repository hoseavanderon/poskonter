<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\DigitalTransaction;
use Illuminate\Support\Facades\Auth;

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
            ->with(['details.product' => function ($q) {
                $q->select('id', 'name');
            }])
            ->get(['id', 'customer_id', 'nomor_nota', 'subtotal', 'created_at']);

        /**
         * Ambil utang dari transaksi digital
         * Sekarang ikut dengan kategori digital
         */
        $digitalDebt = DigitalTransaction::whereNull('paid_at')
            ->whereNotNull('customer_id')
            ->where('outlet_id', $outlet->id)
            ->with(['product.category' => function ($q) {
                $q->select('id', 'name'); // name dari tabel digital_categories
            }])
            ->get(['id', 'customer_id', 'nomor_nota', 'subtotal', 'created_at', 'digital_product_id']);

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

    public function payDebt($id)
    {
        $transaction = \App\Models\Transaction::find($id);
        $digital = \App\Models\DigitalTransaction::find($id);

        $debt = $transaction ?? $digital;

        if (!$debt) {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        // 🔍 Ambil customer terkait sebelum dihapus dari transaksi
        $customer = \App\Models\Customer::find($debt->customer_id);

        // 💰 Total nominal utang yang dibayar
        $nominal = $debt->subtotal ?? 0;

        // ✅ Tandai transaksi lunas
        $debt->update([
            'paid_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}

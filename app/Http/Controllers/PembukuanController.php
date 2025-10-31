<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\CashbookWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembukuanController extends Controller
{
    public function index()
    {
        $outletId = Auth::user()->outlet_id;

        // === Ambil saldo per wallet berdasarkan outlet ===
        $wallets = CashbookWallet::select('id', 'cashbook_wallet as name')
            ->where('outlet_id', $outletId)
            ->get()
            ->map(function ($wallet) use ($outletId) {
                $wallet->balance = Cashbook::where('cashbook_wallet_id', $wallet->id)
                    ->where('outlet_id', $outletId)
                    ->selectRaw('
                        SUM(CASE 
                            WHEN type = "IN" THEN CAST(nominal AS SIGNED)
                            WHEN type = "OUT" THEN -CAST(nominal AS SIGNED)
                            ELSE 0 END
                        ) as saldo
                    ')
                    ->value('saldo') ?? 0;

                $wallet->type = 'Dompet';
                $wallet->note = $wallet->balance == 0 ? 'Belum ada transaksi' : 'Aktif';
                return $wallet;
            });

        // === Hitung total seluruh wallet outlet ===
        $totalBalance = Cashbook::where('outlet_id', $outletId)
            ->selectRaw('
                SUM(CASE 
                    WHEN type = "IN" THEN CAST(nominal AS SIGNED)
                    WHEN type = "OUT" THEN -CAST(nominal AS SIGNED)
                    ELSE 0 END
                ) as total_balance
            ')
            ->value('total_balance') ?? 0;

        // Tambahkan “Semua Wallet” di atas
        $wallets->prepend((object) [
            'id' => 0,
            'name' => 'Semua Wallet',
            'balance' => $totalBalance,
            'type' => 'Semua',
            'note' => 'Gabungan seluruh wallet',
        ]);

        // === Ambil tahun unik dari transaksi outlet ini ===
        $years = Cashbook::where('outlet_id', $outletId)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // === Ambil semua transaksi outlet ini ===
        $transactions = Cashbook::where('outlet_id', $outletId)
            ->select('id', 'cashbook_wallet_id', 'deskripsi', 'type', 'nominal', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'cashbook_wallet_id' => $t->cashbook_wallet_id,
                'deskripsi' => $t->deskripsi,
                'type' => $t->type,
                'nominal' => (float) $t->nominal,
                'created_at' => $t->created_at->format('Y-m-d H:i:s'),
            ]);

        // === Total saldo outlet
        $totalSaldo = Cashbook::where('outlet_id', $outletId)
            ->selectRaw("
                SUM(CASE 
                    WHEN type = 'IN' THEN CAST(nominal AS SIGNED) 
                    ELSE -CAST(nominal AS SIGNED) 
                END) AS total
            ")
            ->value('total') ?? 0;

        // === Tanggal terakhir update
        $lastUpdate = Cashbook::where('outlet_id', $outletId)
            ->latest('updated_at')
            ->first()?->updated_at;

        return view('pembukuan.index', compact(
            'wallets',
            'transactions',
            'years',
            'totalSaldo',
            'lastUpdate'
        ));
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $validated = validator($data, [
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'cashbook_wallet_id' => 'required|exists:cashbook_wallets,id',
            'type' => 'required|in:IN,OUT',
        ])->validate();

        $validated['cashbook_category_id'] = 2;
        $validated['outlet_id'] = Auth::user()->outlet_id;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $cashbook = Cashbook::create($validated);

        return response()->json($cashbook, 201);
    }

    public function destroy($id)
    {
        $outletId = Auth::user()->outlet_id;

        $cashbook = Cashbook::where('id', $id)
            ->where('outlet_id', $outletId)
            ->firstOrFail();

        $cashbook->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

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

                $wallet->balance = (int) $wallet->balance;
                $wallet->type = 'Dompet';
                $wallet->note = $wallet->balance == 0 ? 'Belum ada transaksi' : 'Aktif';
                return $wallet;
            });

        $walletIds = $wallets->pluck('id');

        // Hanya transaksi yang wallet-nya milik outlet ini.
        // Baris lama dari tutup buku / barang masuk sering tersimpan di wallet id 1
        // (akun pertama) sambil membawa outlet_id akun lain, jadi tidak boleh ikut dijumlahkan.
        $ledger = Cashbook::query()
            ->where('outlet_id', $outletId)
            ->whereIn('cashbook_wallet_id', $walletIds);

        $totalBalance = (int) $wallets->sum('balance');

        // Tambahkan “Semua Wallet” di atas
        $wallets->prepend((object) [
            'id' => 0,
            'name' => 'Semua Wallet',
            'balance' => $totalBalance,
            'type' => 'Semua',
            'note' => 'Gabungan seluruh wallet',
        ]);

        // === Ambil tahun unik dari transaksi outlet ini ===
        $years = (clone $ledger)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // === Ambil semua transaksi outlet ini ===
        $transactions = (clone $ledger)
            ->select('id', 'cashbook_wallet_id', 'deskripsi', 'type', 'nominal', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'cashbook_wallet_id' => $t->cashbook_wallet_id,
                'deskripsi' => $t->deskripsi,
                'type' => $t->type,
                'nominal' => (float) $t->nominal,
                'created_at' => $t->created_at->format('Y-m-d H:i:s'),
            ]);

        $totalSaldo = $totalBalance;

        // === Tanggal terakhir update
        $lastUpdate = (clone $ledger)
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
        $outletId = Auth::user()->outlet_id;

        $validated = validator($data, [
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'cashbook_wallet_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($outletId) {
                    $belongsToOutlet = CashbookWallet::where('id', $value)
                        ->where('outlet_id', $outletId)
                        ->exists();

                    if (!$belongsToOutlet) {
                        $fail('Wallet tidak termasuk outlet ini.');
                    }
                },
            ],
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

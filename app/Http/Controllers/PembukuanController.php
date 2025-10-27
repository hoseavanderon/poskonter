<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\CashbookWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PembukuanController extends Controller
{
    public function index()
    {
        // === Ambil saldo per wallet ===
        $wallets = CashbookWallet::query()
            ->select('id', 'cashbook_wallet as name')
            ->get()
            ->map(function ($wallet) {
                $wallet->balance = Cashbook::where('cashbook_wallet_id', $wallet->id)
                    ->selectRaw('
                        SUM(CASE WHEN type = "IN" THEN CAST(nominal AS SIGNED)
                                WHEN type = "OUT" THEN -CAST(nominal AS SIGNED)
                                ELSE 0 END) as saldo
                    ')
                    ->value('saldo') ?? 0;

                $wallet->type = 'Dompet';
                $wallet->note = $wallet->balance == 0 ? 'Belum ada transaksi' : 'Aktif';
                return $wallet;
            });

        $totalBalance = Cashbook::selectRaw('
            SUM(CASE WHEN type = "IN" THEN CAST(nominal AS SIGNED)
                    WHEN type = "OUT" THEN -CAST(nominal AS SIGNED)
                    ELSE 0 END) as total_balance
        ')->value('total_balance') ?? 0;

        $wallets->prepend((object)[
            'id' => 0,
            'name' => 'Semua Wallet',
            'balance' => $totalBalance,
            'type' => 'Semua',
            'note' => 'Gabungan seluruh wallet',
        ]);

        // === Ambil tahun unik dari transaksi
        $years = Cashbook::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $transactions = Cashbook::select('id', 'cashbook_wallet_id', 'deskripsi', 'type', 'nominal', 'created_at')
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

        // === Hitung total saldo semua wallet
        $totalSaldo = Cashbook::selectRaw("
                SUM(CASE WHEN type = 'IN' THEN CAST(nominal AS SIGNED) ELSE -CAST(nominal AS SIGNED) END) AS total
            ")->value('total') ?? 0;

        // === Tanggal terakhir update
        $lastUpdate = Cashbook::latest('updated_at')->first()?->updated_at;

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
        $validated = $request->validate([
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'cashbook_wallet_id' => 'required|exists:cashbook_wallets,id',
            'type' => 'required|in:IN,OUT',
        ]);

        $validated['outlet_id'] = 4; // sesuaikan outlet aktif kamu
        $validated['cashbook_category_id'] = 1; // sementara default
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $cashbook = Cashbook::create($validated);

        return response()->json($cashbook); // 🆕 kirim data balik ke JS
    }

    public function destroy($id)
    {
        $cashbook = Cashbook::findOrFail($id);
        $cashbook->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isOwner()) {
            return redirect()->route('pos');
        }

        return view('admindashboard.index');
    }

    public function getData(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isOwner()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $period = $request->get('period', 'day');
        if (! in_array($period, ['day', 'week', 'month', 'year'], true)) {
            $period = 'day';
        }

        $outletIds = $this->resolveOutletIds($request, $user->id);
        if ($outletIds->isEmpty()) {
            return response()->json($this->blankPayload($period));
        }

        [$start, $end] = $this->getDateRange($period);
        [$prevStart, $prevEnd] = $this->getPreviousRange($period);

        return response()->json(
            $this->buildPayload($outletIds->all(), $start->copy(), $end->copy(), $prevStart->copy(), $prevEnd->copy(), $period)
        );
    }

    private function buildPayload(array $ids, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd, string $period): array
    {
        $outlets = Outlet::whereIn('id', $ids)->orderBy('name')->get(['id', 'name']);
        $periodStats = $this->periodStatsByOutlet($ids, $start, $end);
        $previousSales = $this->salesTotalByOutlet($ids, $prevStart, $prevEnd);
        $monthSales = $this->salesTotalByOutlet($ids, now()->copy()->startOfMonth(), now()->copy()->endOfMonth());
        $trends = $this->trendByOutlet($ids);

        $barangSales = 0.0;
        $digitalSales = 0.0;
        $profit = 0.0;
        $transactions = 0;
        $items = 0;
        $outletCards = [];

        foreach ($outlets as $outlet) {
            $id = (int) $outlet->id;
            $row = $periodStats[$id] ?? $this->emptyOutletStat();
            $current = (float) $row['sales'];
            $previous = (float) ($previousSales[$id] ?? 0);
            $barangSales += (float) $row['barang'];
            $digitalSales += (float) $row['digital'];
            $profit += (float) $row['profit'];
            $transactions += (int) $row['trx'];
            $items += (int) $row['qty'];

            $outletCards[] = [
                'id' => $id,
                'name' => $outlet->name,
                'sales' => round($current, 2),
                'transactions' => (int) $row['trx'],
                'monthSales' => round((float) ($monthSales[$id] ?? 0), 2),
                'profit' => round((float) $row['profit'], 2),
                'growth' => $this->growth($current, $previous),
                'trend' => $this->trendBars($trends[$id] ?? []),
            ];
        }

        $sales = $barangSales + $digitalSales;
        $prevTotal = array_sum($previousSales);
        $monthTotal = array_sum($monthSales);
        $growth = $this->growth($sales, $prevTotal);

        $yearStart = now()->copy()->startOfYear();
        $yearEnd = now()->copy()->endOfYear();
        $dailySales = $this->dailySales($ids, $yearStart, $yearEnd);
        $dailyProfit = $this->dailyProfit($ids, $yearStart, $yearEnd);
        $hourly = $this->hourlyToday($ids);

        $lowStock = $this->lowStock($ids);
        $bestSellers = $this->bestSellers($ids, $start, $end);
        $bestDigital = $this->bestDigital($ids, $start, $end);
        $categorySales = $this->categorySales($ids, $start, $end);
        $stockCategories = $this->stockCategories($ids);
        $stockValue = $this->stockValue($ids);
        $wallets = $this->wallets($ids);
        $walletTotal = round(collect($wallets)->sum('value'), 2);
        $digitalApps = $this->digitalApps($ids, $start, $end);
        $debt = $this->outstandingDebt($ids);
        $transfer = $this->movementTotal($ids, $this->transferProductIds(), $start, $end);
        $tarik = $this->movementTotal($ids, $this->tarikProductIds(), $start, $end);
        $activity = $this->activity($ids);
        $assetName = $outlets->count() === 1 ? $outlets->first()->name : 'Semua outlet';
        $assetTotal = round((float) $stockValue['modal'] + $walletTotal, 2);

        $charts = [
            'year' => $this->yearChart($dailySales),
            'month' => $this->monthChart($dailySales),
            'week' => $this->weekChart($dailySales),
        ];
        $profitSeries = [
            'today' => $this->profitBucket($hourly, 'profit', (float) ($dailyProfit[now()->toDateString()] ?? 0), $dailySales),
            'week' => $this->rangeProfit('week', $dailySales, $dailyProfit),
            'month' => $this->rangeProfit('month', $dailySales, $dailyProfit),
            'year' => $this->rangeProfit('year', $dailySales, $dailyProfit),
        ];

        $dashboard = [
            'sales' => round($sales, 2),
            'profit' => round($profit, 2),
            'transactions' => $transactions,
            'items' => $items,
            'digitalSales' => round($digitalSales, 2),
            'barangSales' => round($barangSales, 2),
            'monthSales' => round($monthTotal, 2),
            'growth' => $growth,
            'transfer' => round($transfer, 2),
            'tarik' => round($tarik, 2),
            'debt' => round($debt, 2),
            'periodLabel' => $this->periodLabel($period),
            'compareLabel' => $this->compareLabel($period),
            'outlets' => $outletCards,
            'activity' => array_slice($activity, 0, 6),
            'lowStock' => $lowStock,
            'bestSellers' => $bestSellers,
            'bestDigital' => $bestDigital,
            'stockCategories' => $stockCategories,
            'assetTotal' => $assetTotal,
            'stockSell' => round((float) $stockValue['sell'], 2),
            'stockModal' => round((float) $stockValue['modal'], 2),
            'walletTotal' => $walletTotal,
            'charts' => $charts,
            'busy' => [
                'today' => $this->busyFromBars($hourly, 'trx'),
                'month' => $this->busyWeekday($dailySales),
                'year' => $this->busyYear($dailySales),
            ],
            'profitSeries' => $profitSeries,
            'insights' => $this->insights($growth, $period, $categorySales, $lowStock, $debt, $outletCards, $digitalApps),
            'categorySales' => $categorySales,
            'digitalApps' => $digitalApps,
            'recent' => array_slice($activity, 0, 12),
            'assets' => $this->assetItems($stockValue, $wallets),
            'assetName' => $assetName,
        ];

        return [
            'todaySales' => $dashboard['sales'],
            'todayProfit' => $dashboard['profit'],
            'totalTransactions' => $dashboard['transactions'],
            'totalItems' => $dashboard['items'],
            'monthlySales' => $dashboard['monthSales'],
            'growth' => $dashboard['growth'],
            'dashboard' => $dashboard,
        ];
    }

    private function resolveOutletIds(Request $request, int $userId): Collection
    {
        $owned = Outlet::where('owner_id', $userId)->pluck('id')->map(fn ($id) => (int) $id)->values();
        $selected = (string) $request->get('outlet', 'all');

        if ($selected === '' || $selected === 'all') {
            return $owned;
        }

        $id = (int) $selected;
        if (! $owned->contains($id)) {
            abort(403, 'Outlet tidak termasuk milik Anda.');
        }

        return collect([$id]);
    }

    private function periodStatsByOutlet(array $ids, Carbon $start, Carbon $end): array
    {
        $map = [];
        foreach ($ids as $id) {
            $map[(int) $id] = $this->emptyOutletStat();
        }

        $fisik = $this->fisikBase($ids)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw('transactions.outlet_id as outlet_id, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as total, COUNT(DISTINCT transactions.id) as trx, SUM(detail_transaction.qty) as qty')
            ->groupBy('transactions.outlet_id')
            ->get();

        foreach ($fisik as $row) {
            $id = (int) $row->outlet_id;
            $map[$id]['barang'] += (float) $row->total;
            $map[$id]['sales'] += (float) $row->total;
            $map[$id]['trx'] += (int) $row->trx;
            $map[$id]['qty'] += (int) $row->qty;
        }

        $profit = $this->fisikBase($ids)
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw('transactions.outlet_id as outlet_id, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2)) - (CAST(COALESCE(products.modal, 0) AS DECIMAL(15,2)) * detail_transaction.qty)) as profit')
            ->groupBy('transactions.outlet_id')
            ->get();

        foreach ($profit as $row) {
            $map[(int) $row->outlet_id]['profit'] += (float) $row->profit;
        }

        $digital = DB::table('digital_transactions')
            ->leftJoin('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->whereIn('digital_transactions.outlet_id', $ids)
            ->whereNull('digital_transactions.deleted_at')
            ->whereNotIn('digital_transactions.digital_product_id', $this->movementProductIds())
            ->whereBetween('digital_transactions.created_at', [$start, $end])
            ->selectRaw("
                digital_transactions.outlet_id as outlet_id,
                SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2))) as total,
                COUNT(*) as trx,
                SUM(CASE
                    WHEN CAST(COALESCE(digital_products.base_price, '0') AS DECIMAL(15,2)) > 0
                    THEN CAST(digital_transactions.subtotal AS DECIMAL(15,2)) - CAST(digital_products.base_price AS DECIMAL(15,2))
                    ELSE 0
                END) as profit
            ")
            ->groupBy('digital_transactions.outlet_id')
            ->get();

        foreach ($digital as $row) {
            $id = (int) $row->outlet_id;
            $map[$id]['digital'] += (float) $row->total;
            $map[$id]['sales'] += (float) $row->total;
            $map[$id]['trx'] += (int) $row->trx;
            $map[$id]['profit'] += (float) $row->profit;
        }

        return $map;
    }

    private function salesTotalByOutlet(array $ids, Carbon $start, Carbon $end): array
    {
        $map = [];
        foreach ($ids as $id) {
            $map[(int) $id] = 0.0;
        }

        $fisik = $this->fisikBase($ids)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw('transactions.outlet_id as outlet_id, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as total')
            ->groupBy('transactions.outlet_id')
            ->get();

        foreach ($fisik as $row) {
            $map[(int) $row->outlet_id] += (float) $row->total;
        }

        $digital = $this->digitalBase($ids)
            ->whereBetween('digital_transactions.created_at', [$start, $end])
            ->selectRaw('digital_transactions.outlet_id as outlet_id, SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2))) as total')
            ->groupBy('digital_transactions.outlet_id')
            ->get();

        foreach ($digital as $row) {
            $map[(int) $row->outlet_id] += (float) $row->total;
        }

        return $map;
    }

    private function trendByOutlet(array $ids): array
    {
        $start = now()->copy()->subDays(6)->startOfDay();
        $end = now()->copy()->endOfDay();
        $map = [];

        $fisik = $this->fisikBase($ids)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw("transactions.outlet_id as outlet_id, DATE_FORMAT(transactions.created_at, '%Y-%m-%d') as bucket, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as total")
            ->groupBy('transactions.outlet_id', DB::raw("DATE_FORMAT(transactions.created_at, '%Y-%m-%d')"))
            ->get();

        foreach ($fisik as $row) {
            $map[(int) $row->outlet_id][$row->bucket] = (float) $row->total;
        }

        $digital = $this->digitalBase($ids)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("outlet_id, DATE_FORMAT(created_at, '%Y-%m-%d') as bucket, SUM(CAST(subtotal AS DECIMAL(15,2))) as total")
            ->groupBy('outlet_id', DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get();

        foreach ($digital as $row) {
            $id = (int) $row->outlet_id;
            $map[$id][$row->bucket] = ($map[$id][$row->bucket] ?? 0) + (float) $row->total;
        }

        return $map;
    }

    private function trendBars(array $byDate): array
    {
        $bars = [];
        $cursor = now()->copy()->subDays(6)->startOfDay();
        for ($i = 0; $i < 7; $i++) {
            $key = $cursor->format('Y-m-d');
            $bars[] = [
                'label' => $this->dayShort($cursor),
                'value' => round((float) ($byDate[$key] ?? 0), 2),
            ];
            $cursor->addDay();
        }

        return $bars;
    }

    private function dailySales(array $ids, Carbon $start, Carbon $end): array
    {
        $map = [];

        $fisik = $this->fisikBase($ids)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(transactions.created_at, '%Y-%m-%d') as bucket, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as total, COUNT(DISTINCT transactions.id) as trx")
            ->groupBy(DB::raw("DATE_FORMAT(transactions.created_at, '%Y-%m-%d')"))
            ->get();

        foreach ($fisik as $row) {
            $map[$row->bucket] = ['sales' => (float) $row->total, 'trx' => (int) $row->trx];
        }

        $digital = $this->digitalBase($ids)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as bucket, SUM(CAST(subtotal AS DECIMAL(15,2))) as total, COUNT(*) as trx")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get();

        foreach ($digital as $row) {
            if (! isset($map[$row->bucket])) {
                $map[$row->bucket] = ['sales' => 0, 'trx' => 0];
            }
            $map[$row->bucket]['sales'] += (float) $row->total;
            $map[$row->bucket]['trx'] += (int) $row->trx;
        }

        return $map;
    }

    private function dailyProfit(array $ids, Carbon $start, Carbon $end): array
    {
        $map = [];

        $rows = $this->fisikBase($ids)
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(transactions.created_at, '%Y-%m-%d') as bucket, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2)) - (CAST(COALESCE(products.modal, 0) AS DECIMAL(15,2)) * detail_transaction.qty)) as profit")
            ->groupBy(DB::raw("DATE_FORMAT(transactions.created_at, '%Y-%m-%d')"))
            ->get();

        foreach ($rows as $row) {
            $map[$row->bucket] = (float) $row->profit;
        }

        $digital = DB::table('digital_transactions')
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->whereIn('digital_transactions.outlet_id', $ids)
            ->whereNull('digital_transactions.deleted_at')
            ->whereNotIn('digital_transactions.digital_product_id', $this->movementProductIds())
            ->whereBetween('digital_transactions.created_at', [$start, $end])
            ->whereRaw("CAST(COALESCE(digital_products.base_price, '0') AS DECIMAL(15,2)) > 0")
            ->selectRaw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d') as bucket, SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2)) - CAST(digital_products.base_price AS DECIMAL(15,2))) as profit")
            ->groupBy(DB::raw("DATE_FORMAT(digital_transactions.created_at, '%Y-%m-%d')"))
            ->get();

        foreach ($digital as $row) {
            $map[$row->bucket] = ($map[$row->bucket] ?? 0) + (float) $row->profit;
        }

        return $map;
    }

    private function hourlyToday(array $ids): array
    {
        $start = now()->copy()->startOfDay();
        $end = now()->copy()->endOfDay();
        $map = [];
        foreach (range(7, 22) as $hour) {
            $map[$hour] = ['sales' => 0.0, 'trx' => 0, 'profit' => 0.0];
        }

        $fisik = $this->fisikBase($ids)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw('HOUR(transactions.created_at) as hour, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as total, COUNT(DISTINCT transactions.id) as trx')
            ->groupBy(DB::raw('HOUR(transactions.created_at)'))
            ->get();

        foreach ($fisik as $row) {
            $hour = (int) $row->hour;
            if (! isset($map[$hour])) {
                continue;
            }
            $map[$hour]['sales'] += (float) $row->total;
            $map[$hour]['trx'] += (int) $row->trx;
        }

        $digital = $this->digitalBase($ids)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('HOUR(created_at) as hour, SUM(CAST(subtotal AS DECIMAL(15,2))) as total, COUNT(*) as trx')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->get();

        foreach ($digital as $row) {
            $hour = (int) $row->hour;
            if (! isset($map[$hour])) {
                continue;
            }
            $map[$hour]['sales'] += (float) $row->total;
            $map[$hour]['trx'] += (int) $row->trx;
        }

        $profit = $this->fisikBase($ids)
            ->join('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw('HOUR(transactions.created_at) as hour, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2)) - (CAST(COALESCE(products.modal, 0) AS DECIMAL(15,2)) * detail_transaction.qty)) as profit')
            ->groupBy(DB::raw('HOUR(transactions.created_at)'))
            ->get();

        foreach ($profit as $row) {
            $hour = (int) $row->hour;
            if (isset($map[$hour])) {
                $map[$hour]['profit'] += (float) $row->profit;
            }
        }

        return $map;
    }

    private function lowStock(array $ids): array
    {
        return DB::table('products')
            ->join('product_attribute_values as pav', 'pav.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('outlets', 'outlets.id', '=', 'products.outlet_id')
            ->whereIn('products.outlet_id', $ids)
            ->groupBy('products.id', 'products.name', 'categories.name', 'products.minimal_stok', 'outlets.name')
            ->havingRaw('SUM(pav.stok) <= products.minimal_stok')
            ->orderByRaw('SUM(pav.stok) asc')
            ->limit(20)
            ->selectRaw('products.id, products.name, categories.name as category, outlets.name as outlet, SUM(pav.stok) as stock, products.minimal_stok as minimum')
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'category' => $row->category,
                'outlet' => $row->outlet,
                'stock' => (int) $row->stock,
                'minimum' => (int) $row->minimum,
            ])
            ->all();
    }

    private function bestSellers(array $ids, Carbon $start, Carbon $end): array
    {
        return $this->fisikBase($ids)
            ->leftJoin('products', 'products.id', '=', 'detail_transaction.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw("COALESCE(products.name, detail_transaction.manual_name, 'Item') as name, COALESCE(categories.name, 'Jasa') as category, SUM(detail_transaction.qty) as qty, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as amount")
            ->groupBy(DB::raw("COALESCE(products.name, detail_transaction.manual_name, 'Item')"), DB::raw("COALESCE(categories.name, 'Jasa')"))
            ->orderByRaw('SUM(detail_transaction.qty) desc')
            ->limit(12)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'category' => $row->category,
                'qty' => (int) $row->qty,
                'amount' => round((float) $row->amount, 2),
            ])
            ->all();
    }

    private function bestDigital(array $ids, Carbon $start, Carbon $end): array
    {
        return $this->digitalBase($ids)
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->leftJoin('digital_categories', 'digital_categories.id', '=', 'digital_products.digital_category_id')
            ->whereBetween('digital_transactions.created_at', [$start, $end])
            ->selectRaw("digital_products.name as name, COALESCE(digital_categories.name, 'Digital') as category, COUNT(*) as qty, SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2))) as amount")
            ->groupBy('digital_products.id', 'digital_products.name', 'digital_categories.name')
            ->orderByRaw('SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2))) desc')
            ->limit(12)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'category' => $row->category,
                'qty' => (int) $row->qty,
                'amount' => round((float) $row->amount, 2),
            ])
            ->all();
    }

    private function categorySales(array $ids, Carbon $start, Carbon $end): array
    {
        return $this->fisikBase($ids)
            ->leftJoin('products', 'products.id', '=', 'detail_transaction.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw("COALESCE(categories.name, 'Jasa') as name, SUM(detail_transaction.qty) as qty, SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) as amount")
            ->groupBy(DB::raw("COALESCE(categories.name, 'Jasa')"))
            ->orderByRaw('SUM(CAST(detail_transaction.subtotal AS DECIMAL(15,2))) desc')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'value' => (int) $row->qty,
                'amount' => round((float) $row->amount, 2),
            ])
            ->all();
    }

    private function stockCategories(array $ids): array
    {
        return DB::table('product_attribute_values as pav')
            ->join('products', 'products.id', '=', 'pav.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereIn('pav.outlet_id', $ids)
            ->groupBy('categories.id', 'categories.name')
            ->orderByRaw('SUM(pav.stok) desc')
            ->selectRaw('categories.name as name, SUM(pav.stok) as pcs')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'pcs' => (int) $row->pcs,
            ])
            ->all();
    }

    private function stockValue(array $ids): array
    {
        $row = DB::table('product_attribute_values as pav')
            ->join('products', 'products.id', '=', 'pav.product_id')
            ->whereIn('pav.outlet_id', $ids)
            ->selectRaw('SUM(pav.stok * CAST(COALESCE(products.jual, 0) AS DECIMAL(15,2))) as sell, SUM(pav.stok * CAST(COALESCE(products.modal, 0) AS DECIMAL(15,2))) as modal')
            ->first();

        return [
            'sell' => (float) ($row->sell ?? 0),
            'modal' => (float) ($row->modal ?? 0),
        ];
    }

    private function wallets(array $ids): array
    {
        return DB::table('cashbooks')
            ->join('cashbook_wallets', 'cashbook_wallets.id', '=', 'cashbooks.cashbook_wallet_id')
            ->whereIn('cashbooks.outlet_id', $ids)
            ->whereNull('cashbook_wallets.deleted_at')
            ->groupBy('cashbook_wallets.id', 'cashbook_wallets.cashbook_wallet')
            ->orderBy('cashbook_wallets.cashbook_wallet')
            ->selectRaw("cashbook_wallets.cashbook_wallet as name, SUM(CASE WHEN cashbooks.type = 'IN' THEN cashbooks.nominal ELSE -cashbooks.nominal END) as balance")
            ->get()
            ->map(fn ($row) => [
                'label' => $row->name,
                'value' => round((float) $row->balance, 2),
            ])
            ->all();
    }

    private function digitalApps(array $ids, Carbon $start, Carbon $end): array
    {
        return $this->digitalBase($ids)
            ->join('apps', 'apps.id', '=', 'digital_transactions.app_id')
            ->whereBetween('digital_transactions.created_at', [$start, $end])
            ->selectRaw('apps.name as name, SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2))) as total, COUNT(*) as trx')
            ->groupBy('apps.id', 'apps.name')
            ->orderByRaw('SUM(CAST(digital_transactions.subtotal AS DECIMAL(15,2))) desc')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'total' => round((float) $row->total, 2),
                'trx' => (int) $row->trx,
            ])
            ->all();
    }

    private function outstandingDebt(array $ids): float
    {
        $fisik = (float) DB::table('transactions')
            ->whereIn('outlet_id', $ids)
            ->whereNotNull('customer_id')
            ->whereNull('paid_at')
            ->whereNull('deleted_at')
            ->selectRaw('COALESCE(SUM(CAST(subtotal AS DECIMAL(15,2))), 0) as total')
            ->value('total');

        $digital = (float) DB::table('digital_transactions')
            ->whereIn('outlet_id', $ids)
            ->whereNotNull('customer_id')
            ->whereNull('paid_at')
            ->whereNull('deleted_at')
            ->selectRaw('COALESCE(SUM(CAST(subtotal AS DECIMAL(15,2))), 0) as total')
            ->value('total');

        return $fisik + $digital;
    }

    private function movementTotal(array $ids, array $productIds, Carbon $start, Carbon $end): float
    {
        if ($productIds === []) {
            return 0;
        }

        return (float) DB::table('digital_transactions')
            ->whereIn('outlet_id', $ids)
            ->whereIn('digital_product_id', $productIds)
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(CAST(subtotal AS DECIMAL(15,2))), 0) as total')
            ->value('total');
    }

    private function activity(array $ids): array
    {
        $items = [];

        $sales = DB::table('transactions')
            ->join('outlets', 'outlets.id', '=', 'transactions.outlet_id')
            ->leftJoin('detail_transaction', function ($join) {
                $join->on('detail_transaction.transaction_id', '=', 'transactions.id')
                    ->whereNull('detail_transaction.deleted_at');
            })
            ->leftJoin('products', 'products.id', '=', 'detail_transaction.product_id')
            ->whereIn('transactions.outlet_id', $ids)
            ->whereNull('transactions.deleted_at')
            ->groupBy('transactions.id', 'transactions.subtotal', 'transactions.created_at', 'outlets.name')
            ->orderByDesc('transactions.created_at')
            ->limit(10)
            ->selectRaw("transactions.subtotal as amount, transactions.created_at, outlets.name as outlet, SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(products.name, detail_transaction.manual_name) ORDER BY detail_transaction.id SEPARATOR ' · '), ' · ', 2) as label")
            ->get();

        foreach ($sales as $row) {
            $items[] = [
                'type' => 'sale',
                'name' => $row->label ?: 'Transaksi barang',
                'outlet' => $row->outlet,
                'amount' => round((float) $row->amount, 2),
                'time' => Carbon::parse($row->created_at)->locale('id')->diffForHumans(),
                'at' => Carbon::parse($row->created_at)->timestamp,
            ];
        }

        $digital = DB::table('digital_transactions')
            ->join('outlets', 'outlets.id', '=', 'digital_transactions.outlet_id')
            ->join('digital_products', 'digital_products.id', '=', 'digital_transactions.digital_product_id')
            ->whereIn('digital_transactions.outlet_id', $ids)
            ->whereNull('digital_transactions.deleted_at')
            ->orderByDesc('digital_transactions.created_at')
            ->limit(10)
            ->get([
                'digital_products.name as label',
                'digital_transactions.subtotal as amount',
                'digital_transactions.created_at',
                'outlets.name as outlet',
            ]);

        foreach ($digital as $row) {
            $items[] = [
                'type' => 'digital',
                'name' => $row->label,
                'outlet' => $row->outlet,
                'amount' => round((float) $row->amount, 2),
                'time' => Carbon::parse($row->created_at)->locale('id')->diffForHumans(),
                'at' => Carbon::parse($row->created_at)->timestamp,
            ];
        }

        $restock = DB::table('inventory_histories')
            ->join('products', 'products.id', '=', 'inventory_histories.product_id')
            ->join('outlets', 'outlets.id', '=', 'inventory_histories.outlet_id')
            ->whereIn('inventory_histories.outlet_id', $ids)
            ->where('inventory_histories.type', 'IN')
            ->orderByDesc('inventory_histories.created_at')
            ->limit(8)
            ->get([
                'products.name as label',
                'inventory_histories.pcs',
                'inventory_histories.created_at',
                'outlets.name as outlet',
            ]);

        foreach ($restock as $row) {
            $items[] = [
                'type' => 'restock',
                'name' => 'Restok '.$row->label.' ×'.$row->pcs,
                'outlet' => $row->outlet,
                'amount' => null,
                'time' => Carbon::parse($row->created_at)->locale('id')->diffForHumans(),
                'at' => Carbon::parse($row->created_at)->timestamp,
            ];
        }

        usort($items, fn ($a, $b) => $b['at'] <=> $a['at']);

        return array_map(function ($item) {
            unset($item['at']);

            return $item;
        }, $items);
    }

    private function yearChart(array $dailySales): array
    {
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $bars = [];
        $revenue = 0;
        $transactions = 0;
        $year = now()->year;

        foreach ($labels as $index => $label) {
            $prefix = sprintf('%04d-%02d', $year, $index + 1);
            $value = 0;
            $trx = 0;
            foreach ($dailySales as $date => $row) {
                if (str_starts_with($date, $prefix)) {
                    $value += $row['sales'];
                    $trx += $row['trx'];
                }
            }
            $revenue += $value;
            $transactions += $trx;
            $bars[] = ['label' => $label, 'value' => round($value, 2)];
        }

        return [
            'label' => (string) $year,
            'transactions' => $transactions,
            'revenue' => round($revenue, 2),
            'bars' => $bars,
        ];
    }

    private function monthChart(array $dailySales): array
    {
        $bars = [];
        $revenue = 0;
        $transactions = 0;
        $cursor = now()->copy()->startOfMonth();
        $end = now()->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $week = (int) ceil($cursor->day / 7);
            if (! isset($bars[$week])) {
                $bars[$week] = ['label' => 'M'.$week, 'value' => 0];
            }
            $row = $dailySales[$cursor->format('Y-m-d')] ?? null;
            if ($row) {
                $bars[$week]['value'] += $row['sales'];
                $revenue += $row['sales'];
                $transactions += $row['trx'];
            }
            $cursor->addDay();
        }

        $names = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return [
            'label' => $names[now()->month].' '.now()->year,
            'transactions' => $transactions,
            'revenue' => round($revenue, 2),
            'bars' => array_map(function ($bar) {
                $bar['value'] = round($bar['value'], 2);

                return $bar;
            }, array_values($bars)),
        ];
    }

    private function weekChart(array $dailySales): array
    {
        $bars = [];
        $revenue = 0;
        $transactions = 0;
        $cursor = now()->copy()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $key = $cursor->format('Y-m-d');
            $row = $dailySales[$key] ?? ['sales' => 0, 'trx' => 0];
            $revenue += $row['sales'];
            $transactions += $row['trx'];
            $bars[] = [
                'label' => $this->dayShort($cursor),
                'value' => round((float) $row['sales'], 2),
            ];
            $cursor->addDay();
        }

        return [
            'label' => 'Minggu ini',
            'transactions' => $transactions,
            'revenue' => round($revenue, 2),
            'bars' => $bars,
        ];
    }

    private function profitBucket(array $hourly, string $field, float $total, array $dailySales): array
    {
        $bars = [];
        foreach ($hourly as $hour => $row) {
            $bars[] = [
                'label' => sprintf('%02d', $hour),
                'value' => round((float) $row[$field], 2),
            ];
        }

        $today = $dailySales[now()->toDateString()] ?? ['sales' => 0, 'trx' => 0];
        $trx = (int) $today['trx'];

        return [
            'total' => round($total, 2),
            'transactions' => $trx,
            'avg' => $trx > 0 ? round($today['sales'] / $trx, 0) : 0,
            'bars' => $bars,
        ];
    }

    private function rangeProfit(string $range, array $dailySales, array $dailyProfit): array
    {
        [$start, $end] = match ($range) {
            'week' => [now()->copy()->startOfWeek(), now()->copy()->endOfWeek()],
            'year' => [now()->copy()->startOfYear(), now()->copy()->endOfYear()],
            default => [now()->copy()->startOfMonth(), now()->copy()->endOfMonth()],
        };

        $bars = [];
        $profit = 0;
        $sales = 0;
        $trx = 0;

        if ($range === 'year') {
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            foreach ($labels as $index => $label) {
                $prefix = sprintf('%04d-%02d', now()->year, $index + 1);
                $value = 0;
                foreach ($dailyProfit as $date => $amount) {
                    if (str_starts_with($date, $prefix)) {
                        $value += $amount;
                    }
                }
                $profit += $value;
                $bars[] = ['label' => $label, 'value' => round($value, 2)];
            }
            foreach ($dailySales as $date => $row) {
                if (str_starts_with($date, now()->year.'-')) {
                    $sales += $row['sales'];
                    $trx += $row['trx'];
                }
            }
        } else {
            $cursor = $start->copy()->startOfDay();
            $last = $end->copy()->startOfDay();
            $limit = $range === 'month' ? now()->copy()->startOfDay() : $last;
            if ($limit->lt($last)) {
                $last = $limit;
            }
            while ($cursor->lte($last)) {
                $key = $cursor->format('Y-m-d');
                $value = (float) ($dailyProfit[$key] ?? 0);
                $row = $dailySales[$key] ?? ['sales' => 0, 'trx' => 0];
                $profit += $value;
                $sales += $row['sales'];
                $trx += $row['trx'];
                $bars[] = [
                    'label' => $range === 'week' ? $this->dayShort($cursor) : $cursor->format('d'),
                    'value' => round($value, 2),
                ];
                $cursor->addDay();
            }
        }

        return [
            'total' => round($profit, 2),
            'transactions' => $trx,
            'avg' => $trx > 0 ? round($sales / $trx, 0) : 0,
            'bars' => $bars,
        ];
    }

    private function busyFromBars(array $hourly, string $field): array
    {
        $bars = [];
        $peakValue = -1;
        $peak = '-';
        foreach ($hourly as $hour => $row) {
            $value = (float) $row[$field];
            $label = sprintf('%02d', $hour);
            $bars[] = ['label' => $label, 'value' => $value];
            if ($value > $peakValue) {
                $peakValue = $value;
                $peak = $label.':00';
            }
        }

        if ($peakValue <= 0) {
            $peak = '-';
        }

        return ['peak' => $peak, 'bars' => $bars];
    }

    private function busyWeekday(array $dailySales): array
    {
        $names = [1 => 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $counts = array_fill(1, 7, 0);
        $cursor = now()->copy()->startOfMonth();
        $end = now()->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $row = $dailySales[$cursor->format('Y-m-d')] ?? null;
            if ($row) {
                $counts[$cursor->dayOfWeekIso] += $row['trx'];
            }
            $cursor->addDay();
        }

        return $this->peakBars($names, $counts);
    }

    private function busyYear(array $dailySales): array
    {
        $names = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $counts = array_fill(1, 12, 0);
        foreach ($dailySales as $date => $row) {
            if (! str_starts_with($date, now()->year.'-')) {
                continue;
            }
            $month = (int) substr($date, 5, 2);
            $counts[$month] += $row['trx'];
        }

        return $this->peakBars($names, $counts);
    }

    private function peakBars(array $names, array $counts): array
    {
        $bars = [];
        $peak = '-';
        $peakValue = -1;
        foreach ($names as $key => $label) {
            $value = (float) ($counts[$key] ?? 0);
            $bars[] = ['label' => $label, 'value' => $value];
            if ($value > $peakValue) {
                $peakValue = $value;
                $peak = $label;
            }
        }
        if ($peakValue <= 0) {
            $peak = '-';
        }

        return ['peak' => $peak, 'bars' => $bars];
    }

    private function insights(float $growth, string $period, array $categorySales, array $lowStock, float $debt, array $outlets, array $digitalApps): array
    {
        $items = [];
        $compare = $this->compareLabel($period);

        if ($growth > 0) {
            $items[] = ['tone' => 'up', 'text' => 'Penjualan naik '.$this->percent($growth), 'detail' => 'dibanding '.$compare];
        } elseif ($growth < 0) {
            $items[] = ['tone' => 'down', 'text' => 'Penjualan turun '.$this->percent(abs($growth)), 'detail' => 'dibanding '.$compare];
        } else {
            $items[] = ['tone' => 'info', 'text' => 'Penjualan belum bergerak', 'detail' => 'dibanding '.$compare];
        }

        if ($categorySales !== []) {
            $top = $categorySales[0];
            $items[] = [
                'tone' => 'info',
                'text' => 'Kategori terlaris: '.$top['name'],
                'detail' => number_format($top['value'], 0, ',', '.').' pcs · Rp '.number_format($top['amount'], 0, ',', '.'),
            ];
        }

        if (count($lowStock) > 0) {
            $items[] = [
                'tone' => 'down',
                'text' => count($lowStock).' produk menyentuh stok minimum',
                'detail' => $lowStock[0]['name'].' tersisa '.$lowStock[0]['stock'].' pcs',
            ];
        }

        if ($debt > 0) {
            $items[] = [
                'tone' => 'down',
                'text' => 'Utang belum lunas Rp '.number_format($debt, 0, ',', '.'),
                'detail' => 'dari pelanggan yang belum dibayar',
            ];
        }

        if (count($outlets) > 1) {
            $best = collect($outlets)->sortByDesc('sales')->first();
            if ($best && $best['sales'] > 0) {
                $items[] = [
                    'tone' => 'up',
                    'text' => 'Outlet terlaris: '.$best['name'],
                    'detail' => 'Rp '.number_format($best['sales'], 0, ',', '.').' pada '.$this->periodLabel($period),
                ];
            }
        }

        if ($digitalApps !== []) {
            $app = $digitalApps[0];
            $items[] = [
                'tone' => 'info',
                'text' => 'Aplikasi digital teratas: '.$app['name'],
                'detail' => $app['trx'].' trx · Rp '.number_format($app['total'], 0, ',', '.'),
            ];
        }

        return array_slice($items, 0, 5);
    }

    private function assetItems(array $stockValue, array $wallets): array
    {
        $items = [[
            'label' => 'Nilai stok (modal)',
            'value' => round((float) $stockValue['modal'], 2),
        ]];

        foreach ($wallets as $wallet) {
            $items[] = $wallet;
        }

        return $items;
    }

    private function yearChartPlaceholder(): array
    {
        return ['label' => '', 'transactions' => 0, 'revenue' => 0, 'bars' => []];
    }

    private function blankPayload(string $period): array
    {
        $emptyChart = $this->yearChartPlaceholder();
        $emptyProfit = ['total' => 0, 'transactions' => 0, 'avg' => 0, 'bars' => []];
        $emptyBusy = ['peak' => '-', 'bars' => []];
        $dashboard = [
            'sales' => 0,
            'profit' => 0,
            'transactions' => 0,
            'items' => 0,
            'digitalSales' => 0,
            'barangSales' => 0,
            'monthSales' => 0,
            'growth' => 0,
            'transfer' => 0,
            'tarik' => 0,
            'debt' => 0,
            'periodLabel' => $this->periodLabel($period),
            'compareLabel' => $this->compareLabel($period),
            'outlets' => [],
            'activity' => [],
            'lowStock' => [],
            'bestSellers' => [],
            'bestDigital' => [],
            'stockCategories' => [],
            'assetTotal' => 0,
            'stockSell' => 0,
            'stockModal' => 0,
            'walletTotal' => 0,
            'charts' => ['year' => $emptyChart, 'month' => $emptyChart, 'week' => $emptyChart],
            'busy' => ['today' => $emptyBusy, 'month' => $emptyBusy, 'year' => $emptyBusy],
            'profitSeries' => ['today' => $emptyProfit, 'week' => $emptyProfit, 'month' => $emptyProfit, 'year' => $emptyProfit],
            'insights' => [],
            'categorySales' => [],
            'digitalApps' => [],
            'recent' => [],
            'assets' => [],
            'assetName' => 'Semua outlet',
        ];

        return [
            'todaySales' => 0,
            'todayProfit' => 0,
            'totalTransactions' => 0,
            'totalItems' => 0,
            'monthlySales' => 0,
            'growth' => 0,
            'dashboard' => $dashboard,
        ];
    }

    private function fisikBase(array $ids)
    {
        return DB::table('detail_transaction')
            ->join('transactions', 'transactions.id', '=', 'detail_transaction.transaction_id')
            ->whereIn('transactions.outlet_id', $ids)
            ->whereNull('transactions.deleted_at')
            ->whereNull('detail_transaction.deleted_at');
    }

    private function digitalBase(array $ids)
    {
        return DB::table('digital_transactions')
            ->whereIn('digital_transactions.outlet_id', $ids)
            ->whereNull('digital_transactions.deleted_at')
            ->whereNotIn('digital_transactions.digital_product_id', $this->movementProductIds());
    }

    private function emptyOutletStat(): array
    {
        return ['sales' => 0, 'barang' => 0, 'digital' => 0, 'trx' => 0, 'qty' => 0, 'profit' => 0];
    }

    private function growth(float $current, float $previous): float
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 1);
        }

        return $current > 0 ? 100 : 0;
    }

    private function percent(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',').'%';
    }

    private function periodLabel(string $period): string
    {
        return match ($period) {
            'week' => 'Minggu ini',
            'month' => 'Bulan ini',
            'year' => 'Tahun ini',
            default => 'Hari ini',
        };
    }

    private function compareLabel(string $period): string
    {
        return match ($period) {
            'week' => 'minggu lalu',
            'month' => 'bulan lalu',
            'year' => 'tahun lalu',
            default => 'kemarin',
        };
    }

    private function dayShort(Carbon $date): string
    {
        return [1 => 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'][$date->dayOfWeekIso];
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'week' => [now()->copy()->startOfWeek(), now()->copy()->endOfWeek()],
            'month' => [now()->copy()->startOfMonth(), now()->copy()->endOfMonth()],
            'year' => [now()->copy()->startOfYear(), now()->copy()->endOfYear()],
            default => [now()->copy()->startOfDay(), now()->copy()->endOfDay()],
        };
    }

    private function getPreviousRange(string $period): array
    {
        return match ($period) {
            'week' => [now()->copy()->subWeek()->startOfWeek(), now()->copy()->subWeek()->endOfWeek()],
            'month' => [now()->copy()->subMonth()->startOfMonth(), now()->copy()->subMonth()->endOfMonth()],
            'year' => [now()->copy()->subYear()->startOfYear(), now()->copy()->subYear()->endOfYear()],
            default => [now()->copy()->subDay()->startOfDay(), now()->copy()->subDay()->endOfDay()],
        };
    }

    private function transferProductIds(): array
    {
        return [112, 114, 115, 119, 123, 124, 125, 127, 128, 129, 251, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 259];
    }

    private function tarikProductIds(): array
    {
        return [113, 116, 120];
    }

    private function movementProductIds(): array
    {
        return array_merge($this->transferProductIds(), $this->tarikProductIds());
    }
}

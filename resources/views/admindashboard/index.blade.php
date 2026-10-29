@extends('layouts.admin')

@section('content')
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.3); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.6); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        .category-item {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            animation: fadeUpScale 0.5s ease forwards;
        }

        .category-item:hover { transform: translateY(-4px); background: #334155; }
        .title { font-size: 13px; color: #cbd5e1; }
        .value { font-size: 16px; font-weight: 600; color: #fff; }

        @keyframes fadeUpScale {
            from { opacity: 0; transform: translateY(20px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>

    <div class="relative min-h-[300px] overflow-x-hidden">
        <p x-show="loading" class="text-xs text-white/40 mb-3">Memuat data penjualan...</p>
        <p x-show="loadError" x-text="loadError" class="text-xs text-red-300 mb-3"></p>

        <div x-show="page === 'home'" x-transition.opacity class="pb-24 space-y-6 text-xs md:text-sm">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur">
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="font-semibold text-base md:text-lg flex items-center gap-2">
                        <span x-text="selected === 'all' ? 'Semua outlet' : activeOutletName"></span>
                    </h2>
                    <div data-period-group="home" class="relative">
                        <div class="flex gap-4 text-sm font-medium text-white/60 relative">
                            <button data-period="day" @click="changePeriod('day', $el)" :class="period === 'day' ? 'text-white' : ''">Hari</button>
                            <button data-period="week" @click="changePeriod('week', $el)" :class="period === 'week' ? 'text-white' : ''">Minggu</button>
                            <button data-period="month" @click="changePeriod('month', $el)" :class="period === 'month' ? 'text-white' : ''">Bulan</button>
                            <button data-period="year" @click="changePeriod('year', $el)" :class="period === 'year' ? 'text-white' : ''">Tahun</button>
                            <div data-period-indicator class="absolute -bottom-1 h-[2px] bg-white rounded-full transition-all duration-300"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70" x-text="dash.periodLabel"></p>
                        <p class="font-semibold text-sm md:text-lg" x-text="formatRp(dash.sales)"></p>
                        <p class="text-[11px] text-white/50 mt-1" x-text="formatGrowth(dash.growth) + ' vs ' + dash.compareLabel"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Transaksi</p>
                        <p class="font-semibold text-sm md:text-lg" x-text="dash.transactions"></p>
                        <p class="text-[11px] text-white/50 mt-1" x-text="dash.items + ' pcs barang'"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Bulan ini</p>
                        <p class="font-semibold text-sm md:text-lg" x-text="formatRp(dash.monthSales)"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Laba</p>
                        <p class="font-semibold text-sm md:text-lg" x-text="formatRp(dash.profit)"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Barang</p>
                        <p class="font-semibold text-sm md:text-lg" x-text="formatRp(dash.barangSales)"></p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Digital</p>
                        <p class="font-semibold text-sm md:text-lg" x-text="formatRp(dash.digitalSales)"></p>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-white/50">
                    <span>Transfer <span class="text-white/80" x-text="formatRp(dash.transfer)"></span></span>
                    <span>Tarik tunai <span class="text-white/80" x-text="formatRp(dash.tarik)"></span></span>
                    <span>Utang belum lunas <span class="text-white/80" x-text="formatRp(dash.debt)"></span></span>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
                <div class="space-y-4">
                    <template x-if="dash.outlets.length === 0">
                        <div class="bg-[#111827] rounded-2xl p-5 border border-white/5 text-white/50">
                            Belum ada outlet pada akun ini.
                        </div>
                    </template>
                    <template x-for="outlet in dash.outlets" :key="outlet.id">
                        <div class="bg-[#111827] rounded-2xl p-4 md:p-5 border border-white/5">
                            <div class="flex justify-between items-center mb-4 gap-3">
                                <h3 class="font-semibold text-base md:text-lg" x-text="outlet.name"></h3>
                                <span class="text-white/70 text-xs" x-text="formatGrowth(outlet.growth)"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-black/30 rounded-xl p-3">
                                    <p class="text-gray-400 text-[10px]" x-text="dash.periodLabel"></p>
                                    <p class="font-medium text-sm md:text-lg" x-text="formatRp(outlet.sales)"></p>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3">
                                    <p class="text-gray-400 text-[10px]">Transaksi</p>
                                    <p class="font-medium text-sm md:text-lg" x-text="outlet.transactions"></p>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3">
                                    <p class="text-gray-400 text-[10px]">Bulan ini</p>
                                    <p class="font-medium text-sm md:text-lg" x-text="formatRp(outlet.monthSales)"></p>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3">
                                    <p class="text-gray-400 text-[10px]">Laba</p>
                                    <p class="font-medium text-sm md:text-lg" x-text="formatRp(outlet.profit)"></p>
                                </div>
                            </div>
                            <div class="mt-4 bg-black/20 border border-white/5 rounded-xl p-3">
                                <p class="text-[10px] text-gray-400 mb-2">7 hari terakhir</p>
                                <div class="flex items-end justify-between h-16 gap-2">
                                    <template x-for="(bar, i) in outlet.trend" :key="outlet.id + '-' + i">
                                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                                            <div class="w-full bg-white/30 rounded-sm" :style="'height:' + barPct(bar.value, outlet.trend) + '%'"></div>
                                            <span class="text-[9px] text-white/30 mt-1" x-text="bar.label"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="space-y-4">
                    <div class="bg-[#111827] rounded-2xl p-4 border border-white/5">
                        <h3 class="font-semibold text-base md:text-lg mb-4">Aktivitas terbaru</h3>
                        <div class="space-y-3">
                            <template x-if="dash.activity.length === 0">
                                <p class="text-white/40 text-sm">Belum ada transaksi.</p>
                            </template>
                            <template x-for="(item, i) in dash.activity" :key="i">
                                <div class="flex items-center justify-between bg-black/30 rounded-xl p-3 gap-3">
                                    <div class="min-w-0">
                                        <p class="font-medium text-white truncate" x-text="item.name"></p>
                                        <p class="text-gray-400 text-[10px]" x-text="item.outlet + ' · ' + item.time"></p>
                                    </div>
                                    <p x-show="item.amount !== null" class="text-white/70 font-semibold shrink-0" x-text="formatRp(item.amount)"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-[#111827] rounded-2xl p-4 border border-white/5" x-show="dash.digitalApps.length > 0">
                        <h3 class="font-semibold text-base mb-3">Penjualan digital per aplikasi</h3>
                        <div class="space-y-2">
                            <template x-for="app in dash.digitalApps" :key="app.name">
                                <div class="flex items-center justify-between bg-black/30 rounded-xl px-3 py-2">
                                    <div>
                                        <p class="text-white" x-text="app.name"></p>
                                        <p class="text-[10px] text-white/40" x-text="app.trx + ' trx'"></p>
                                    </div>
                                    <p class="text-white/80 font-medium" x-text="formatRp(app.total)"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="page === 'product'" x-transition.opacity class="min-h-screen p-1 md:p-2 space-y-6 pb-28">
            <div class="flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
                <div>
                    <h1 class="text-lg md:text-2xl font-semibold">Info produk outlet</h1>
                    <p class="text-gray-400 text-xs md:text-sm">Stok menipis dan produk terlaris sesuai periode yang dipilih.</p>
                </div>
                <div data-period-group="product" class="relative">
                    <div class="flex gap-4 relative pb-2 text-xs">
                        <div data-period-indicator class="absolute bottom-0 h-[2px] bg-white/70 transition-all duration-300"></div>
                        <button data-period="day" @click="changePeriod('day', $el)" :class="period === 'day' ? 'text-white' : 'text-gray-400'">Hari</button>
                        <button data-period="week" @click="changePeriod('week', $el)" :class="period === 'week' ? 'text-white' : 'text-gray-400'">Minggu</button>
                        <button data-period="month" @click="changePeriod('month', $el)" :class="period === 'month' ? 'text-white' : 'text-gray-400'">Bulan</button>
                        <button data-period="year" @click="changePeriod('year', $el)" :class="period === 'year' ? 'text-white' : 'text-gray-400'">Tahun</button>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="space-y-4">
                    <h2 class="font-semibold text-sm">Stok menipis</h2>
                    <div class="flex gap-4 overflow-x-auto no-scrollbar text-xs">
                        <template x-for="cat in stockCategoryOptions" :key="cat">
                            <button @click="stockFilter = cat" :class="stockFilter === cat ? 'text-white' : 'text-gray-400'" class="pb-2 shrink-0" x-text="cat"></button>
                        </template>
                    </div>
                    <div class="space-y-3">
                        <template x-if="visibleStock.length === 0">
                            <p class="text-sm text-white/40">Tidak ada produk di bawah stok minimum.</p>
                        </template>
                        <template x-for="item in visibleStock" :key="item.id">
                            <div class="bg-white/5 rounded-2xl p-4 flex justify-between items-center gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-400" x-text="item.category + ' · ' + item.outlet"></p>
                                </div>
                                <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full shrink-0" x-text="item.stock + ' pcs'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-semibold text-sm">Produk terlaris</h2>
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">
                            <button @click="sellerType = 'barang'; sellerFilter = 'Semua'" :class="sellerType === 'barang' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Barang</button>
                            <button @click="sellerType = 'digital'; sellerFilter = 'Semua'" :class="sellerType === 'digital' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Digital</button>
                        </div>
                    </div>
                    <div class="flex gap-4 overflow-x-auto no-scrollbar text-xs">
                        <template x-for="cat in sellerCategoryOptions" :key="sellerType + cat">
                            <button @click="sellerFilter = cat" :class="sellerFilter === cat ? 'text-white' : 'text-gray-400'" class="pb-2 shrink-0" x-text="cat"></button>
                        </template>
                    </div>
                    <div class="space-y-3">
                        <template x-if="visibleSellers.length === 0">
                            <p class="text-sm text-white/40">Belum ada penjualan pada periode ini.</p>
                        </template>
                        <template x-for="(item, i) in visibleSellers" :key="sellerType + '-' + i">
                            <div class="bg-white/5 rounded-2xl p-4 flex justify-between items-center gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-400" x-text="item.category"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-[10px] text-white/70" x-text="item.qty + ' terjual'"></p>
                                    <p class="text-xs text-white" x-text="formatRp(item.amount)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="page === 'detail'" x-transition.opacity class="min-h-screen space-y-6 pb-28">
            <div>
                <p class="text-[11px] text-gray-400" x-text="'Aset ' + dash.assetName"></p>
                <h1 class="text-3xl font-semibold mt-2" x-text="formatRp(dash.assetTotal)"></h1>
                <p class="text-xs text-white/40 mt-2">
                    Stok modal <span x-text="formatRp(dash.stockModal)"></span>
                    · Nilai jual <span x-text="formatRp(dash.stockSell)"></span>
                    · Dompet <span x-text="formatRp(dash.walletTotal)"></span>
                </p>
            </div>

            <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6">
                <h2 class="text-sm text-slate-400 mb-6">Stok per kategori</h2>
                <template x-if="dash.stockCategories.length === 0">
                    <p class="text-sm text-white/40">Belum ada stok barang.</p>
                </template>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                    <template x-for="(item, i) in dash.stockCategories" :key="item.name">
                        <div class="category-item" :style="'animation-delay:' + (i * 40) + 'ms'">
                            <span class="title" x-text="item.name"></span>
                            <span class="value" x-text="item.pcs + ' pcs'"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium">Penjualan</p>
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">
                            <button @click="chartTab = 'year'" :class="chartTab === 'year' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Tahun</button>
                            <button @click="chartTab = 'month'" :class="chartTab === 'month' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Bulan</button>
                            <button @click="chartTab = 'week'" :class="chartTab === 'week' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Minggu</button>
                        </div>
                    </div>
                    <h2 class="text-2xl font-semibold" x-text="dash.charts[chartTab].label"></h2>
                    <p class="text-sm text-white/40">Transaksi <span class="text-white" x-text="dash.charts[chartTab].transactions"></span></p>
                    <p class="text-sm text-white/40">Omzet <span class="text-white" x-text="formatRp(dash.charts[chartTab].revenue)"></span></p>
                    <div class="flex items-end gap-1 h-24">
                        <template x-for="(bar, i) in dash.charts[chartTab].bars" :key="chartTab + i">
                            <div class="flex-1 flex flex-col items-center justify-end h-full">
                                <div class="w-full max-w-3 rounded bg-white/30" :style="'height:' + barPx(bar.value, dash.charts[chartTab].bars, 72) + 'px'"></div>
                                <span class="text-[9px] text-white/30 mt-1" x-text="bar.label"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium">Jam rame</p>
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">
                            <button @click="busyTab = 'today'" :class="busyTab === 'today' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Hari</button>
                            <button @click="busyTab = 'month'" :class="busyTab === 'month' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Bulan</button>
                            <button @click="busyTab = 'year'" :class="busyTab === 'year' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Tahun</button>
                        </div>
                    </div>
                    <span class="text-xs bg-white/5 text-white/70 px-3 py-1 rounded-full" x-text="'Puncak: ' + dash.busy[busyTab].peak"></span>
                    <div class="flex items-end gap-1 h-32">
                        <template x-for="(bar, i) in dash.busy[busyTab].bars" :key="busyTab + i">
                            <div class="flex-1 flex flex-col items-center justify-end h-full">
                                <div class="w-full max-w-3 rounded bg-white/30" :style="'height:' + barPx(bar.value, dash.busy[busyTab].bars, 96) + 'px'"></div>
                                <span class="text-[9px] text-white/30 mt-1" x-text="bar.label"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium">Laba</p>
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">
                            <button @click="profitTab = 'today'" :class="profitTab === 'today' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Hari</button>
                            <button @click="profitTab = 'week'" :class="profitTab === 'week' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Minggu</button>
                            <button @click="profitTab = 'month'" :class="profitTab === 'month' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Bulan</button>
                            <button @click="profitTab = 'year'" :class="profitTab === 'year' ? 'bg-white/10 text-white' : 'text-white/40'" class="px-3 py-1 rounded-lg">Tahun</button>
                        </div>
                    </div>
                    <h2 class="text-2xl font-semibold" x-text="formatRp(dash.profitSeries[profitTab].total)"></h2>
                    <p class="text-sm text-white/40">Transaksi <span class="text-white" x-text="dash.profitSeries[profitTab].transactions"></span></p>
                    <p class="text-sm text-white/40">Rata-rata <span class="text-white" x-text="formatRp(dash.profitSeries[profitTab].avg)"></span></p>
                    <div class="flex items-end gap-1 h-20 overflow-x-auto">
                        <template x-for="(bar, i) in dash.profitSeries[profitTab].bars" :key="profitTab + i">
                            <div class="w-3 shrink-0 rounded bg-white/30" :style="'height:' + barPx(bar.value, dash.profitSeries[profitTab].bars, 72) + 'px'"></div>
                        </template>
                    </div>
                </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-3">
                    <p class="text-sm font-medium">Ringkasan</p>
                    <template x-if="dash.insights.length === 0">
                        <p class="text-sm text-white/40">Belum cukup data untuk ringkasan.</p>
                    </template>
                    <template x-for="(item, i) in dash.insights" :key="i">
                        <div class="bg-white/5 rounded-xl p-3">
                            <p class="text-sm text-white" x-text="item.text"></p>
                            <p class="text-xs text-white/40" x-text="item.detail"></p>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-sm font-medium">Penjualan barang per kategori</p>
                    <span class="text-xs text-white/40">Total <span class="text-white" x-text="categoryTotal"></span> pcs</span>
                </div>
                <template x-if="dash.categorySales.length === 0">
                    <p class="text-sm text-white/40">Belum ada penjualan barang pada periode ini.</p>
                </template>
                <div x-show="dash.categorySales.length > 0" class="flex flex-col md:flex-row items-center gap-6">
                    <div class="relative w-44 h-44">
                        <svg viewBox="0 0 160 160" class="rotate-[-90deg]">
                            <circle cx="80" cy="80" r="70" stroke="rgba(255,255,255,0.08)" stroke-width="14" fill="none" />
                            <template x-for="(item, i) in dash.categorySales" :key="item.name">
                                <circle cx="80" cy="80" r="70" fill="none" stroke-width="14" stroke-linecap="butt"
                                    :stroke="'rgba(255,255,255,' + donut(i).opacity + ')'"
                                    :stroke-dasharray="donut(i).dash"
                                    :stroke-dashoffset="donut(i).offset"></circle>
                            </template>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-semibold" x-text="categoryTotal"></span>
                            <span class="text-xs text-white/40">pcs</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3 w-full">
                        <template x-for="item in dash.categorySales" :key="'legend-' + item.name">
                            <div class="flex justify-between text-sm gap-3">
                                <span class="text-white/70" x-text="item.name"></span>
                                <span class="text-white" x-text="item.value + ' · ' + formatRp(item.amount)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm font-medium">Riwayat terbaru</p>
                    <span class="text-xs text-white/40" x-text="dash.recent.length + ' item'"></span>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <template x-if="dash.recent.length === 0">
                        <p class="text-sm text-white/40">Belum ada aktivitas.</p>
                    </template>
                    <template x-for="(item, i) in dash.recent" :key="'recent-' + i">
                        <div class="flex items-center justify-between px-3 py-3 rounded-xl gap-3">
                            <div class="min-w-0">
                                <p class="text-sm text-white/80 truncate" x-text="item.name"></p>
                                <p class="text-[11px] text-white/40" x-text="item.outlet"></p>
                            </div>
                            <span class="text-xs text-white/40 shrink-0" x-text="item.time"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="page === 'calculator'" x-data="assetApp()" class="h-full overflow-y-auto p-1 space-y-4 pb-28">
            <h1 class="text-xl font-bold text-white/90">Kalkulator aset</h1>
            <div class="relative rounded-2xl border border-white/10 bg-white/[0.04] p-6 text-center space-y-3">
                <button @click="copy()" class="absolute top-4 right-4 text-xs text-white/60 hover:text-white" x-text="copied ? 'Tersalin' : 'Salin'"></button>
                <div class="inline-flex px-3 py-1 rounded-full bg-white/10 text-xs text-white/70" x-text="outletName"></div>
                <p class="text-xs tracking-widest text-white/40 uppercase">Total aset</p>
                <h2 class="text-4xl font-semibold" x-text="format(totalAssets())"></h2>
                <p class="text-[11px] text-white/40">Angka awal diambil dari nilai stok dan saldo dompet. Ubah jika ingin simulasi.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <p class="text-sm font-semibold text-white/80">Komponen</p>
                    <button @click="addField()" class="text-xs px-3 py-1.5 rounded-lg bg-white/10 text-white/80">+ Tambah</button>
                </div>
                <template x-if="items.length === 0">
                    <p class="text-sm text-white/40">Belum ada saldo dompet atau stok.</p>
                </template>
                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center justify-between gap-3">
                            <input x-model="item.label" @input="userEdited = true" type="text" placeholder="Nama"
                                class="flex-1 bg-transparent border-none outline-none text-sm text-white/90">
                            <input type="text" inputmode="numeric" :value="formatNumber(item.value)" @input="updateValue($event, item)"
                                class="w-40 text-right px-3 py-2 rounded-xl bg-white/[0.05] border border-white/10 text-white focus:outline-none">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <script>
            function assetApp() {
                return {
                    copied: false,
                    userEdited: false,
                    outletName: 'Semua outlet',
                    items: [],
                    init() {
                        window.addEventListener('owner-assets', (event) => {
                            if (this.userEdited) return
                            const detail = event.detail || {}
                            this.outletName = detail.name || 'Semua outlet'
                            this.items = (detail.items || []).map(item => ({
                                label: item.label,
                                value: Number(item.value || 0),
                                editing: false,
                            }))
                        })
                    },
                    addField() {
                        this.userEdited = true
                        this.items.push({ label: '', value: 0, editing: true })
                    },
                    updateValue(event, item) {
                        this.userEdited = true
                        item.value = parseInt(String(event.target.value).replace(/\D/g, '') || '0', 10)
                    },
                    totalAssets() {
                        return this.items.reduce((sum, item) => sum + (Number(item.value) || 0), 0)
                    },
                    format(number) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0)
                    },
                    formatNumber(number) {
                        return new Intl.NumberFormat('id-ID').format(number || 0)
                    },
                    copy() {
                        let text = this.outletName + '\nTotal aset\n' + this.format(this.totalAssets()) + '\n\n'
                        this.items.forEach(item => {
                            if (item.label) text += item.label + ': ' + this.format(item.value) + '\n'
                        })
                        navigator.clipboard.writeText(text)
                        this.copied = true
                        setTimeout(() => { this.copied = false }, 1200)
                    }
                }
            }
        </script>
    </div>
@endsection

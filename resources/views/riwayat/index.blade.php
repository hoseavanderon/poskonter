@extends('layouts.app')

@push('head')
    <!-- FLATPICKR (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
@endpush

@section('content')
    <style>
        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.1);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-pulse {
            animation: pulse 0.8s ease-in-out infinite;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            scrollbar-width: none;
        }
    </style>

    <style>
        /* 🔧 Hilangkan scrollbar bawaan browser secara global */
        html,
        body {
            overflow-x: hidden !important;
            overflow-y: auto;
            /* ubah ke hidden kalau kamu ingin full tanpa scroll */
            height: 100%;
            background-color: #0f172a;
            /* warna dasar agar tidak ada flicker putih */
        }

        /* 🔹 Hilangkan scrollbar horizontal di container utama */
        [x-data="transactionHistory()"] {
            overflow-x: hidden !important;
        }

        /* 🔸 Pastikan area utama tidak menyebabkan scroll tambahan */
        .p-5,
        .p-6 {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* 🩵 Kalau mau hilangkan scroll seluruh halaman */
        body.no-scroll {
            overflow: hidden !important;
        }
    </style>

    <div x-data="transactionHistory()" x-init="init()" class="p-5 sm:p-6 w-full h-full overflow-x-hidden relative">

        <!-- HEADER -->
        <div class="flex items-center justify-between border-b border-gray-700 pb-3 mb-5 relative">
            <h1 class="text-2xl font-semibold text-gray-100">Riwayat Transaksi</h1>

            <!-- DROPDOWN BUTTON -->
            <button @click="toggleDropdown"
                class="p-2 rounded-md hover:bg-gray-700 transition-all duration-300 z-30 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 transform transition-transform duration-300"
                    :class="showDropdown ? 'rotate-180 text-blue-400' : 'rotate-0 text-gray-400'" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- FLOATING DROPDOWN (ganti yang lama) -->
            <div x-show="showDropdown" @click.outside="showDropdown=false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="absolute top-[52px] right-0 bg-gray-800 border border-gray-700 rounded-xl p-5 w-72 shadow-2xl z-50 space-y-4">

                <template x-for="year in availableYears" :key="year">
                    <div>
                        <p class="text-lg font-semibold text-gray-100 mb-3" x-text="year"></p>
                        <div class="grid grid-cols-4 gap-3">
                            <template x-for="m in months" :key="m">
                                <button @click="selectMonth(m, year)"
                                    class="h-10 rounded-lg text-sm font-medium transition-all duration-300"
                                    :class="(selectedYear === year && selectedMonth === m) ?
                                    'bg-blue-600 text-white scale-105 shadow' :
                                    'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                    <span x-text="m"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- DATE RANGE FILTER (FLATPICKR) -->
        <div class="flex justify-center items-center mb-6">
            <input id="dateRangePicker" type="text"
                class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-sm text-gray-200 focus:ring-1 focus:ring-blue-400 text-center w-64 cursor-pointer"
                readonly placeholder="Pilih rentang tanggal" />
        </div>

        <!-- DATE SLIDER -->
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-4 mb-4 smooth-scroll">
            <template x-for="day in days" :key="day">
                <button @click="selectedDate = day; fetchData();"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-300 ease-out"
                    :class="selectedDate === day ?
                        'bg-blue-600 text-white scale-105 shadow-md' :
                        'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                    <span x-text="day + '/' + selectedMonthNumber"></span>
                </button>
            </template>
        </div>

        <style>
            /* Hilangkan scrollbar sepenuhnya */
            .no-scrollbar::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }

            .no-scrollbar {
                -ms-overflow-style: none !important;
                scrollbar-width: none !important;
                overflow: -moz-scrollbars-none;
            }

            .smooth-scroll {
                scroll-behavior: smooth;
            }
        </style>

        <!-- MAIN GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-5">

            <div id="summaryBox"
                class="bg-[#0E1524] border border-[#1B2334] rounded-2xl p-6 text-[15px] text-[#D8DFEA] space-y-6 leading-normal">

                <!-- HEADER -->
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[12px] uppercase tracking-wider text-[#7A8292]">Transaction Summary</p>

                        <h2 class="text-[20px] font-semibold text-white mt-1">
                            <template x-if="isRangeActive">
                                <span x-text="formatRangeTanggal(fromDate, toDate)"></span>
                            </template>
                            <template x-if="!isRangeActive">
                                <span x-text="formatTanggal(selectedDate, selectedMonthNumber, selectedYear)"></span>
                            </template>
                        </h2>
                    </div>

                    <button @click="copySummary()" class="p-1 transition" :disabled="copied">

                        <!-- ICON -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            class="w-5 h-5 transition-all duration-300"
                            :class="copied
                                ?
                                'text-green-400 drop-shadow-[0_0_6px_rgba(34,197,94,0.7)] scale-110' :
                                'text-[#A9B4C8] hover:opacity-80'">

                            <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 13H7a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 01-2 2z" />

                            <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" d="M17 17H11a2 2 0 01-2-2v-6" />
                        </svg>

                    </button>

                </div>

                <hr class="border-[#1E2532]">

                <!-- BARANG + DIGITAL -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-[#9BA8BF]">Barang</span>
                        <span class="font-semibold text-[#81ACFF]" x-text="formatCurrency(barangTotal)"></span>
                    </div>

                    <template x-for="d in digitalPerApp" :key="d.name">
                        <div class="flex justify-between">
                            <span class="text-[#9BA8BF]" x-text="d.name"></span>
                            <span class="font-semibold text-[#81ACFF]" x-text="formatCurrency(d.total)"></span>
                        </div>
                    </template>
                </div>

                <hr class="border-[#1E2532]">

                <!-- SUBTOTAL -->
                <div class="flex justify-between pt-1">
                    <span class="text-[#A3AEC0]">Subtotal (Pre-Debt)</span>
                    <span class="font-semibold text-white" x-text="formatCurrency(totalPenjualanSebelumUtang)"></span>
                </div>

                <!-- DEBT -->
                <template x-if="utangList.length > 0">
                    <div class="bg-[#131B2C] rounded-xl p-4 space-y-2">
                        <p class="uppercase text-[12px] text-[#7A8292] font-semibold">DEBT</p>

                        <template x-for="u in utangList" :key="u.name">
                            <div class="flex justify-between">
                                <span class="text-white" x-text="u.name"></span>
                                <span class="text-[#FF6B6B] font-semibold"
                                    x-text="'(' + formatCurrency(u.subtotal) + ')'"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- PAYMENT -->
                <template x-if="pembayaranUtang.length > 0">
                    <div class="bg-[#131B2C] rounded-xl p-4 space-y-2">
                        <p class="uppercase text-[12px] text-[#7A8292] font-semibold">PAYMENT</p>

                        <template x-for="u in pembayaranUtang" :key="u.name">
                            <div class="flex justify-between">
                                <span class="text-[#7CFF99]" x-text="u.name"></span>
                                <span class="text-[#7CFF99] font-semibold" x-text="formatCurrency(u.subtotal)"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- TOTAL SALES -->
                <div class="bg-[#131B2C] rounded-xl px-4 py-3 flex justify-between font-semibold text-white">
                    <span>Total Sales</span>
                    <span x-text="formatCurrency(computedTotalPenjualan())"></span>
                </div>

                <!-- TRANSFERS -->
                <div>
                    <p class="uppercase text-[12px] text-[#7A8292] font-semibold mb-2">Transfers</p>

                    <div class="space-y-2">

                        <template x-if="tfTarikByApp[7]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="text-[#9BA8BF]">Brilink TF</span>
                                <span class="text-[#81ACFF]" x-text="formatCurrency(tfTarikByApp[7].tf)"></span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[7]?.tarik > 0">
                            <div class="flex justify-between">
                                <span class="text-[#FF6B6B]">Brilink Tarik</span>
                                <span class="text-[#FF6B6B]" x-text="formatCurrency(tfTarikByApp[7].tarik)"></span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[9]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="text-[#9BA8BF]">MyBCA TF</span>
                                <span class="text-[#81ACFF]" x-text="formatCurrency(tfTarikByApp[9].tf)"></span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[10]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="text-[#9BA8BF]">SHP Pay TF</span>
                                <span class="text-[#81ACFF]" x-text="formatCurrency(tfTarikByApp[10].tf)"></span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[12]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="text-[#9BA8BF]">ShopeePay TF</span>
                                <span class="text-[#81ACFF]" x-text="formatCurrency(tfTarikByApp[12].tf)"></span>
                            </div>
                        </template>

                    </div>
                </div>

                @if (Auth::user()->outlet_id == 3)
                    <div class="flex justify-between items-center font-semibold text-white text-lg mt-4">
                        <span>Grand Total</span>
                        <span x-text="formatCurrency(computedGrandTotal())"></span>
                    </div>
                @endif

            </div>

            <!-- RIGHT SIDE -->
            <div class="flex flex-col gap-5 overflow-y-auto no-scrollbar" x-ref="rightSide"
                style="max-height: calc(110vh - 100px);">
                <!-- TABS -->
                <div class="flex items-center border-b border-gray-700 w-full">
                    <button @click="activeTab = 'produk'"
                        class="px-4 py-2 text-sm font-semibold transition-all flex-1 text-center"
                        :class="activeTab === 'produk'
                            ?
                            'text-blue-400 border-blue-400 border-b-2' :
                            'text-gray-400 hover:text-gray-300'">
                        Produk Fisik
                    </button>

                    <button @click="activeTab = 'digital'"
                        class="px-4 py-2 text-sm font-semibold transition-all flex-1 text-center"
                        :class="activeTab === 'digital'
                            ?
                            'text-blue-400 border-blue-400 border-b-2' :
                            'text-gray-400 hover:text-gray-300'">
                        Produk Digital
                    </button>
                </div>

                <!-- TAB PRODUK FISIK -->
                <div x-show="activeTab === 'produk'" class="space-y-5">

                    <!-- CATEGORY SUMMARY -->
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Ringkasan Kategori Produk</h3>

                        <template x-if="categories.length > 0">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <template x-for="(c, index) in categories" :key="c.name + index">
                                    <div class="bg-gray-700/60 rounded-xl py-3 transition hover:bg-gray-700/90">
                                        <p class="text-sm font-semibold text-gray-100" x-text="c.name"></p>
                                        <p class="text-sm text-gray-400" x-text="c.total_pcs + ' pcs'"></p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="categories.length === 0">
                            <p class="text-center text-gray-400 py-10">📭 Tidak ada data kategori produk.</p>
                        </template>
                    </div>

                    <!-- PRODUCT HISTORY -->
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Riwayat Transaksi Produk</h3>

                        <template x-if="productTransactions.length > 0">
                            <div class="space-y-4">
                                <template x-for="t in productTransactions" :key="t.transaction_id">
                                    <div
                                        class="bg-gray-700/60 rounded-xl p-4 hover:bg-gray-700/80 transition-all duration-200 space-y-3">

                                        <!-- TAMPILKAN TANGGAL & JAM SEKALI SAJA -->
                                        <div class="mb-2 flex items-center gap-2">
                                            <span class="text-xs text-gray-400"
                                                x-text="formatPrettyDate(t.datetime || t.date || t.created_at)">
                                            </span>

                                            <span class="text-xs text-gray-400" x-text="getTransactionTime(t)">
                                            </span>
                                        </div>

                                        <!-- LIST PRODUK DALAM TRANSAKSI -->
                                        <template x-for="d in t.details" :key="d.name">

                                            <div class="flex justify-between items-start">

                                                <!-- KIRI (NAMA PRODUK) -->
                                                <div class="flex flex-col">
                                                    <span class="text-gray-100 font-semibold text-sm"
                                                        x-text="d.name"></span>

                                                    <!-- pcs -->
                                                    <span class="text-xs text-gray-400" x-text="d.qty + ' pcs'"></span>
                                                </div>

                                                <!-- KANAN (HARGA) -->
                                                <div class="flex flex-col text-right">
                                                    <span class="text-blue-400 font-semibold text-sm"
                                                        x-text="formatCurrency(d.amount)"></span>
                                                </div>

                                            </div>

                                        </template>

                                    </div>

                                </template>

                            </div>
                        </template>

                        <template x-if="productTransactions.length === 0">
                            <p class="text-gray-400 text-center py-8">📭 Tidak ada transaksi produk pada tanggal ini.</p>
                        </template>
                    </div>

                </div>

                <!-- TAB PRODUK DIGITAL -->
                <div x-show="activeTab === 'digital'" class="space-y-5">

                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Riwayat Produk Digital</h3>

                        <template x-if="Object.keys(digitalTransactions).length > 0">
                            <div class="space-y-6">

                                <!-- DEVICE LIST -->
                                <template x-for="(apps, deviceName) in digitalTransactions" :key="deviceName">
                                    <div>
                                        <h4 class="text-gray-200 font-semibold mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 2h6a2 2 0 012 2v16a2 2 0 01-2 2H9a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                            </svg>
                                            <span x-text="deviceName"></span>
                                        </h4>

                                        <!-- APPS UNDER DEVICE -->
                                        <div class="space-y-3">
                                            <template x-for="(app, appName) in apps" :key="appName">
                                                <div class="bg-gray-700/60 rounded-xl p-3">

                                                    <!-- APP HEADER -->
                                                    <button @click="app.open = !app.open"
                                                        class="w-full flex justify-between items-center px-2 py-1 text-left text-sm font-semibold text-gray-100 hover:text-blue-400">

                                                        <div class="flex items-center gap-2">
                                                            <span x-text="appName"></span>
                                                            <span class="text-xs text-gray-400"
                                                                x-text="app.transactions.length + ' Trx'"></span>
                                                        </div>

                                                        <div class="flex items-center gap-2">
                                                            <span class="text-blue-400 text-xs font-medium"
                                                                x-text="formatCurrency(app.total)"></span>
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="w-4 h-4 transition-transform"
                                                                :class="app.open ? 'rotate-180' : ''" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </div>

                                                    </button>

                                                    <!-- DETAIL -->
                                                    <div x-show="app.open" x-collapse class="mt-3 space-y-2">
                                                        <template x-for="t in app.transactions">
                                                            <div
                                                                class="bg-gray-800/60 border border-gray-700 rounded-lg p-3 flex justify-between items-center">
                                                                <div>
                                                                    <p class="text-gray-100 text-sm font-semibold"
                                                                        x-text="t.name"></p>
                                                                    <p class="text-xs text-gray-400" x-text="t.datetime">
                                                                    </p>
                                                                </div>
                                                                <span class="text-sm font-semibold"
                                                                    :class="{
                                                                        'text-green-400': t.category_id == 8,
                                                                        'text-red-400': t.category_id == 9,
                                                                        'text-blue-400': ![8, 9].includes(t
                                                                            .category_id),
                                                                    }"
                                                                    x-text="formatCurrency(t.amount)">
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </div>

                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </template>

                            </div>
                        </template>

                        <template x-if="Object.keys(digitalTransactions).length === 0">
                            <p class="text-center text-gray-400 py-10">📭 Tidak ada transaksi digital.</p>
                        </template>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        function transactionHistory() {
            return {
                fromDate: null,
                toDate: null,
                rangeDisplay: '',
                showDropdown: false,
                availableYears: [], // tahun dinamis dari backend
                currentYear: new Date().getFullYear(),
                currentMonthIndex: new Date().getMonth(),
                selectedYear: new Date().getFullYear(),
                selectedMonth: '',
                selectedDate: new Date().getDate(),
                emptyData: false, // tampilkan pesan jika kosong

                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                days: [],
                categories: @json($defaultData['categories'] ?? []),
                productTransactions: @json($defaultData['productTransactions'] ?? []),
                digitalTransactions: @json($defaultData['digitalTransactions'] ?? []),
                total: @json($defaultData['total'] ?? 0),
                extra: @json($defaultData['extra'] ?? 0),
                barangTotal: @json($defaultData['barangTotal'] ?? 0),
                digitalPerApp: @json($defaultData['digitalPerApp'] ?? []),
                totalTarik: @json($defaultData['totalTarik'] ?? 0),
                utangList: @json($defaultData['utangList'] ?? []),
                pembayaranUtang: @json($defaultData['pembayaranUtang'] ?? []),
                totalPembayaranUtang: @json($defaultData['totalPembayaranUtang'] ?? 0),
                totalTransfer: @json($defaultData['totalTransfer'] ?? 0),
                productTransactions: @json($defaultData['productTransactions'] ?? []),
                groupedDigitalTransactions: @json($defaultData['digitalTransactions'] ?? []),
                copied: false,
                tfTarikByApp: @json($defaultData['tfTarikByApp'] ?? []),
                isRangeActive: false,
                outletId: {{ Auth::user()->outlet_id }},
                activeTab: 'produk',

                get totalPenjualan() {
                    // Hitung total barang + semua digital apps
                    const totalDigital = this.digitalPerApp.reduce((sum, d) => sum + Number(d.total || 0), 0);
                    return Number(this.barangTotal || 0) + totalDigital;
                },

                get grandTotal() {
                    const outletId = {{ Auth::user()->outlet_id }};

                    if (outletId == 3) {
                        return Number(this.totalPenjualan || 0) +
                            this.totalTransferFix -
                            this.totalTarikFix;
                    }

                    // Outlet lain → rumus lama
                    const totalUtang = this.utangList.reduce((sum, u) => sum + Number(u.subtotal || 0), 0);
                    return this.totalPenjualan - totalUtang;
                },

                toggleDropdown() {
                    this.showDropdown = !this.showDropdown;
                },

                selectMonth(m, year) {
                    this.selectedMonth = m;
                    this.selectedYear = year;
                    this.showDropdown = false;

                    const idx = this.months.indexOf(m);
                    const month = idx + 1;
                    const lastDay = new Date(year, month, 0).getDate();
                    this.days = Array.from({
                        length: lastDay
                    }, (_, i) => i + 1);

                    this.selectedDate = 1;
                    this.fetchData();
                },

                get selectedMonthNumber() {
                    const idx = this.months.indexOf(this.selectedMonth);
                    return idx >= 0 ? String(idx + 1).padStart(2, '0') : '';
                },

                formatTanggal(day, monthNumber, year) {
                    const namaBulan = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    const bulanIndex = parseInt(monthNumber, 10) - 1;
                    const bulanNama = namaBulan[bulanIndex] || '-';
                    return `${day} ${bulanNama} ${year}`;
                },

                formatCurrency(v) {
                    return 'Rp ' + Number(v).toLocaleString('id-ID');
                },

                // helper to produce ISO YYYY-MM-DD
                formatDate(date) {
                    if (!date) return '';
                    if (typeof date === 'string') return date;
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                },

                formatPrettyDate(dateValue) {
                    if (!dateValue) return '-';

                    // Case: "YYYY-MM-DD HH:MM:SS" atau "YYYY-MM-DD HH:MM"
                    if (typeof dateValue === 'string' && dateValue.includes(' ')) {
                        const [datePart, timePart] = dateValue.split(' ');
                        const dateObj = new Date(datePart + 'T' + (timePart || '00:00'));
                        if (!isNaN(dateObj)) {
                            const cleanTime = timePart?.substring(0, 5) || '';
                            return `${this.formatIndo(datePart)} ${cleanTime}`;
                        }
                    }

                    // Case: lengkap ISO: "2025-11-15T14:30:00"
                    const tryISO = new Date(dateValue);
                    if (!isNaN(tryISO)) {
                        const jam = String(tryISO.getHours()).padStart(2, '0');
                        const menit = String(tryISO.getMinutes()).padStart(2, '0');
                        return `${this.formatIndo(tryISO)} ${jam}:${menit}`;
                    }

                    // Case fallback: "YYYY-MM-DD"
                    if (/^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                        return this.formatIndo(dateValue);
                    }

                    // Kalau semua gagal → tampilkan apa adanya
                    return dateValue;
                },

                getTransactionTime(t) {
                    if (!t) return '';

                    // Cari jam dari field transaksi langsung
                    const dt = t.datetime || t.created_at || t.date || null;
                    if (!dt) return '';

                    const d = new Date(dt);
                    if (isNaN(d)) return '';

                    const jam = String(d.getHours()).padStart(2, '0');
                    const menit = String(d.getMinutes()).padStart(2, '0');

                    return `${jam}:${menit}`;
                },

                // helper to produce pretty Indonesian date
                formatIndo(dateInput) {
                    let date = (typeof dateInput === 'string') ? new Date(dateInput) : dateInput;
                    if (!(date instanceof Date) || isNaN(date)) return '-';
                    const bulan = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`;
                },

                formatRangeTanggal(fromIso, toIso) {
                    if (!fromIso || !toIso) return this.formatIndo(fromIso || toIso);
                    if (fromIso === toIso) return this.formatIndo(fromIso);
                    return `${this.formatIndo(fromIso)} - ${this.formatIndo(toIso)}`;
                },

                setSelectedFromIso(iso) {
                    if (!iso) return;
                    const parts = iso.split('-');
                    if (parts.length !== 3) return;
                    const y = parseInt(parts[0], 10);
                    const m = parseInt(parts[1], 10);
                    const d = parseInt(parts[2], 10);
                    this.selectedYear = y;
                    this.selectedMonth = this.months[m - 1];
                    const lastDay = new Date(y, m, 0).getDate();
                    this.days = Array.from({
                        length: lastDay
                    }, (_, i) => i + 1);
                    this.selectedDate = d;
                },

                async fetchData() {
                    this.isRangeActive = false;

                    const monthNum = this.selectedMonthNumber;
                    const day = String(this.selectedDate).padStart(2, '0');
                    const date = `${this.selectedYear}-${monthNum}-${day}`;

                    try {
                        const res = await fetch(`/riwayat/data?tanggal=${date}`);
                        const data = await res.json();

                        if (data.empty) {
                            this.emptyData = true;

                            this.categories = [];
                            this.productTransactions = [];
                            this.digitalTransactions = [];
                            this.groupedDigitalTransactions = [];

                            this.total = 0;
                            this.extra = 0;
                            this.barangTotal = 0;

                            this.digitalPerApp = [];
                            this.totalTransfer = 0;
                            this.totalTarik = 0;

                            this.utangList = [];
                            this.pembayaranUtang = [];
                            this.totalPembayaranUtang = 0;

                            this.tfTarikByApp = {};
                        } else {
                            this.emptyData = false;

                            this.categories = data.categories;

                            this.productTransactions = data.productTransactions.map(t => ({
                                ...t,
                                open: false
                            }));

                            this.digitalTransactions = data.digitalTransactions || [];
                            this.groupedDigitalTransactions = data.digitalTransactions || [];

                            this.total = data.total;
                            this.extra = data.extra;
                            this.barangTotal = data.barangTotal;

                            this.digitalPerApp = data.digitalPerApp || [];

                            this.totalTransfer = data.totalTransfer || 0;
                            this.totalTarik = data.totalTarik || 0;

                            this.utangList = data.utangList || [];
                            this.tfTarikByApp = data.tfTarikByApp || {};

                            // 🔥 WAJIB
                            this.pembayaranUtang = data.pembayaranUtang || [];
                            this.totalPembayaranUtang = data.totalPembayaranUtang || 0;
                        }

                    } catch (error) {
                        console.error("⚠️ Gagal memuat data transaksi:", error);

                        this.emptyData = true;

                        this.categories = [];
                        this.productTransactions = [];
                        this.digitalTransactions = [];
                        this.groupedDigitalTransactions = [];

                        this.total = 0;
                        this.extra = 0;
                        this.barangTotal = 0;

                        this.digitalPerApp = [];
                        this.totalTransfer = 0;
                        this.totalTarik = 0;

                        this.utangList = [];
                        this.pembayaranUtang = [];
                        this.totalPembayaranUtang = 0;

                        this.tfTarikByApp = {};
                    }
                },

                async fetchDataRange() {
                    if (!this.fromDate || !this.toDate) return;
                    this.isRangeActive = true;

                    try {
                        const res = await fetch(`/riwayat/data-range?from=${this.fromDate}&to=${this.toDate}`);
                        const data = await res.json();

                        if (data.empty) {
                            this.emptyData = true;

                            this.categories = [];
                            this.productTransactions = [];
                            this.digitalTransactions = [];
                            this.groupedDigitalTransactions = [];

                            this.total = 0;
                            this.extra = 0;
                            this.barangTotal = 0;

                            this.digitalPerApp = [];
                            this.totalTransfer = 0;
                            this.totalTarik = 0;

                            this.utangList = [];
                            this.pembayaranUtang = [];
                            this.totalPembayaranUtang = 0;
                        } else {
                            this.emptyData = false;

                            this.categories = data.categories;

                            this.productTransactions = (data.productTransactions || []).map(t => ({
                                ...t,
                                open: false
                            }));

                            this.digitalTransactions = data.digitalTransactions || [];
                            this.groupedDigitalTransactions = data.digitalTransactions || [];

                            this.total = data.total || 0;
                            this.extra = data.extra || 0;
                            this.barangTotal = data.barangTotal || 0;

                            this.digitalPerApp = data.digitalPerApp || [];

                            this.totalTransfer = data.totalTransfer || 0;
                            this.totalTarik = data.totalTarik || 0;

                            this.utangList = data.utangList || [];

                            // 🔥 WAJIB
                            this.pembayaranUtang = data.pembayaranUtang || [];
                            this.totalPembayaranUtang = data.totalPembayaranUtang || 0;
                        }

                        this.rangeDisplay = this.formatRangeTanggal(this.fromDate, this.toDate);
                        this.setSelectedFromIso(this.toDate);

                        this.$nextTick(() => {
                            const container = document.querySelector('.smooth-scroll');
                            const activeBtn = container?.querySelector('.bg-blue-600');
                            if (activeBtn && container) {
                                const offsetLeft = activeBtn.offsetLeft - container.clientWidth / 2 + activeBtn
                                    .clientWidth / 2;
                                container.scrollTo({
                                    left: offsetLeft,
                                    behavior: 'smooth'
                                });
                            }
                        });

                    } catch (err) {
                        console.error("Gagal memuat data rentang tanggal:", err);
                    }
                },

                async loadAvailableYears() {
                    const res = await fetch('/riwayat/years');
                    this.availableYears = await res.json();
                },

                async copySummary() {
                    try {
                        const tanggal = this.isRangeActive ?
                            this.formatRangeTanggal(this.fromDate, this.toDate) :
                            this.formatTanggal(this.selectedDate, this.selectedMonthNumber, this.selectedYear);

                        let lines = [];
                        lines.push(`Rincian Transaksi ${tanggal}`);
                        lines.push('');

                        // ============================
                        // BARANG + DIGITAL
                        // ============================
                        lines.push(`Barang : ${this.formatCurrency(this.barangTotal)}`);

                        this.digitalPerApp.forEach(d => {
                            lines.push(`${d.name} : ${this.formatCurrency(d.total)}`);
                        });

                        lines.push('');

                        // ============================
                        // TOTAL SEBELUM UTANG
                        // ============================
                        lines.push(
                            `Total Penjualan (Sebelum Utang) : ${this.formatCurrency(this.totalPenjualanSebelumUtang)}`
                        );
                        lines.push('');

                        // ============================
                        // UTANG
                        // ============================
                        if (this.utangList.length > 0) {
                            lines.push('Utang :');
                            this.utangList.forEach(u => {
                                lines.push(`- ${u.name} (${this.formatCurrency(u.subtotal)})`);
                            });
                            lines.push('');
                        }

                        // ============================
                        // PEMBAYARAN UTANG (BARU)
                        // ============================
                        if (this.pembayaranUtang.length > 0) {
                            lines.push('Pembayaran Utang :');
                            this.pembayaranUtang.forEach(u => {
                                lines.push(`+ ${u.name} (${this.formatCurrency(u.subtotal)})`);
                            });
                            lines.push('');
                        }

                        // ============================
                        // TOTAL SESUDAH UTANG
                        // ============================
                        lines.push(
                            `Total Penjualan : ${this.formatCurrency(this.computedTotalPenjualan())}`
                        );
                        lines.push('');

                        // ============================
                        // TF & TARIK per APP
                        // ============================
                        const APP_LABELS = {
                            5: "Brimo",
                            6: "Seabank",
                            7: "Brilink",
                            9: "MyBCA",
                            10: "SHP Pay",
                            12: "ShopeePay"
                        };

                        let totalTF = 0;
                        let totalTarik = 0;

                        Object.keys(APP_LABELS).forEach(appId => {
                            const item = this.tfTarikByApp[appId];
                            if (!item) return;

                            const name = APP_LABELS[appId];

                            if (item.tf > 0) {
                                lines.push(`${name} TF : ${this.formatCurrency(item.tf)}`);
                                totalTF += Number(item.tf);
                            }

                            if (item.tarik > 0) {
                                lines.push(`${name} Tarik : ${this.formatCurrency(item.tarik)}`);
                                totalTarik += Number(item.tarik);
                            }
                        });

                        lines.push('');

                        // ============================
                        // GRAND TOTAL (OUTLET 3)
                        // ============================
                        if (this.outletId == 3) {
                            lines.push(
                                `Grand Total : ${this.formatCurrency(this.computedGrandTotal())}`
                            );
                        }

                        // ============================
                        // TOTAL TF/TARIK (OUTLET BUKAN 3)
                        // ============================
                        if (this.outletId != 3) {
                            lines.push(`TOTAL TF : ${this.formatCurrency(totalTF)}`);
                            lines.push(`TOTAL TARIK : ${this.formatCurrency(totalTarik)}`);
                        }

                        await navigator.clipboard.writeText(lines.join('\n'));

                        this.copied = true;
                        setTimeout(() => (this.copied = false), 2000);

                    } catch (err) {
                        console.error('❌ Copy gagal:', err);
                    }
                },

                computedTotalPenjualan() {
                    const barang = Number(this.barangTotal || 0);
                    const digital = this.digitalPerApp.reduce((s, d) => s + Number(d.total || 0), 0);

                    // ini FIXED
                    const utang = this.utangList.reduce((s, d) => s + Number(d.subtotal || 0), 0);

                    const bayarUtang = Number(this.totalPembayaranUtang || 0);

                    return (barang + digital) - utang + bayarUtang;
                },

                computedGrandTotal() {
                    if (this.outletId !== 3) return 0;

                    const totalPenjualan = this.computedTotalPenjualan();
                    const totalTF = this.totalTransferFix;
                    const totalTarik = this.totalTarikFix;

                    return totalPenjualan + totalTF - totalTarik;
                },

                get totalPenjualanSebelumUtang() {
                    const barang = Number(this.barangTotal || 0);
                    const digital = this.digitalPerApp.reduce((s, d) => s + Number(d.total || 0), 0);
                    return barang + digital;
                },

                get totalTransferFix() {
                    return Object.values(this.tfTarikByApp || {}).reduce((sum, a) => sum + (a.tf || 0), 0);
                },

                get totalTarikFix() {
                    return Object.values(this.tfTarikByApp || {}).reduce((sum, a) => sum + (a.tarik || 0), 0);
                },

                init() {
                    this.selectedMonth = this.months[this.currentMonthIndex];
                    this.selectedYear = this.currentYear;

                    const year = this.currentYear;
                    const month = this.currentMonthIndex + 1;
                    const lastDay = new Date(year, month, 0).getDate();
                    this.days = Array.from({
                        length: lastDay
                    }, (_, i) => i + 1);

                    this.loadAvailableYears();

                    this.$nextTick(() => {
                        // Inisialisasi Flatpickr untuk rentang tanggal
                        const fp = flatpickr("#dateRangePicker", {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            locale: "id",
                            defaultDate: [this.fromDate, this.toDate],
                            onChange: (selectedDates, dateStr, instance) => {
                                if (selectedDates.length === 2) {
                                    this.fromDate = this.formatDate(selectedDates[0]);
                                    this.toDate = this.formatDate(selectedDates[1]);
                                    instance.input.value = this.formatRangeTanggal(this.fromDate, this
                                        .toDate);
                                    this.fetchDataRange();
                                }
                            },
                        });

                        const container = document.querySelector('.smooth-scroll');
                        const activeBtn = container?.querySelector('.bg-blue-600');
                        if (activeBtn && container) {
                            const offsetLeft = activeBtn.offsetLeft - container.clientWidth / 2 + activeBtn
                                .clientWidth / 2;
                            container.scrollTo({
                                left: offsetLeft,
                                behavior: 'smooth'
                            });
                        }
                    });
                },
            };
        }
    </script>
@endsection

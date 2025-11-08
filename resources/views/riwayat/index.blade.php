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

            <!-- LEFT SUMMARY -->
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 text-sm text-gray-300 space-y-3">

                <!-- JUDUL TANGGAL -->
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-base font-semibold text-gray-100">
                        Rincian Transaksi
                        <template x-if="isRangeActive">
                            <span x-text="'Tanggal ' + formatRangeTanggal(fromDate, toDate)"></span>
                        </template>
                        <template x-if="!isRangeActive">
                            <span
                                x-text="'Tanggal ' + formatTanggal(selectedDate, selectedMonthNumber, selectedYear)"></span>
                        </template>
                        :
                    </h2>

                    <!-- Tombol Copy -->
                    <button @click="copySummary()"
                        class="flex items-center gap-1 px-2 py-1 text-xs rounded-md border border-gray-600 text-gray-300 hover:bg-gray-700 transition-all duration-300 relative overflow-hidden"
                        :disabled="copied">
                        <template x-if="!copied">
                            <span class="flex items-center gap-1">
                                📋 <span>Copy</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="flex items-center gap-1 text-green-400 animate-pulse">
                                ✅ <span>Copied!</span>
                            </span>
                        </template>
                    </button>
                </div>

                <!-- BAGIAN ATAS: Barang + Digital App -->
                <div class="space-y-1.5">
                    <!-- BARANG -->
                    <div class="flex justify-between items-center">
                        <span>Barang :</span>
                        <span class="font-medium text-blue-400" x-text="formatCurrency(barangTotal)"></span>
                    </div>

                    <!-- DIGITAL APPS -->
                    <template x-for="d in digitalPerApp" :key="d.name">
                        <div class="flex justify-between items-center">
                            <span x-text="d.name + ' :'"></span>
                            <span class="font-medium text-blue-400" x-text="formatCurrency(d.total)"></span>
                        </div>
                    </template>
                </div>

                <!-- ⚡️UTANG DITEMPATKAN DI SINI⚡️ -->
                <template x-if="utangList.length > 0">
                    <div>
                        <hr class="border-gray-700 my-2">
                        <p class="font-semibold text-gray-200 mb-1">Utang :</p>
                        <template x-for="u in utangList" :key="u.name">
                            <div class="flex justify-between items-center pl-2">
                                <span x-text="u.name"></span>
                                <span class="text-rose-400" x-text="'(' + formatCurrency(u.subtotal) + ')'"></span>
                            </div>
                        </template>
                    </div>
                </template>
                <!-- ⚡️UTANG SAMPAI SINI⚡️ -->

                <hr class="border-gray-700 my-2">

                <!-- TOTAL SUMMARY -->
                <div class="space-y-1">

                    <!-- Total Penjualan Semua (Barang + Digital) -->
                    <div class="flex justify-between font-semibold text-gray-200">
                        <span>Total Penjualan</span>
                        <span x-text="formatCurrency(totalPenjualan)"></span>
                    </div>

                    <!-- Grand Total (setelah dikurangi utang) -->
                    <div class="flex justify-between font-semibold text-blue-400">
                        <span>Grand Total</span>
                        <span x-text="formatCurrency(grandTotal)"></span>
                    </div>

                    <hr class="border-gray-700 my-2">

                    <!-- TOTAL TRANSFER -->
                    <template x-if="totalTransfer > 0">
                        <div class="pt-3 flex justify-between">
                            <span>Total Transfer :</span>
                            <span class="font-medium text-blue-400" x-text="formatCurrency(totalTransfer)"></span>
                        </div>
                    </template>

                    <!-- TOTAL TARIK -->
                    <template x-if="totalTarik > 0">
                        <div class="pt-1 flex justify-between">
                            <span class="text-red-500">Total Tarik :</span>
                            <span class="font-medium text-red-500" x-text="formatCurrency(totalTarik)"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="flex flex-col gap-5">
                <!-- CATEGORY SUMMARY -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-gray-200 mb-3">Ringkasan Kategori Produk</h3>

                    <!-- Kalau ada data -->
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

                    <!-- Kalau kosong -->
                    <template x-if="categories.length === 0">
                        <div class="text-center text-gray-400 py-10">
                            <template x-if="!isRangeActive">
                                <p>
                                    📭 Tidak ada data pada tanggal
                                    <span x-text="formatTanggal(selectedDate, selectedMonthNumber, selectedYear)"></span>.
                                </p>
                            </template>

                            <template x-if="isRangeActive">
                                <p>
                                    📭 Tidak ada data dari tanggal
                                    <span x-text="formatIndo(fromDate)"></span>
                                    sampai
                                    <span x-text="formatIndo(toDate)"></span>.
                                </p>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- PRODUCT + DIGITAL HISTORY -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- PRODUCT HISTORY (Card per Transaction, simple) -->
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Riwayat Transaksi Produk</h3>

                        <template x-if="productTransactions.length > 0">
                            <div class="space-y-3">
                                <template x-for="t in productTransactions" :key="t.transaction_id">
                                    <div
                                        class="bg-gray-700/60 rounded-xl p-3 hover:bg-gray-700/80 transition-all duration-200">
                                        <!-- DETAIL PRODUK DALAM TRANSAKSI -->
                                        <div class="space-y-2">
                                            <template x-for="d in t.details" :key="d.name">
                                                <div>
                                                    <!-- Baris 1: nama produk + harga -->
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-200 text-sm truncate"
                                                            x-text="d.name"></span>
                                                        <span class="text-blue-400 text-sm font-medium"
                                                            x-text="formatCurrency(d.amount)"></span>
                                                    </div>

                                                    <!-- Baris 2: jumlah pcs -->
                                                    <div class="text-right text-xs text-gray-400" x-text="d.qty + ' pcs'">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="productTransactions.length === 0">
                            <p class="text-gray-400 text-center py-8">📭 Tidak ada transaksi produk pada tanggal ini.</p>
                        </template>
                    </div>

                    <!-- DIGITAL HISTORY -->
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Riwayat Transaksi Produk Digital</h3>

                        <template x-if="groupedDigitalTransactions.length > 0">
                            <div class="space-y-3">
                                <template x-for="(app, appIndex) in groupedDigitalTransactions"
                                    :key="app.name + appIndex">
                                    <div class="bg-gray-700/60 rounded-xl p-3">
                                        <!-- HEADER APP -->
                                        <button @click="app.open = !app.open"
                                            class="w-full flex justify-between items-center px-2 py-1 text-left text-sm font-semibold text-gray-100 hover:text-blue-400 transition">
                                            <div class="flex items-center gap-2">
                                                <span x-text="app.name"></span>
                                                <!-- 🔹 Tambahan: jumlah transaksi -->
                                                <span class="text-xs text-gray-400 font-normal"
                                                    x-text="'( ' + app.transactions.length + ' Trx )'"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-blue-400 font-medium text-xs"
                                                    x-text="formatCurrency(app.total)"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 transform transition-transform"
                                                    :class="app.open ? 'rotate-180' : ''" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- DETAIL PRODUK PER APP -->
                                        <div x-show="app.open" x-collapse class="mt-3 space-y-2">
                                            <template x-for="(t, index) in app.transactions"
                                                :key="app.name + '-' + index">
                                                <div
                                                    class="bg-gray-800/60 border border-gray-700 rounded-lg p-3 flex justify-between items-center hover:bg-gray-700 transition">
                                                    <div class="flex flex-col">
                                                        <span class="text-gray-100 text-sm font-semibold"
                                                            x-text="t.name"></span>
                                                        <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="w-3 h-3 text-blue-400" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v4.5a.75.75 0 00.75.75h3a.75.75 0 000-1.5H10.75v-3.75z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            <span x-text="t.datetime"></span>
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-semibold"
                                                        :class="{
                                                            'text-green-400': [5, 6, 7].includes(app.app_id) && t
                                                                .category_id == 8, // transfer = hijau
                                                            'text-red-400': [5, 6, 7].includes(app.app_id) && t
                                                                .category_id == 9, // tarik tunai = merah
                                                            'text-blue-400': ![8, 9].includes(t
                                                                .category_id) // normal = biru
                                                        }"
                                                        x-text="formatCurrency(t.amount)">
                                                    </span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="groupedDigitalTransactions.length === 0">
                            <p class="text-gray-400 text-center py-8">📭 Tidak ada transaksi digital pada tanggal ini.</p>
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
                totalTransfer: @json($defaultData['totalTransfer'] ?? 0),
                productTransactions: @json($defaultData['productTransactions'] ?? []),
                groupedDigitalTransactions: @json($defaultData['digitalTransactions'] ?? []),
                copied: false,

                isRangeActive: false,

                get totalPenjualan() {
                    // Hitung total barang + semua digital apps
                    const totalDigital = this.digitalPerApp.reduce((sum, d) => sum + Number(d.total || 0), 0);
                    return Number(this.barangTotal || 0) + totalDigital;
                },

                get grandTotal() {
                    // Total - semua utang
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
                            this.total = 0;
                            this.extra = 0;
                            this.barangTotal = 0;
                            this.digitalPerApp = [];
                            this.totalTransfer = 0;
                            this.totalTarik = 0;
                            this.utangList = [];
                            this.groupedDigitalTransactions = [];
                        } else {
                            this.emptyData = false;
                            this.categories = data.categories;
                            this.productTransactions = data.productTransactions.map(t => ({
                                ...t,
                                open: false
                            }));
                            this.digitalTransactions = data.digitalTransactions || [];
                            this.total = data.total;
                            this.extra = data.extra;
                            this.barangTotal = data.barangTotal;
                            this.digitalPerApp = data.digitalPerApp;
                            this.totalTransfer = data.totalTransfer;
                            this.totalTarik = data.totalTarik;
                            this.utangList = data.utangList;
                            this.groupedDigitalTransactions = data.digitalTransactions;
                        }
                    } catch (error) {
                        console.error("⚠️ Gagal memuat data transaksi:", error);
                        this.emptyData = true;
                        this.categories = [];
                        this.productTransactions = [];
                        this.digitalTransactions = [];
                        this.total = 0;
                        this.extra = 0;
                        this.barangTotal = 0;
                        this.digitalPerApp = [];
                        this.totalTransfer = 0;
                        this.totalTarik = 0;
                        this.utangList = [];
                    }
                },

                async fetchDataRange() {
                    if (!this.fromDate || !this.toDate) return;
                    this.isRangeActive = true; // <--- Tambahkan ini
                    try {
                        const res = await fetch(`/riwayat/data-range?from=${this.fromDate}&to=${this.toDate}`);
                        const data = await res.json();

                        if (data.empty) {
                            this.emptyData = true;
                            this.categories = [];
                            this.productTransactions = [];
                            this.digitalTransactions = [];
                            this.total = 0;
                            this.extra = 0;
                            this.barangTotal = 0;
                            this.digitalPerApp = [];
                            this.totalTransfer = 0;
                            this.totalTarik = 0;
                            this.utangList = [];
                            this.groupedDigitalTransactions = [];
                        } else {
                            this.emptyData = false;
                            this.categories = data.categories;
                            this.productTransactions = (data.productTransactions || []).map(t => ({
                                ...t,
                                open: false
                            }));
                            this.digitalTransactions = data.digitalTransactions || [];
                            this.total = data.total || 0;
                            this.extra = data.extra || 0;
                            this.barangTotal = data.barangTotal || 0;
                            this.digitalPerApp = data.digitalPerApp || [];
                            this.totalTransfer = data.totalTransfer || 0;
                            this.totalTarik = data.totalTarik || 0;
                            this.utangList = data.utangList || [];
                            this.groupedDigitalTransactions = data.digitalTransactions || [];
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
                        // 📅 Format tanggal (range atau satuan)
                        const tanggal = this.isRangeActive ?
                            this.formatRangeTanggal(this.fromDate, this.toDate) :
                            this.formatTanggal(this.selectedDate, this.selectedMonthNumber, this.selectedYear);

                        let lines = [];
                        lines.push(`${tanggal}`);
                        lines.push('');

                        // 🧩 TOTAL BARANG
                        lines.push(`Barang : ${this.formatCurrency(this.barangTotal)}`);

                        // 🧩 DIGITAL PER APP
                        let totalDigital = 0;
                        if (this.digitalPerApp.length > 0) {
                            this.digitalPerApp.forEach(app => {
                                totalDigital += Number(app.total || 0);
                                lines.push(`${app.name} : ${this.formatCurrency(app.total)}`);
                            });
                            lines.push('');
                        }

                        // 🧩 TOTAL SEMUA PRODUK = Barang + Digital
                        const totalProduk = Number(this.barangTotal || 0) + totalDigital;
                        lines.push(`= ${this.formatCurrency(totalProduk)}`);
                        lines.push('');

                        // 🧩 UTANG
                        if (this.utangList.length > 0) {
                            lines.push('Utang :');
                            this.utangList.forEach(u => {
                                const nominal = Math.abs(Number(u.subtotal || 0));
                                lines.push(`${u.name} (Rp ${nominal.toLocaleString('id-ID')})`);
                            });
                            lines.push('');
                        }

                        // 🧩 BAYAR HUTANG
                        lines.push('Bayar Hutang  :');
                        lines.push('');

                        // 🧩 TOTAL RINCIAN
                        // (Total & Grand Total pakai totalProduk + extra)
                        lines.push(`Total => ${this.formatCurrency(totalProduk)}`);
                        lines.push(`Lebih => ${this.formatCurrency(this.extra)}`);
                        lines.push(`Total => ${this.formatCurrency(totalProduk + Number(this.extra || 0))}`);
                        lines.push('');

                        // 🧩 BAGIAN BOLD (format WhatsApp-friendly)
                        lines.push(`*TOTAL NARIK : ${this.formatCurrency(this.totalTarik)}*`);
                        lines.push(`*TOTAL TF : ${this.formatCurrency(this.totalTransfer)}*`);

                        // 🧾 SALIN KE CLIPBOARD
                        const textToCopy = lines.join('\n');
                        await navigator.clipboard.writeText(textToCopy);

                        // 🔄 Animasi copied
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } catch (err) {
                        console.error('❌ Gagal menyalin teks:', err);
                        alert('Gagal menyalin teks ke clipboard.');
                    }
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

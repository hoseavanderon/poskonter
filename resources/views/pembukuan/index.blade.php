@extends('layouts.app')

@section('content')
    <div x-data="ledgerApp()" x-init="init()" class="p-3 sm:p-4 md:p-6 w-full h-full overflow-x-hidden">
        <style>
            /* === Scrollbar modern === */
            .scrollbar-modern::-webkit-scrollbar {
                height: 6px;
                width: 6px;
            }

            .scrollbar-modern::-webkit-scrollbar-track {
                background: transparent;
            }

            .scrollbar-modern::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 999px;
            }

            .dark .scrollbar-modern::-webkit-scrollbar-thumb {
                background: #374151;
            }

            .scrollbar-modern {
                scrollbar-width: thin;
                scrollbar-color: #d1d5db transparent;
            }

            .dark .scrollbar-modern {
                scrollbar-color: #374151 transparent;
            }

            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
        <!-- === GRID LAYOUT === -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-4 max-w-[1800px] mx-auto">

            <!-- LEFT: Balance & Wallets -->
            <aside
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-5 flex flex-col min-w-0 overflow-hidden space-y-5">

                <!-- CARD SALDO (tanpa garis tipis) -->
                <div class="bg-[#1f2531] dark:bg-gray-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <h3 class="text-sm font-semibold text-gray-200">Sisa Saldo</h3>

                    <div class="mt-2">
                        <p class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Rp 100.000</p>
                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Terakhir Di Update :
                        <span class="text-gray-300 font-medium">22 Oct 2025 18:01</span>
                    </p>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="grid grid-cols-4 gap-3 mt-5 text-center select-none">

                    <!-- 🔹 MASUK -->
                    <div>
                        <button
                            class="w-full h-12 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-xl transition-all duration-300 hover:scale-[1.04] active:scale-[0.97] shadow-sm hover:shadow-md">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-200" />
                        </button>
                        <p class="mt-2.5 text-[11px] font-medium text-gray-300">Masuk</p>
                    </div>

                    <!-- 🔸 KELUAR -->
                    <div>
                        <button
                            class="w-full h-12 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-xl transition-all duration-300 hover:scale-[1.04] active:scale-[0.97] shadow-sm hover:shadow-md">
                            <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-gray-200" />
                        </button>
                        <p class="mt-2.5 text-[11px] font-medium text-gray-300">Keluar</p>
                    </div>

                    <!-- 🟣 SWAP -->
                    <div>
                        <button
                            class="w-full h-12 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-xl transition-all duration-300 hover:scale-[1.04] active:scale-[0.97] shadow-sm hover:shadow-md">
                            <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-200" />
                        </button>
                        <p class="mt-2.5 text-[11px] font-medium text-gray-300">Swap</p>
                    </div>

                    <!-- 🟢 TAMBAH -->
                    <div>
                        <button
                            class="w-full h-12 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-xl transition-all duration-300 hover:scale-[1.04] active:scale-[0.97] shadow-sm hover:shadow-md">
                            <x-heroicon-o-plus class="w-5 h-5 text-gray-200" />
                        </button>
                        <p class="mt-2.5 text-[11px] font-medium text-gray-300">Tambah</p>
                    </div>
                </div>

                <!-- LIST WALLET -->
                <div class="flex flex-col gap-2 sm:gap-3 flex-1 overflow-y-auto no-scrollbar pr-1">
                    <template x-for="w in wallets" :key="w.id">
                        <button @click="selectWallet(w.id)"
                            class="relative flex items-center gap-3 p-3 rounded-xl w-full text-left group transition-all duration-200 ease-out hover:scale-[1.02] hover:bg-gray-100 dark:hover:bg-gray-700"
                            :class="selectedWallet === w.id ? 'bg-gray-100 dark:bg-gray-700 shadow-sm scale-[1.02]' :
                                'bg-transparent'">

                            <!-- 🔹 Animated Active Indicator -->
                            <div x-cloak x-show="selectedWallet === w.id" x-transition.opacity.duration.300ms
                                x-transition:enter="transform transition ease-out duration-300"
                                x-transition:enter-start="-translate-x-2 opacity-0"
                                x-transition:enter-end="translate-x-0 opacity-100"
                                class="absolute -left-[3px] top-1/2 -translate-y-1/2 h-[70%] w-[3px] bg-blue-500 rounded-r-md">
                            </div>

                            <!-- Heroicon -->
                            <div class="relative w-10 h-10 flex items-center justify-center flex-shrink-0">
                                <div x-bind:class="selectedWallet === w.id ?
                                    'absolute left-0 top-1/2 -translate-y-1/2 h-[80%] w-[3px] bg-blue-500 rounded-full opacity-100 translate-x-0 shadow-[0_0_6px_#3b82f6aa]' :
                                    'opacity-0 -translate-x-2'"
                                    x-transition.opacity.duration.300ms
                                    x-transition:enter="transform transition ease-out duration-300"
                                    x-transition:enter-start="-translate-x-2 opacity-0"
                                    x-transition:enter-end="translate-x-0 opacity-100"
                                    class="absolute left-0 h-[80%] w-[3px] bg-blue-500 rounded-full"></div>

                                <template x-if="w.id === 0">
                                    <x-heroicon-o-wallet class="w-6 h-6 text-gray-500 dark:text-gray-300" />
                                </template>
                                <template x-if="w.id === 1">
                                    <x-heroicon-o-credit-card class="w-6 h-6 text-green-500" />
                                </template>
                                <template x-if="w.id === 2">
                                    <x-heroicon-o-banknotes class="w-6 h-6 text-blue-500" />
                                </template>
                                <template x-if="w.id === 3">
                                    <x-heroicon-o-building-library class="w-6 h-6 text-purple-500" />
                                </template>
                                <template x-if="w.id === 4">
                                    <x-heroicon-o-currency-dollar class="w-6 h-6 text-yellow-500" />
                                </template>
                            </div>

                            <!-- TEXT -->
                            <div class="min-w-0 flex-1 pr-3">
                                <p class="font-medium text-sm truncate text-gray-200 dark:text-gray-100" x-text="w.name">
                                </p>
                                <p class="text-xs text-gray-400 truncate" x-text="w.note"></p>
                            </div>

                            <!-- VALUE -->
                            <div class="text-right flex-shrink-0">
                                <p class="font-medium text-sm text-gray-200 dark:text-gray-100"
                                    x-text="formatCurrency(w.balance)"></p>
                                <p class="text-xs text-gray-400" x-text="w.type"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </aside>
            <!-- RIGHT: Transactions -->
            <section
                class="relative bg-[#1f2937] dark:bg-gray-800 rounded-2xl shadow-md p-7 flex flex-col min-w-0 overflow-hidden"
                style="height: 640px;" {{-- fixed tinggi --}} x-data="{
                    showSearch: false,
                    showDropdown: false,
                    selectedMonth: 'latest',
                    selectedDate: new Date().getDate(),
                    selectedYear: new Date().getFullYear(),
                    activeTransaction: null,
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    days: Array.from({ length: 30 }, (_, i) => i + 1),
                    transactions: [
                        { id: 1, title: 'Saldo Awal', note: 'asdasdasd', amount: 123.123, date: '2025-10-22', balance: 100.000, created_at: '22 Oktober 2025 18:01:34' },
                        { id: 2, title: 'Top Up', note: 'Isi saldo utama', amount: 500.000, date: '2025-10-21', balance: 600.000, created_at: '21 Oktober 2025 12:05:14' },
                        { id: 3, title: 'Pembelian', note: 'Voucher Game', amount: -100.000, date: '2025-10-20', balance: 500.000, created_at: '20 Oktober 2025 11:22:40' },
                    ],
                    toggleSearch() { this.showSearch = !this.showSearch },
                    toggleDropdown() { this.showDropdown = !this.showDropdown },
                    selectMonth(m) {
                        this.selectedMonth = m;
                        this.showDropdown = false
                    },
                    toggleTransaction(id) { this.activeTransaction = this.activeTransaction === id ? null : id },
                    formatCurrency(v) { return 'Rp ' + v.toLocaleString('id-ID', { minimumFractionDigits: 3 }); }
                }">

                <style>
                    .no-scrollbar::-webkit-scrollbar {
                        display: none;
                    }

                    .no-scrollbar {
                        -ms-overflow-style: none;
                        scrollbar-width: none;
                    }

                    .smooth-scroll {
                        scroll-behavior: smooth;
                    }
                </style>

                <!-- HEADER -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-100">Transaction History</h2>
                    <button @click="toggleSearch()" class="p-2 rounded-md hover:bg-gray-700 transition-all duration-300">
                        <template x-if="!showSearch">
                            <x-heroicon-o-magnifying-glass
                                class="w-5 h-5 text-gray-400 transition-transform duration-300 hover:scale-110" />
                        </template>
                        <template x-if="showSearch">
                            <x-heroicon-o-x-mark
                                class="w-5 h-5 text-gray-400 transition-transform duration-300 hover:rotate-90" />
                        </template>
                    </button>
                </div>

                <!-- SEARCH -->
                <div x-show="showSearch" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                    class="my-3">
                    <input type="text" placeholder="Cari transaksi..."
                        class="w-full rounded-lg p-2 border border-gray-600 bg-gray-700 text-gray-200 focus:ring focus:ring-blue-400 text-sm placeholder-gray-400" />
                </div>

                <!-- WRAPPER KONTEN SCROLLABLE -->
                <div class="relative flex-1 overflow-hidden">
                    <!-- SCROLLABLE AREA -->
                    <div class="absolute inset-0 flex flex-col overflow-y-auto no-scrollbar pr-1 pb-2 smooth-scroll">

                        <!-- SELECT MONTH -->
                        <div class="pb-4 border-b border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold text-gray-300">Select Month</h3>
                                <button @click="toggleDropdown()" class="p-1.5 rounded-md hover:bg-gray-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 transform transition-transform duration-300"
                                        :class="showDropdown ? 'rotate-180 text-blue-400' : 'rotate-0 text-gray-400'"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <!-- SLIDER MONTH -->
                            <div class="flex gap-2 overflow-x-auto no-scrollbar smooth-scroll pb-1">
                                <button @click="selectMonth('latest')"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 ease-out"
                                    :class="selectedMonth === 'latest'
                                        ?
                                        'bg-blue-600 text-white scale-105 shadow-md' :
                                        'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                    Latest
                                </button>
                                <template x-for="m in months" :key="m">
                                    <button @click="selectMonth(m)"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 ease-out"
                                        :class="selectedMonth === m ?
                                            'bg-blue-600 text-white scale-105 shadow-md' :
                                            'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                        <span x-text="m"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- DATE SLIDER -->
                        <div class="py-4 border-b border-gray-700">
                            <div x-ref="dateContainer" class="flex gap-2 overflow-x-auto no-scrollbar smooth-scroll px-1">
                                <template x-for="day in days" :key="day"> <button @click="selectedDate = day"
                                        class="min-w-[48px] h-9 flex items-center justify-center rounded-full text-sm font-medium whitespace-nowrap transition-all duration-300 ease-out"
                                        :class="selectedDate === day ? 'bg-blue-600 text-white scale-105 shadow-md' :
                                            'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                        <span x-text="day + '/10'"></span> </button> </template>
                            </div>
                        </div>


                        <!-- TRANSACTION LIST -->
                        <div class="flex flex-col gap-4 mt-2 pb-3">
                            <template x-for="t in transactions" :key="t.id">
                                <div
                                    class="bg-gray-700/90 rounded-xl p-3 transition-all duration-300 ease-out hover:scale-[1.01] hover:shadow-lg hover:bg-gray-700/95">
                                    <button @click="toggleTransaction(t.id)"
                                        class="w-full flex items-center justify-between text-left transition-all duration-300">
                                        <div>
                                            <p class="font-medium text-gray-100">
                                                <span x-text="t.date.substring(8,10) + '/' + t.date.substring(5,7)"></span>
                                                -
                                                <span x-text="t.title"></span>
                                            </p>
                                            <p class="text-xs text-gray-400" x-text="t.note"></p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <p class="text-sm font-semibold"
                                                :class="t.amount > 0 ? 'text-green-400' : 'text-red-400'"
                                                x-text="formatCurrency(t.amount)"></p>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5 transform transition-transform duration-300"
                                                :class="activeTransaction === t.id ? 'rotate-180 text-blue-400' :
                                                    'rotate-0 text-gray-400'"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>

                                    <!-- DETAIL DROPDOWN -->
                                    <div x-show="activeTransaction === t.id"
                                        x-transition:enter="transition ease-out duration-400"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-2"
                                        class="mt-3 bg-gray-800 rounded-xl p-4 border border-gray-700 text-sm space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Created</span>
                                            <span class="font-medium text-gray-100" x-text="t.created_at"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Nominal</span>
                                            <span class="font-medium text-gray-100"
                                                x-text="formatCurrency(t.amount)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Saldo Saat Itu</span>
                                            <span class="font-medium text-gray-100"
                                                x-text="formatCurrency(t.balance)"></span>
                                        </div>
                                        <div class="pt-2">
                                            <button
                                                class="flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all duration-300 ease-out hover:scale-[1.02]">
                                                <x-heroicon-o-trash class="w-4 h-4" /> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- DROPDOWN YEAR OVERLAY -->
                    <div x-show="showDropdown" x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute top-[80px] left-0 right-0 bg-gray-800 rounded-xl p-5 border border-gray-700 space-y-4 shadow-lg z-20 max-h-[380px] overflow-y-auto no-scrollbar">
                        <template x-for="year in [2025,2024]" :key="year">
                            <div>
                                <p class="text-xl font-bold text-gray-100 mb-3" x-text="year"></p>
                                <div class="grid grid-cols-4 gap-3">
                                    <template x-for="m in months" :key="m">
                                        <button @click="selectMonth(m)"
                                            class="h-10 rounded-lg text-sm font-medium transition-all duration-300"
                                            :class="selectedMonth === m ?
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
            </section>

        </div>
    </div>

    <script>
        function ledgerApp() {
            return {
                showSearch: false,
                filter: '',
                selectedWallet: null,
                selectedDate: new Date().getDate(),
                selectedMonthIndex: new Date().getMonth(),
                selectedYear: new Date().getFullYear(),
                days: Array.from({
                    length: 30
                }, (_, i) => i + 1),
                wallets: [{
                        id: 0,
                        name: 'All Wallets',
                        icon: '💼',
                        balance: 4494.50,
                        note: 'Semua dana',
                        type: 'Multi',
                        color: '#6b7280'
                    },
                    {
                        id: 1,
                        name: 'Main Wallet',
                        icon: '💳',
                        balance: 3200.00,
                        note: 'Utama',
                        type: 'Cash',
                        color: '#10b981'
                    },
                    {
                        id: 2,
                        name: 'Cash',
                        icon: '💵',
                        balance: 800.25,
                        note: 'Dompet tunai',
                        type: 'Cash',
                        color: '#3b82f6'
                    },
                    {
                        id: 3,
                        name: 'Bank Account',
                        icon: '🏦',
                        balance: 494.25,
                        note: 'BCA ****',
                        type: 'Bank',
                        color: '#7c3aed'
                    },
                ],
                transactions: [{
                        id: 1,
                        title: 'Restaurant',
                        note: 'Makan malam team',
                        tag: 'Food',
                        amount: -60.00,
                        date: '2025-10-19',
                        detail: 'Bayar makan 3 orang',
                        last_balance: 4434.50,
                        open: false
                    },
                    {
                        id: 2,
                        title: 'Coffee & Snacks',
                        note: 'Cemilan meeting',
                        tag: 'Food',
                        amount: -45.00,
                        date: '2025-10-12',
                        detail: 'Snack meeting jam 10',
                        last_balance: 4494.50,
                        open: false
                    },
                    {
                        id: 3,
                        title: 'Cash Withdrawal',
                        note: 'Ambil tunai',
                        tag: 'Transfer',
                        amount: 200.00,
                        date: '2025-10-05',
                        detail: 'Tarik dari ATM',
                        last_balance: 4534.50,
                        open: false
                    }
                ],
                lastUpdated: new Date().toLocaleString(),

                get displayMonth() {
                    return new Intl.DateTimeFormat('id-ID', {
                        month: 'long'
                    }).format(new Date(this.selectedYear, this.selectedMonthIndex));
                },
                get displayMonthNumber() {
                    return this.selectedMonthIndex + 1;
                },
                init() {
                    this.selectedWallet = this.wallets[0].id;
                    this.$nextTick(() => this.scrollSelectedDayIntoView());
                },
                formatCurrency(v) {
                    const sign = v < 0 ? '-' : '';
                    return sign + '$' + Math.abs(v).toFixed(2);
                },
                selectWallet(id) {
                    this.selectedWallet = id
                },
                toggleSearch() {
                    this.showSearch = !this.showSearch;
                    if (!this.showSearch) this.filter = '';
                    else this.$nextTick(() => document.querySelector('input[type=search]')?.focus());
                },
                closeSearch() {
                    this.showSearch = false;
                    this.filter = '';
                },
                prevMonth() {
                    if (this.selectedMonthIndex === 0) {
                        this.selectedMonthIndex = 11;
                        this.selectedYear--;
                    } else this.selectedMonthIndex--;
                },
                nextMonth() {
                    if (this.selectedMonthIndex === 11) {
                        this.selectedMonthIndex = 0;
                        this.selectedYear++;
                    } else this.selectedMonthIndex++;
                },
                selectDay(day) {
                    this.selectedDate = day;
                    this.$nextTick(() => {
                        const el = this.$refs.dateContainer?.querySelector(`[data-day='${day}']`);
                        if (el) el.scrollIntoView({
                            behavior: 'smooth',
                            inline: 'center',
                            block: 'nearest'
                        });
                    });
                },
                scrollSelectedDayIntoView() {
                    const el = this.$refs.dateContainer?.querySelector(`[data-day='${this.selectedDate}']`);
                    if (el) el.scrollIntoView({
                        behavior: 'smooth',
                        inline: 'center',
                        block: 'nearest'
                    });
                },
                toggleDetail(i) {
                    this.transactions[i].open = !this.transactions[i].open;
                },
                get filteredTransactions() {
                    if (!this.filter) return this.transactions;
                    const q = this.filter.toLowerCase();
                    return this.transactions.filter(t =>
                        t.title.toLowerCase().includes(q) || t.note.toLowerCase().includes(q) || t.amount.toString()
                        .includes(q)
                    );
                },
            }
        }
    </script>
@endsection

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

                <!-- CARD SALDO -->
                <div class="bg-[#1f2531] dark:bg-gray-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <h3 class="text-sm font-semibold text-gray-200">Sisa Saldo</h3>

                    <div class="mt-2">
                        <p class="text-3xl sm:text-4xl font-bold text-white tracking-tight"
                            x-text="formatCurrency(totalSaldo)"></p>
                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Terakhir Di Update :
                        <span class="text-gray-300 font-medium" x-text="lastUpdated"></span>
                    </p>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="grid grid-cols-2 gap-4 mt-5 text-center select-none">
                    <!-- MASUK -->
                    <div>
                        <button @click="openModal('IN')"
                            class="w-full h-14 flex flex-col items-center justify-center bg-gray-700 hover:bg-gray-600 active:bg-gray-500 rounded-xl transition-all duration-300 hover:scale-[1.03] active:scale-[0.97] shadow-sm hover:shadow-md">
                            <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-gray-200" />
                            <span class="mt-1 text-[12px] font-medium text-gray-300">Masuk</span>
                        </button>
                    </div>

                    <!-- KELUAR -->
                    <div>
                        <button @click="openModal('OUT')"
                            class="w-full h-14 flex flex-col items-center justify-center bg-gray-700 hover:bg-gray-600 active:bg-gray-500 rounded-xl transition-all duration-300 hover:scale-[1.03] active:scale-[0.97] shadow-sm hover:shadow-md">
                            <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-gray-200" />
                            <span class="mt-1 text-[12px] font-medium text-gray-300">Keluar</span>
                        </button>
                    </div>
                </div>

                <!-- ✅ MODAL INPUT PEMBUKUAN -->
                <div x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm">

                    <div class="bg-gray-800 rounded-2xl shadow-2xl w-[90%] max-w-md p-6 relative border border-gray-700">

                        <!-- CLOSE BUTTON -->
                        <button @click="closeModal"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-200 transition">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>

                        <!-- HEADER -->
                        <div class="mb-5 text-center">
                            <h2 class="text-xl font-bold"
                                :class="modalType === 'IN' ? 'text-blue-400' : 'text-purple-400'"
                                x-text="modalType === 'IN' ? 'Tambah Pemasukan' : 'Tambah Pengeluaran'"></h2>
                            <p class="text-sm text-gray-400 mt-1">
                                Lengkapi detail pembukuan di bawah ini
                            </p>
                        </div>

                        <!-- FORM -->
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-300">Deskripsi</label>
                                <input type="text" x-model="form.deskripsi"
                                    class="w-full mt-1 rounded-lg bg-gray-700 border border-gray-600 text-sm p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-100 placeholder-gray-400"
                                    placeholder="Contoh: Penjualan Pulsa, Bayar Listrik, dll" />
                            </div>

                            <div>
                                <label class="text-sm text-gray-300">Nominal</label>
                                <input type="text"
                                    x-model="form.nominalDisplay"
                                    @input="formatNominal"
                                    inputmode="numeric"
                                    class="w-full mt-1 rounded-lg bg-gray-700 border border-gray-600 text-sm p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-100 placeholder-gray-400"
                                    placeholder="Rp Masukkan jumlah uang" />
                            </div>

                            <div>
                                <label class="text-sm text-gray-300">Wallet</label>
                                <select x-model="form.cashbook_wallet_id"
                                    class="w-full mt-1 rounded-lg bg-gray-700 border border-gray-600 text-sm p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-100">
                                    <template x-for="w in wallets.filter(w => w.id !== 0)" :key="w.id">
                                        <option :value="w.id" x-text="w.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                       <!-- ACTION BUTTONS -->
                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="closeModal"
                                class="px-4 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-all duration-200">
                                Batal
                            </button>

                            <button @click="submitTransaction"
                                :class="[
                                    modalType === 'IN' ? 'bg-blue-600 hover:bg-blue-500' : 'bg-purple-600 hover:bg-purple-500',
                                    'px-5 py-2.5 text-sm font-medium text-white rounded-lg shadow-md transition-all duration-200 hover:shadow-lg active:scale-[0.97]'
                                ]">
                                Simpan
                            </button>
                        </div>
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
                style="height: 640px;">

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
                    <input type="search" 
                        x-model="filter"
                        placeholder="Cari transaksi..."
                        class="w-full rounded-lg p-2 border border-gray-600 bg-gray-700 text-gray-200 focus:ring focus:ring-blue-400 text-sm placeholder-gray-400" />
                </div>

                <!-- WRAPPER UTAMA -->
                <div class="flex-1 flex flex-col overflow-hidden">

                    <!-- HEADER BULAN & TANGGAL (tetap statis) -->
                    <div class="flex-shrink-0 relative">
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

                            <!-- DROPDOWN YEAR -->
                            <div x-show="showDropdown"
                                x-transition:enter="transition ease-out duration-400"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute top-[100%] left-0 right-0 mt-2 bg-gray-800 rounded-xl p-5 border border-gray-700 space-y-4 shadow-lg z-40 max-h-[380px] overflow-y-auto no-scrollbar">
                                
                                <template x-for="year in years" :key="year">
                                    <div>
                                        <p class="text-xl font-bold text-gray-100 mb-3" x-text="year"></p>
                                        <div class="grid grid-cols-4 gap-3">
                                            <template x-for="m in months" :key="m.index">
                                                <button
                                                    @click="selectYear(year); selectMonth(m.index)"
                                                    class="h-10 rounded-lg text-sm font-medium transition-all duration-300"
                                                    :class="isMonthActive(m.index, year)
                                                        ? 'bg-blue-600 text-white scale-105 shadow'
                                                        : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                                    <span x-text="m.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- SLIDER MONTH -->
                            <div class="flex gap-2 overflow-x-auto no-scrollbar smooth-scroll pb-1">
                                <template x-for="m in months" :key="m.index">
                                    <button 
                                        @click="selectMonth(m.index)" 
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 ease-out"
                                        :class="selectedMonthIndex === m.index && selectedYear === currentYear
                                            ? 'bg-blue-600 text-white scale-105 shadow-md'
                                            : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                        <span x-text="m.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- DATE SLIDER -->
                        <div class="py-4 border-b border-gray-700">
                            <div x-ref="dateContainer" class="flex gap-2 overflow-x-auto no-scrollbar smooth-scroll px-1">
                                <template x-for="day in days" :key="day">
                                    <button 
                                        @click="selectDay(day)"
                                        :data-day="day"
                                        class="min-w-[48px] h-9 flex items-center justify-center rounded-full text-sm font-medium whitespace-nowrap transition-all duration-300 ease-out"
                                        :class="selectedDate === day ? 'bg-blue-600 text-white scale-105 shadow-md' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'">
                                        <span x-text="day + '/' + (selectedMonthIndex + 1)"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- LIST TRANSAKSI (ini aja yang scrollable) -->
                    <div class="flex-1 overflow-y-auto no-scrollbar mt-2 pb-3 smooth-scroll">
                        <div class="flex flex-col gap-4">
                            <template x-if="filteredTransactions.length > 0">
                                <template x-for="t in filteredTransactions" :key="t.id">
                                    <div
                                        class="bg-gray-700/90 rounded-xl p-3 transition-all duration-300 ease-out hover:scale-[1.01] hover:shadow-lg hover:bg-gray-700/95">

                                        <!-- HEADER -->
                                        <button @click="toggleTransaction(t.id)"
                                            class="w-full flex items-center justify-between text-left transition-all duration-300">
                                            <div>
                                                <p class="font-medium text-gray-100">
                                                    <span
                                                        x-text="new Date(t.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' })"></span>
                                                    -
                                                    <span x-text="t.deskripsi"></span>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <p class="text-sm font-semibold"
                                                    :class="t.type === 'IN' ? 'text-green-400' : 'text-red-400'"
                                                    x-text="formatCurrency(t.nominal)"></p>
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

                                        <!-- DETAIL (DROPDOWN) -->
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
                                                    x-text="formatCurrency(t.nominal)"></span>
                                            </div>

                                            <div class="flex justify-between">
                                                <span class="text-gray-400">Tipe</span>
                                                <span class="font-medium text-gray-100"
                                                    x-text="t.type === 'IN' ? 'Pemasukan' : 'Pengeluaran'"></span>
                                            </div>

                                            <div class="pt-3 border-t border-gray-700 flex justify-end">
                                                <button @click="requestDelete(t.id)"
                                                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-red-600/20 hover:bg-red-600/30 text-red-400 text-sm font-medium transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                    Hapus Transaksi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>

                            <!-- Jika tidak ada transaksi -->
                            <template x-if="filteredTransactions.length === 0">
                                <div
                                    class="text-center text-gray-400 text-sm mt-8 border border-gray-700 rounded-xl py-6 bg-gray-800/60">
                                    Tidak ada pembukuan di tanggal ini.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MODAL KONFIRMASI -->
            <div x-show="showConfirmModal"
                x-transition.opacity.duration.300ms
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
                x-cloak>
                
                <div @click.away="cancelDelete()"
                    class="bg-gray-800 rounded-2xl p-6 w-full max-w-sm shadow-lg border border-gray-700 transform transition-all duration-300"
                    x-transition.scale.duration.250ms>
                    
                    <h2 class="text-lg font-semibold text-white mb-2">
                        Konfirmasi Hapus
                    </h2>
                    <p class="text-gray-400 text-sm mb-5">
                        Apakah kamu yakin ingin menghapus transaksi ini? <br>
                        Tindakan ini tidak bisa dibatalkan.
                    </p>

                    <div class="flex justify-end gap-3">
                        <button @click="cancelDelete()"
                            class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium transition-all duration-200">
                            Batal
                        </button>
                        <button @click="confirmDelete()"
                            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white text-sm font-medium transition-all duration-200 shadow-md">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    function ledgerApp() {
        return {
            // === STATE ===
            showSearch: false,
            showDropdown: false,
            filter: '',
            selectedWallet: 0, // default: Semua Wallet
            selectedDate: new Date().getDate(),
            selectedMonthIndex: new Date().getMonth(),
            selectedYear: new Date().getFullYear(),
            currentYear: new Date().getFullYear(),
            days: [],
            wallets: @json($wallets),
            transactions: @json($transactions),
            years: @json($years),
            totalSaldo: {{ $totalSaldo ?? 0 }},
            lastUpdated: '{{ $lastUpdate ? $lastUpdate->format('d M Y H:i') : '-' }}',
            activeTransaction: null,

            // === LIST BULAN ===
            months: [
                { name: 'Jan', index: 0 },
                { name: 'Feb', index: 1 },
                { name: 'Mar', index: 2 },
                { name: 'Apr', index: 3 },
                { name: 'May', index: 4 },
                { name: 'Jun', index: 5 },
                { name: 'Jul', index: 6 },
                { name: 'Aug', index: 7 },
                { name: 'Sep', index: 8 },
                { name: 'Oct', index: 9 },
                { name: 'Nov', index: 10 },
                { name: 'Dec', index: 11 },
            ],
            showModal: false,
            modalType: null,
            form: {
                deskripsi: '',
                nominalRaw: 0,
                nominalDisplay: '',
                cashbook_wallet_id: '',
            },
            showConfirmModal: false,
            deleteTargetId: null,

            // === INIT ===
            init() {
                // set days sesuai bulan dan tahun saat ini
                this.updateDaysInMonth();

                // pastikan selectedDate default valid (jika hari > jumlah hari di bulan, set ke last day)
                const daysInMonth = new Date(this.selectedYear, this.selectedMonthIndex + 1, 0).getDate();
                if (this.selectedDate > daysInMonth) this.selectedDate = daysInMonth;

                // tunggu Alpine render, lalu gunakan kombinasi observer + retry fallback
                this.$nextTick(() => {
                    // 1) MutationObserver: trigger saat DOM berubah sehingga dateContainer muncul
                    let observer;
                    try {
                        observer = new MutationObserver(() => {
                            const el = this.$refs.dateContainer?.querySelector(`[data-day='${this.selectedDate}']`);
                            if (el) {
                                // scroll sekali elemen ada
                                this.scrollSelectedDayIntoView();
                                observer.disconnect();
                                if (retryTimer) clearInterval(retryTimer);
                            }
                        });
                        observer.observe(this.$el, { childList: true, subtree: true });
                    } catch (e) {
                        // ignore if MutationObserver unsupported
                    }

                    // 2) Fallback retry: jika observer gagal/terlambat, coba berkali2 selama max 2s
                    const start = Date.now();
                    const retryTimer = setInterval(() => {
                        const el = this.$refs.dateContainer?.querySelector(`[data-day='${this.selectedDate}']`);
                        if (el) {
                            this.scrollSelectedDayIntoView();
                            clearInterval(retryTimer);
                            if (observer) observer.disconnect();
                        } else if (Date.now() - start > 2000) { // timeout 2 detik
                            clearInterval(retryTimer);
                            if (observer) observer.disconnect();
                        }
                    }, 80); // cek tiap 80ms

                    // 3) as last resort, jalankan sekali lagi setelah 300ms untuk keamanan
                    setTimeout(() => {
                        const el = this.$refs.dateContainer?.querySelector(`[data-day='${this.selectedDate}']`);
                        if (el) this.scrollSelectedDayIntoView();
                    }, 300);
                });
            },

            // === UPDATE JUMLAH HARI ===
            updateDaysInMonth() {
                const daysInMonth = new Date(this.selectedYear, this.selectedMonthIndex + 1, 0).getDate();
                this.days = Array.from({ length: daysInMonth }, (_, i) => i + 1);
            },

            // === FORMAT RUPIAH ===
            formatCurrency(v) {
                if (!v || isNaN(v)) v = 0;
                return 'Rp ' + Number(v).toLocaleString('id-ID');
            },

            // === TAMPILKAN / SEMBUNYIKAN DETAIL TRANSAKSI ===
            toggleTransaction(id) {
                this.activeTransaction = this.activeTransaction === id ? null : id;
            },

            openModal(type) {
                this.modalType = type;
                this.showModal = true;
                this.form = { deskripsi: '', nominal: '', cashbook_wallet_id: this.wallets[1]?.id || '' };
            },

            closeModal() {
                this.showModal = false;
            },

            formatNominal(e) {
                // Ambil angka murni
                let raw = e.target.value.replace(/\D/g, '');
                if (raw === '') raw = '0';

                // Simpan nilai numeriknya
                this.form.nominal = parseInt(raw);

                // Format tampilan Rp 100.000
                this.form.nominalDisplay = 'Rp ' + new Intl.NumberFormat('id-ID').format(this.form.nominal);
            },  

            // === FILTER TRANSAKSI ===
            get filteredTransactions() {
                let filtered = this.transactions;

                // Filter berdasarkan wallet
                if (this.selectedWallet !== 0) {
                    filtered = filtered.filter(t => t.cashbook_wallet_id === this.selectedWallet);
                }

                // Filter berdasarkan tahun & bulan
                filtered = filtered.filter(t => {
                    const d = new Date(t.created_at);
                    return d.getFullYear() === this.selectedYear && d.getMonth() === this.selectedMonthIndex;
                });

                // Filter berdasarkan tanggal
                filtered = filtered.filter(t => {
                    const d = new Date(t.created_at);
                    return d.getDate() === this.selectedDate;
                });

                // Filter berdasarkan keyword pencarian
                if (this.filter) {
                    const q = this.filter.toLowerCase();
                    filtered = filtered.filter(t =>
                        (t.deskripsi?.toLowerCase().includes(q) || '') ||
                        t.nominal?.toString().includes(q)
                    );
                }

                return filtered;
            },

            // === EVENT HANDLER FILTER ===
            selectWallet(id) {
                this.selectedWallet = id;
            },
            selectYear(year) {
                this.selectedYear = year;
                this.updateDaysInMonth();
                this.showDropdown = false;
                this.scrollSelectedDayIntoView();
            },
            selectMonth(index) {
                this.selectedMonthIndex = index;
                this.updateDaysInMonth();
                this.showDropdown = false;
                this.scrollSelectedDayIntoView();
            },
            selectDay(day) {
                this.selectedDate = day;
                this.scrollSelectedDayIntoView();
            },

            // === DROPDOWN & SEARCH ===
            toggleSearch() {
                this.showSearch = !this.showSearch;
                if (!this.showSearch) this.filter = '';
                else this.$nextTick(() => document.querySelector('input[type=search]')?.focus());
            },
            toggleDropdown() {
                this.showDropdown = !this.showDropdown;
            },

            // === AUTO SCROLL KE TANGGAL AKTIF ===
            scrollSelectedDayIntoView() {
                // extra $nextTick supaya Alpine benar-benar menyelesaikan binding
                this.$nextTick(() => {
                    const container = this.$refs.dateContainer;
                    if (!container) return;

                    const el = container.querySelector(`[data-day='${this.selectedDate}']`);
                    if (!el) return;

                    // scroll - gunakan smooth
                    el.scrollIntoView({
                        behavior: 'smooth',
                        inline: 'center',
                        block: 'nearest'
                    });

                    // opsional: beri highlight sementara agar visual terlihat
                    el.classList.add('ring-2', 'ring-blue-400', 'ring-offset-2');
                    setTimeout(() => {
                        el.classList.remove('ring-2', 'ring-blue-400', 'ring-offset-2');
                    }, 700);
                });
            },

            async submitTransaction() {
                if (!this.form.deskripsi || !this.form.nominal) {
                    this.showToast('Harap isi semua field.', 'error');
                    return;
                }

                try {
                    const response = await fetch('{{ route('cashbook.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            deskripsi: this.form.deskripsi,
                            nominal: this.form.nominal,
                            cashbook_wallet_id: this.form.cashbook_wallet_id,
                            type: this.modalType,
                        }),
                    });

                    if (response.ok) {
                        const newData = await response.json(); // 🆕 Ambil data baru dari response

                        // 🆕 Tambahkan ke daftar transaksi
                        this.transactions.unshift({
                            id: newData.id,
                            deskripsi: newData.deskripsi,
                            nominal: parseFloat(newData.nominal),
                            type: newData.type,
                            cashbook_wallet_id: parseInt(newData.cashbook_wallet_id),
                            created_at: newData.created_at,
                        });

                        // 🆕 Update saldo total
                        if (newData.type === 'IN') {
                            this.totalSaldo += parseFloat(newData.nominal);
                        } else {
                            this.totalSaldo -= parseFloat(newData.nominal);
                        }

                        // 🆕 Update saldo wallet yang bersangkutan
                        const targetWallet = this.wallets.find(w => w.id == newData.cashbook_wallet_id);
                        if (targetWallet) {
                            const currentBalance = parseFloat(targetWallet.balance) || 0;
                            const amount = parseFloat(newData.nominal) || 0;

                            // Update saldo wallet spesifik
                            if (newData.type === 'IN') {
                                targetWallet.balance = currentBalance + amount;
                            } else {
                                targetWallet.balance = currentBalance - amount;
                            }

                            targetWallet.note = 'Aktif';
                        }

                        // 🆕 Update saldo wallet "Semua Wallet" (gabungan)
                        const mainWallet = this.wallets.find(w => w.id === 0);
                        if (mainWallet) {
                            const mainBalance = parseFloat(mainWallet.balance) || 0;
                            const amount = parseFloat(newData.nominal) || 0;
                            if (newData.type === 'IN') {
                                mainWallet.balance = mainBalance + amount;
                            } else {
                                mainWallet.balance = mainBalance - amount;
                            }
                        }

                        // 🆕 Update total saldo global
                        const totalInWallets = this.wallets
                            .filter(w => w.id !== 0)
                            .reduce((sum, w) => sum + (parseFloat(w.balance) || 0), 0);

                        this.totalSaldo = totalInWallets;
                        this.lastUpdated = new Date().toLocaleString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        });

                        this.showToast('Transaksi berhasil disimpan!', 'success');
                        this.closeModal();
                        this.form = { deskripsi: '', nominal: '', cashbook_wallet_id: '' };
                    } else {
                        this.showToast('Gagal menyimpan transaksi.', 'error');
                    }
                } catch (e) {
                    console.error('Error:', e);
                    this.showToast('Terjadi kesalahan koneksi.', 'error');
                }
            },

            // Step 1: Tampilkan modal
            requestDelete(id) {
                this.deleteTargetId = id;
                this.showConfirmModal = true;
            },

            // Step 2: Batalkan
            cancelDelete() {
                this.showConfirmModal = false;
                this.deleteTargetId = null;
            },

            // Step 3: Konfirmasi dan eksekusi delete
            async confirmDelete() {
                const id = this.deleteTargetId;
                if (!id) return;

                this.showConfirmModal = false;

                try {
                    const response = await fetch(`{{ url('cashbook') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                    });

                    if (response.ok) {
                        // Efek fade-out + hapus data
                        const card = document.querySelector(`[data-transaction-id="${id}"]`);
                        if (card) {
                            card.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-300');
                            setTimeout(() => {
                                this.transactions = this.transactions.filter(t => t.id !== id);
                                this.recalculateWallets();
                            }, 250);
                        } else {
                            this.transactions = this.transactions.filter(t => t.id !== id);
                            this.recalculateWallets();
                        }

                        this.showToast('Transaksi berhasil dihapus!', 'success');
                    } else {
                        this.showToast('Gagal menghapus transaksi.', 'error');
                    }
                } catch (e) {
                    console.error(e);
                    this.showToast('Terjadi kesalahan koneksi.', 'error');
                } finally {
                    this.deleteTargetId = null;
                }
            },

            recalculateWallets() {
                // Reset saldo semua wallet (kecuali gabungan)
                this.wallets.forEach(w => {
                    if (w.id !== 0) w.balance = 0;
                });

                // Hitung ulang berdasarkan transaksi yang tersisa
                this.transactions.forEach(t => {
                    const wallet = this.wallets.find(w => w.id === t.cashbook_wallet_id);
                    if (wallet) {
                        const nominal = parseFloat(t.nominal) || 0;
                        wallet.balance += (t.type === 'IN' ? nominal : -nominal);
                    }
                });

                // 🔹 Hitung total semua wallet aktif (bukan gabungan)
                const total = this.wallets
                    .filter(w => w.id !== 0)
                    .reduce((sum, w) => sum + (parseFloat(w.balance) || 0), 0);

                // 🔹 Update totalSaldo global
                this.totalSaldo = total;

                // 🔹 Update wallet gabungan (id: 0)
                const mainWallet = this.wallets.find(w => w.id === 0);
                if (mainWallet) {
                    mainWallet.balance = total;
                }

                // 🔹 Update waktu terakhir
                this.lastUpdated = new Date().toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.textContent = message;
                toast.className = `
                    fixed bottom-5 right-5 px-4 py-3 rounded-lg shadow-lg text-sm font-medium
                    text-white z-50 transition-all duration-500 transform
                    ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
                    translate-y-5 opacity-0
                `;

                document.body.appendChild(toast);

                // animasi masuk
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-y-5', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                });

                // animasi keluar
                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-5', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 2500);
            },

            // === UTILITAS ===
            isMonthActive(monthIndex, year) {
                return this.selectedMonthIndex === monthIndex && this.selectedYear === year;
            },
            get displayMonth() {
                return new Intl.DateTimeFormat('id-ID', { month: 'long' })
                    .format(new Date(this.selectedYear, this.selectedMonthIndex));
            },
        };
    }
    </script>

@endsection

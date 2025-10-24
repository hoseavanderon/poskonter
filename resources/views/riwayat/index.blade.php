@extends('layouts.app')

@section('content')
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

            <!-- FLOATING DROPDOWN -->
            <div x-show="showDropdown" @click.outside="showDropdown=false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="absolute top-[52px] right-0 bg-gray-800 border border-gray-700 rounded-xl p-5 w-72 shadow-2xl z-50 space-y-4">
                <template x-for="year in [2025,2024]" :key="year">
                    <div>
                        <p class="text-lg font-semibold text-gray-100 mb-3" x-text="year"></p>
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

        <!-- DATE RANGE FILTER (CENTERED) -->
        <div class="flex justify-center items-center gap-3 mb-6">
            <input type="date" x-model="fromDate"
                class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-gray-200 focus:ring-1 focus:ring-blue-400 placeholder-gray-500"
                placeholder="Dari Tanggal">
            <span class="text-gray-400">—</span>
            <input type="date" x-model="toDate"
                class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-gray-200 focus:ring-1 focus:ring-blue-400 placeholder-gray-500"
                placeholder="Sampai Tanggal">
        </div>

        <!-- DATE SLIDER -->
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-4 mb-4 smooth-scroll">
            <template x-for="day in days" :key="day">
                <button @click="selectedDate = day"
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
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 space-y-4">
                <template x-for="a in apps" :key="a.name">
                    <div class="flex items-center justify-between text-sm text-gray-300">
                        <div class="flex items-center gap-2">
                            <img :src="a.logo" alt="" class="w-6 h-6 rounded-md">
                            <span x-text="a.name"></span>
                        </div>
                        <span class="font-medium" x-text="formatCurrency(a.amount)"></span>
                    </div>
                </template>

                <hr class="border-gray-700 my-2">

                <div class="space-y-1 text-sm text-gray-300">
                    <div class="flex justify-between">
                        <span>Total</span>
                        <span class="font-medium" x-text="formatCurrency(total)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Lebih</span>
                        <span class="font-medium" x-text="formatCurrency(extra)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Grand Total</span>
                        <span class="font-semibold text-blue-400" x-text="formatCurrency(grandTotal)"></span>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="flex flex-col gap-5">

                <!-- CATEGORY SUMMARY -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <template x-for="c in categories" :key="c.name">
                            <div class="bg-gray-700/60 rounded-xl py-3 transition hover:bg-gray-700/90">
                                <p class="text-sm font-semibold text-gray-100" x-text="c.name"></p>
                                <p class="text-sm text-gray-400" x-text="c.value + ' pcs'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- PRODUCT + DIGITAL HISTORY -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- PRODUCT HISTORY -->
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Riwayat Transaksi Produk</h3>
                        <template x-for="t in productTransactions" :key="t.id">
                            <div
                                class="bg-gray-700/80 rounded-xl p-3 mb-2 flex flex-col gap-1 text-sm text-gray-200 transition hover:scale-[1.01] hover:bg-gray-700/95 duration-200">
                                <div class="flex justify-between font-medium">
                                    <span x-text="t.name"></span>
                                    <span x-text="formatCurrency(t.amount)"></span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span x-text="t.qty + ' pcs'"></span>
                                    <span x-text="t.date"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- DIGITAL HISTORY -->
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-200 mb-3">Riwayat Transaksi Produk Digital</h3>
                        <template x-for="t in digitalTransactions" :key="t.id">
                            <div
                                class="bg-gray-700/80 rounded-xl p-3 mb-2 flex flex-col gap-1 text-sm text-gray-200 transition hover:scale-[1.01] hover:bg-gray-700/95 duration-200">
                                <div class="flex justify-between font-medium">
                                    <span x-text="t.name"></span>
                                    <span x-text="formatCurrency(t.amount)"></span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span x-text="t.qty + ' pcs'"></span>
                                    <span x-text="t.date"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function transactionHistory() {
            return {
                showDropdown: false,
                selectedMonth: 'Oct',
                selectedDate: new Date().getDate(),
                fromDate: '',
                toDate: '',
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                days: Array.from({
                    length: 30
                }, (_, i) => i + 1),
                apps: [{
                        name: 'LS-Reload',
                        logo: '/images/lsreload.png',
                        amount: 123000
                    },
                    {
                        name: 'Digipos',
                        logo: '/images/digipos.png',
                        amount: 123000
                    },
                    {
                        name: 'Mitra Tokopedia',
                        logo: '/images/mitra.png',
                        amount: 89000
                    },
                ],
                categories: [{
                        name: 'Vcr',
                        value: 6
                    },
                    {
                        name: 'Kartu',
                        value: 10
                    },
                    {
                        name: 'Token',
                        value: 3
                    },
                    {
                        name: 'Paket',
                        value: 5
                    },
                ],
                productTransactions: [{
                        id: 1,
                        name: 'Pulpen',
                        qty: 3,
                        amount: 15000,
                        date: '2025-10-21'
                    },
                    {
                        id: 2,
                        name: 'Buku Tulis',
                        qty: 5,
                        amount: 24000,
                        date: '2025-10-22'
                    },
                ],
                digitalTransactions: [{
                        id: 1,
                        name: 'Voucher Game',
                        qty: 2,
                        amount: 50000,
                        date: '2025-10-21'
                    },
                    {
                        id: 2,
                        name: 'Pulsa Telkomsel',
                        qty: 1,
                        amount: 25000,
                        date: '2025-10-22'
                    },
                ],
                total: 1000000,
                extra: 1000000,
                get grandTotal() {
                    return this.total + this.extra
                },
                toggleDropdown() {
                    this.showDropdown = !this.showDropdown
                },
                selectMonth(m) {
                    this.selectedMonth = m;
                    this.showDropdown = false
                },
                get selectedMonthNumber() {
                    const idx = this.months.indexOf(this.selectedMonth) + 1;
                    return idx.toString().padStart(2, '0');
                },
                formatCurrency(v) {
                    return 'Rp ' + v.toLocaleString('id-ID')
                },
                init() {},
            }
        }
    </script>
@endsection

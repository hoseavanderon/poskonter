@extends('layouts.admin')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* 🔥 Modern Dark Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.3);
            /* abu soft */
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.6);
        }

        /* sembunyiin scrollbar tapi tetap bisa scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE & Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* remove spinner (just in case) */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .category-item {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 16px;

            display: flex;
            flex-direction: column;
            align-items: center;
            /* ✅ center horizontal */
            justify-content: center;
            /* ✅ center vertical */
            text-align: center;

            opacity: 0;
            transform: translateY(20px) scale(0.95);

            transition: all 0.3s ease;
        }

        .category-item:hover {
            transform: translateY(-4px) scale(1.05);
            background: #334155;
        }

        .icon {
            width: 22px;
            height: 22px;
            margin-bottom: 8px;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 1.5;
        }

        .title {
            font-size: 13px;
            color: #cbd5e1;
        }

        .value {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        /* animasi lebih smooth */
        @keyframes fadeUpScale {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
    <div class="relative min-h-[300px] overflow-x-hidden">
        <!-- 🏠 HOME -->
        <div x-show="page === 'home'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="pb-24 space-y-6 text-xs md:text-sm">

            <!-- 🔥 ALL OUTLETS -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-base md:text-lg lg:text-xl flex items-center gap-2">
                        <!-- icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        <span x-text="selected === 'all' ? 'All Outlets Combined' : activeOutletName"></span>
                    </h2>
                    <span 
                        class="text-xs"
                        :class="stats.growth >= 0 ? 'text-green-400' : 'text-red-400'"
                        x-text="(stats.growth >= 0 ? '+' : '') + stats.growth + '%'">
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs md:text-sm">
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Today's Sales</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl">
                            <span x-text="'Rp ' + stats.todaySales.toLocaleString('id-ID')"></span>
                        </p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Transactions</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl">
                            <span x-text="stats.totalTransactions"></span>
                        </p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Monthly Sales</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl"
                            x-text="'Rp ' + stats.monthlySales.toLocaleString('id-ID')">
                        </p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Total Profit</p>

                        <p class="font-semibold text-sm md:text-lg lg:text-xl"
                        x-text="'Rp ' + stats.todayProfit.toLocaleString('id-ID')">
                        </p>
                    </div>

                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Total Profit</p>

                        <p class="font-semibold text-sm md:text-lg lg:text-xl"
                        x-text="'Rp ' + stats.todayProfit.toLocaleString('id-ID')">
                        </p>
                    </div>

                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Total Profit</p>

                        <p class="font-semibold text-sm md:text-lg lg:text-xl"
                        x-text="'Rp ' + stats.todayProfit.toLocaleString('id-ID')">
                        </p>
                    </div>
                </div>
            </div>

            <!-- 🔥 SANTUY + LIVE -->
            <div class="grid md:grid-cols-2 gap-4">

                <!-- 🟦 SANTUY CELL -->
                <div class="bg-[#111827] rounded-2xl p-4 md:p-5 border border-white/5">

                    <!-- HEADER -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-base md:text-lg lg:text-xl tracking-tight">
                            Santuy Cell
                        </h3>

                        <div class="flex items-center gap-2">
                            <span class="text-white/70 text-xs md:text-sm font-medium">+12.5%</span>

                            <!-- 🔥 BUTTON DETAIL -->
                            <button @click="page = 'insight'"
                                class="text-[10px] md:text-xs px-2.5 py-1 rounded-lg 
                       bg-white/5 hover:bg-white/10 
                       text-gray-300 hover:text-white 
                       transition-all duration-200">
                                Detail
                            </button>
                        </div>
                    </div>

                    <!-- STATS -->
                    <div class="grid grid-cols-2 gap-3 text-xs md:text-sm">

                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 tracking-wide text-[10px] md:text-xs">Today's Sales</p>
                            <p class="font-medium text-sm md:text-lg lg:text-xl tracking-tight">
                                Rp 45.800.000
                            </p>
                        </div>

                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 tracking-wide text-[10px] md:text-xs">Transactions</p>
                            <p class="font-medium text-sm md:text-lg lg:text-xl tracking-tight">
                                147
                            </p>
                        </div>

                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 tracking-wide text-[10px] md:text-xs">Monthly</p>
                            <p class="font-medium text-sm md:text-lg lg:text-xl tracking-tight">
                                Rp 892.000.000
                            </p>
                        </div>

                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 tracking-wide text-[10px] md:text-xs">Profit</p>
                            <p class="font-medium text-sm md:text-lg lg:text-xl tracking-tight">
                                Rp 12.400.000
                            </p>
                        </div>

                    </div>

                    <!-- CHART -->
                    <div class="mt-4 bg-black/20 border border-white/5 rounded-xl p-3 md:p-4">

                        <!-- HEADER -->
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-[10px] md:text-xs lg:text-sm text-gray-400 tracking-wide">
                                7-Day Sales Trend
                            </p>
                        </div>

                        <!-- BAR CHART -->
                        <div class="flex items-end justify-between h-16 md:h-20 gap-2">

                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:30%"></div>
                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:45%"></div>
                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:25%"></div>
                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:60%"></div>
                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:50%"></div>
                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:70%"></div>
                            <div class="w-full bg-white/30 hover:bg-white/60 rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:45%"></div>

                        </div>

                    </div>

                </div>

                <!-- 🟩 LIVE ACTIVITY -->
                <div class="bg-[#111827] rounded-2xl p-4 border border-white/5">

                    <!-- TITLE -->
                    <h3 class="font-semibold text-base md:text-lg lg:text-xl mb-4 flex items-center gap-2 text-white">
                        <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M4 12h4l2-5 4 10 2-5h4" />
                        </svg>
                        Live Activity
                    </h3>

                    <div class="space-y-3 text-xs md:text-sm">

                        <!-- ITEM -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <!-- LEFT -->
                            <div class="flex items-center gap-3">

                                <!-- ICON -->
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M3 7h18M5 7l1-2h12l1 2M6 7v13h12V7" />
                                    </svg>
                                </div>

                                <!-- TEXT -->
                                <div>
                                    <p class="font-medium text-white">iPhone 15 Pro Max</p>
                                    <p class="text-gray-400 text-[10px] md:text-xs">
                                        Santuy Cell • 2 min ago
                                    </p>
                                </div>

                            </div>

                            <!-- PRICE -->
                            <p class="text-white/70 font-semibold text-sm md:text-lg">
                                Rp 8.500.000
                            </p>
                        </div>

                        <!-- ITEM -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <div class="flex items-center gap-3">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 7h18M5 7l1-2h12l1 2M6 7v13h12V7" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-medium text-white">Samsung Galaxy S24</p>
                                    <p class="text-gray-400 text-[10px] md:text-xs">
                                        Tian Cell • 5 min ago
                                    </p>
                                </div>
                            </div>

                            <p class="text-white/70 font-semibold text-sm md:text-lg">
                                Rp 4.200.000
                            </p>
                        </div>

                        <!-- ITEM -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <div class="flex items-center gap-3">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 7h18M5 7l1-2h12l1 2M6 7v13h12V7" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-medium text-white">AirPods Pro 2nd Gen</p>
                                    <p class="text-gray-400 text-[10px] md:text-xs">
                                        Santuy Cell • 12 min ago
                                    </p>
                                </div>
                            </div>

                            <p class="text-white/70 font-semibold text-sm md:text-lg">
                                Rp 2.100.000
                            </p>
                        </div>

                        <!-- ITEM (RESTOCK - beda icon biru) -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <div class="flex items-center gap-3">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4 8 4 8-4zM4 11l8 4 8-4M4 15l8 4 8-4" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-medium text-white">Restocked iPhone accessories</p>
                                    <p class="text-gray-400 text-[10px] md:text-xs">
                                        Tian Cell • 18 min ago
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- 🛒 PRODUK -->
        <div x-show="page === 'product'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="min-h-screen overflow-y-auto p-4 md:p-6 space-y-6 bg-[#020617] text-white">

            <!-- HEADER -->
            <div class="flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
                <div>
                    <h1 class="text-lg md:text-2xl font-semibold tracking-tight">
                        Info Produk Outlet
                    </h1>
                    <p class="text-gray-400 text-xs md:text-sm">
                        Monitor Produk Laris dan Produk Habis
                    </p>
                </div>

                <div x-data="{
                    active: 'year',
                    move(el) {
                        const indicator = this.$refs.indicator
                        if (!el || !indicator) return
                
                        indicator.style.width = el.offsetWidth + 'px'
                        indicator.style.left = el.offsetLeft + 'px'
                    },
                    init() {
                        // initial (kalau kebetulan sudah visible)
                        this.$nextTick(() => {
                            setTimeout(() => this.move(this.$refs.year), 50)
                        })
                
                        // 🔥 FIX UTAMA
                        this.$watch('page', value => {
                            if (value === 'product') {
                                setTimeout(() => {
                                    this.move(this.$refs.year)
                                }, 50)
                            }
                        })
                    }
                }" x-init="init()" class="relative overflow-x-auto">

                    <div class="flex gap-6 relative min-w-max">

                        <!-- INDICATOR -->
                        <span x-ref="indicator"
                            class="absolute bottom-0 h-[2px] bg-white/70 transition-all duration-300 ease-out">
                        </span>

                        <!-- YEAR -->
                        <button x-ref="year" @click="active='year'; move($el)"
                            :class="active === 'year' ? 'text-white' : 'text-gray-400'"
                            class="pb-2 text-xs whitespace-nowrap transition">
                            Year
                        </button>

                        <!-- MONTH -->
                        <button x-ref="month" @click="active='month'; move($el)"
                            :class="active === 'month' ? 'text-white' : 'text-gray-400'"
                            class="pb-2 text-xs whitespace-nowrap transition">
                            Month
                        </button>

                        <!-- TODAY -->
                        <button x-ref="today" @click="active='today'; move($el)"
                            :class="active === 'today' ? 'text-white' : 'text-gray-400'"
                            class="pb-2 text-xs whitespace-nowrap transition">
                            Today
                        </button>

                    </div>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="grid md:grid-cols-2 gap-5">

                <!-- 🔴 LOW STOCK -->
                <div class="space-y-4" x-data="{
                    active: 'all',
                    move(el) {
                        const indicator = this.$refs.indicator
                        if (!el || !indicator) return
                        indicator.style.width = el.offsetWidth + 'px'
                        indicator.style.left = el.offsetLeft + 'px'
                    },
                    init() {
                        this.$nextTick(() => {
                            setTimeout(() => this.move(this.$refs.all), 50)
                        })
                
                        this.$watch('page', val => {
                            if (val === 'product') {
                                setTimeout(() => this.move(this.$refs.all), 50)
                            }
                        })
                    }
                }" x-init="init()">

                    <!-- TITLE -->
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white-70" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 9v2m0 4h.01M10.29 3.86l-7.09 12.3A1 1 0 004.09 18h15.82a1 1 0 00.89-1.84l-7.09-12.3a1 1 0 00-1.78 0z" />
                        </svg>
                        <h2 class="font-semibold text-sm">Stok Habis</h2>
                    </div>

                    <div
                        class="relative max-w-full overflow-x-auto no-scrollbar touch-pan-x overscroll-x-contain snap-x snap-mandatory scroll-smooth">

                        <div class="flex gap-4 whitespace-nowrap px-1 relative">

                            <!-- INDICATOR -->
                            <span x-ref="indicator"
                                class="absolute bottom-0 h-[2px] bg-white/70 transition-all duration-300">
                            </span>

                            <!-- BUTTON -->
                            <button x-ref="all" @click="active='all'; move($el)"
                                :class="active === 'all' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0 snap-start">
                                All
                            </button>

                            <button @click="active='smartphone'; move($el)"
                                :class="active === 'smartphone' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0 snap-start">
                                Smartphone
                            </button>

                            <button @click="active='accessories'; move($el)"
                                :class="active === 'accessories' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0 snap-start">
                                Accessories
                            </button>

                            <button @click="active='tablet'; move($el)"
                                :class="active === 'tablet' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0 snap-start">
                                Tablet
                            </button>

                            <button @click="active='laptop'; move($el)"
                                :class="active === 'laptop' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0 snap-start">
                                Laptop
                            </button>
                        </div>
                    </div>

                    <!-- CARD -->
                    <div class="space-y-3 overflow-x-hidden touch-pan-y overscroll-y-contain">

                        <!-- SMARTPHONE -->
                        <div x-show="active==='all'||active==='smartphone'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path
                                            d="M12 18h.01M8 2h8a2 2 0 012 2v16a2 2 0 01-2 2H8a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Samsung S24 Ultra</p>
                                    <p class="text-xs text-gray-400">Smartphone</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                3 left
                            </span>
                        </div>


                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M13 7h8m0 0v8m0-8L10 18l-4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">USB-C Cable</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                8 left
                            </span>
                        </div>


                        <!-- TABLET -->
                        <div x-show="active==='all'||active==='tablet'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="4" y="3" width="16" height="18" rx="2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">iPad Air M2</p>
                                    <p class="text-xs text-gray-400">Tablet</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                1 left
                            </span>
                        </div>


                        <!-- LAPTOP -->
                        <div x-show="active==='all'||active==='laptop'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="12" rx="2" />
                                        <path d="M2 20h20" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">MacBook Air M2</p>
                                    <p class="text-xs text-gray-400">Laptop</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                2 left
                            </span>
                        </div>

                    </div>

                </div>


                <!-- 🟢 BEST SELLER -->
                <div class="space-y-4" x-data="{
                    active: 'all',
                    move(el) {
                        const indicator = this.$refs.indicator
                        if (!el || !indicator) return
                
                        indicator.style.width = el.offsetWidth + 'px'
                        indicator.style.left = el.offsetLeft + 'px'
                
                        // 🔥 auto scroll ke tengah
                        el.scrollIntoView({
                            behavior: 'smooth',
                            inline: 'center',
                            block: 'nearest'
                        })
                    },
                    init() {
                        this.$nextTick(() => {
                            setTimeout(() => this.move(this.$refs.all), 50)
                        })
                    }
                }" x-init="init()">

                    <!-- TITLE -->
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M3 17l6-6 4 4 7-7" />
                        </svg>
                        <h2 class="font-semibold text-sm">Produk Terlaris</h2>
                    </div>

                    <div
                        class="relative max-w-full overflow-x-auto no-scrollbar touch-pan-x overscroll-x-contain snap-x snap-mandatory scroll-smooth">

                        <div class="flex gap-4 whitespace-nowrap px-1 relative">

                            <!-- INDICATOR -->
                            <span x-ref="indicator"
                                class="absolute bottom-0 h-[2px] bg-white/70 transition-all duration-300">
                            </span>

                            <!-- CATEGORY -->
                            <button x-ref="all" @click="active='all'; move($el)"
                                :class="active === 'all' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0">
                                All
                            </button>

                            <button @click="active='smartphone'; move($el)"
                                :class="active === 'smartphone' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0">
                                Smartphone
                            </button>

                            <button @click="active='accessories'; move($el)"
                                :class="active === 'accessories' ? 'text-white' : 'text-gray-400'"
                                class="pb-2 text-xs flex-shrink-0">
                                Accessories
                            </button>

                        </div>
                    </div>

                    <div class="space-y-3">

                        <!-- SMARTPHONE -->
                        <div x-show="active==='all'||active==='smartphone'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path
                                            d="M12 18h.01M8 2h8a2 2 0 012 2v16a2 2 0 01-2 2H8a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-medium">iPhone 15 Pro</p>
                                    <p class="text-xs text-gray-400">Smartphone</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                120 sold
                            </span>
                        </div>


                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 18V5l12-2v13" />
                                        <circle cx="6" cy="18" r="3" />
                                        <circle cx="18" cy="16" r="3" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-medium">AirPods Pro</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 18V5l12-2v13" />
                                        <circle cx="6" cy="18" r="3" />
                                        <circle cx="18" cy="16" r="3" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-medium">AirPods Pro</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 18V5l12-2v13" />
                                        <circle cx="6" cy="18" r="3" />
                                        <circle cx="18" cy="16" r="3" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-medium">AirPods Pro</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 18V5l12-2v13" />
                                        <circle cx="6" cy="18" r="3" />
                                        <circle cx="18" cy="16" r="3" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-medium">AirPods Pro</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/5 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 18V5l12-2v13" />
                                        <circle cx="6" cy="18" r="3" />
                                        <circle cx="18" cy="16" r="3" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-medium">AirPods Pro</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-white/5 text-white/70 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- 📊 DETAIL -->
        <div x-show="page === 'detail'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="min-h-screen bg-[#020617] text-white p-4 space-y-6">

            <div class="px-1 py-4 space-y-4">

                <!-- TOTAL ASSET -->
                <div>
                    <div class="flex items-center gap-2">
                        <!-- Wallet Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2v-5" />
                        </svg>

                        <p class="text-[11px] text-gray-400/60 tracking-wide font-medium">
                            Santuy Cell's Asset
                        </p>
                    </div>

                    <h1 class="text-3xl font-semibold text-white mt-2 tracking-tight">
                        Rp 2.456.800.000
                    </h1>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">

                <!-- FULL CARD -->
                <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 shadow w-full">

                    <h2 class="text-sm text-slate-400 mb-6">Product Categories</h2>

                    <!-- GRID CATEGORY -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">

                        <!-- Mobile Phones -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M7 4h10v16H7z" />
                            </svg>
                            <span class="title">Mobile Phones</span>
                            <span class="value">128 pcs</span>
                        </div>

                        <!-- SIM Cards -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M6 2h9l5 5v15H6z" />
                            </svg>
                            <span class="title">SIM Cards</span>
                            <span class="value">342 pcs</span>
                        </div>

                        <!-- Vouchers -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M4 7h16v10H4z" />
                            </svg>
                            <span class="title">Vouchers</span>
                            <span class="value">580 pcs</span>
                        </div>

                        <!-- Cables -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M6 12h12M12 6v12" />
                            </svg>
                            <span class="title">Cables</span>
                            <span class="value">215 pcs</span>
                        </div>

                        <!-- Earphones -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M6 18v-6a6 6 0 0112 0v6" />
                            </svg>
                            <span class="title">Earphones</span>
                            <span class="value">96 pcs</span>
                        </div>

                        <!-- Power Bank -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M7 7h10v10H7zM17 10h2v4h-2z" />
                            </svg>
                            <span class="title">Power Bank</span>
                            <span class="value">64 pcs</span>
                        </div>

                        <!-- Screen Guard -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M5 4h14v16H5z" />
                            </svg>
                            <span class="title">Screen Guard</span>
                            <span class="value">410 pcs</span>
                        </div>

                        <!-- Phone Case -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M8 2h8v20H8z" />
                            </svg>
                            <span class="title">Phone Case</span>
                            <span class="value">375 pcs</span>
                        </div>

                        <!-- Bluetooth -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M6 8l6 4-6 4V8zm6-4v16l6-8-6-8z" />
                            </svg>
                            <span class="title">Bluetooth</span>
                            <span class="value">52 pcs</span>
                        </div>

                        <!-- Charger -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M9 2v6h6V2M7 8h10v14H7z" />
                            </svg>
                            <span class="title">Charger</span>
                            <span class="value">188 pcs</span>
                        </div>

                        <!-- Memory Card -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M6 2h8l4 4v16H6z" />
                            </svg>
                            <span class="title">Memory Card</span>
                            <span class="value">230 pcs</span>
                        </div>

                        <!-- Smartwatch -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M8 2h8l1 4H7l1-4zm0 16h8l1 4H7l1-4zM7 6h10v12H7z" />
                            </svg>
                            <span class="title">Smartwatch</span>
                            <span class="value">38 pcs</span>
                        </div>

                        <!-- Speaker -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M5 8h6l5-4v16l-5-4H5z" />
                            </svg>
                            <span class="title">Speaker</span>
                            <span class="value">72 pcs</span>
                        </div>

                        <!-- Tablet -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M4 3h16v18H4z" />
                            </svg>
                            <span class="title">Tablet</span>
                            <span class="value">45 pcs</span>
                        </div>

                        <!-- Adapter -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M8 7h8v10H8zM10 2v5M14 2v5" />
                            </svg>
                            <span class="title">Adapter</span>
                            <span class="value">33 pcs</span>
                        </div>

                        <!-- Others -->
                        <div class="category-item">
                            <svg class="icon" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <span class="title">Others</span>
                            <span class="value">20 pcs</span>
                        </div>

                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 📊 BEST SELLING PERIOD -->
                <div x-data="{
                    tab: 'year',
                    data: {
                        year: [30, 50, 40, 60, 45, 70, 55, 50, 65, 75, 60, 80],
                        month: [10, 20, 15, 25, 18, 30, 22],
                        week: [5, 10, 8, 12, 9, 14, 11]
                    }
                }" class="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-4">

                    <!-- HEADER -->
                    <div class="flex items-center justify-between">

                        <!-- LEFT -->
                        <div class="flex items-center gap-2">

                            <!-- Icon (monochrome) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/70" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3v18h18M7 13v4M12 9v8M17 5v12" />
                            </svg>

                            <p class="text-sm font-medium text-white/90">
                                Best Selling Period
                            </p>
                        </div>

                        <!-- TABS -->
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">

                            <button @click="tab='year'"
                                :class="tab === 'year'
                                    ?
                                    'bg-white/10 text-white' :
                                    'text-white/40 hover:text-white/70'"
                                class="px-3 py-1 rounded-lg transition">
                                Year
                            </button>

                            <button @click="tab='month'"
                                :class="tab === 'month'
                                    ?
                                    'bg-white/10 text-white' :
                                    'text-white/40 hover:text-white/70'"
                                class="px-3 py-1 rounded-lg transition">
                                Month
                            </button>

                            <button @click="tab='week'"
                                :class="tab === 'week'
                                    ?
                                    'bg-white/10 text-white' :
                                    'text-white/40 hover:text-white/70'"
                                class="px-3 py-1 rounded-lg transition">
                                Week
                            </button>

                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="flex justify-between items-end gap-4">

                        <!-- LEFT INFO -->
                        <div>
                            <h2 class="text-2xl font-semibold text-white/90"
                                x-text="tab === 'year' ? '2025' : tab === 'month' ? 'July' : 'This Week'">
                            </h2>

                            <div class="mt-3 space-y-1 text-sm">

                                <p class="text-white/40">
                                    Transactions
                                    <span class="text-white/80 font-medium ml-2"
                                        x-text="tab === 'year' ? '1840' : tab === 'month' ? '420' : '98'">
                                    </span>
                                </p>

                                <p class="text-white/40">
                                    Revenue
                                    <span class="text-white font-medium ml-2 tabular-nums"
                                        x-text="tab === 'year' ? 'Rp 1.200.000.000' : tab === 'month' ? 'Rp 320.000.000' : 'Rp 75.000.000'">
                                    </span>
                                </p>

                            </div>
                        </div>

                        <!-- CHART -->
                        <div class="flex items-end gap-1 h-20">
                            <template x-for="(val, i) in data[tab]" :key="i">
                                <div class="w-2 rounded bg-white/30 transition-all duration-500 hover:bg-white/60"
                                    :style="'height:' + val + 'px'">
                                </div>
                            </template>
                        </div>

                    </div>

                </div>

                <!-- 🕒 JAM RAME (MONOCHROME) -->
                <div x-data="{
                    tab: 'today',
                    data: {
                        today: [4, 8, 12, 18, 25, 35, 50, 40, 22, 15, 28, 32, 20, 12, 8, 4],
                        month: [10, 20, 30, 25, 35, 45, 40, 50, 38, 28, 32, 36],
                        year: [15, 25, 35, 30, 40, 55, 45, 60, 50, 35, 30, 40]
                    },
                    labels: {
                        today: ['06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21'],
                        month: ['W1', 'W2', 'W3', 'W4', 'W5', 'W6', 'W7', 'W8', 'W9', 'W10', 'W11', 'W12'],
                        year: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    }
                }" class="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-4">

                    <!-- HEADER -->
                    <div class="flex justify-between items-center">

                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M12 6v6l4 2M6 2h12v4H6zM6 22h12v-4H6z" />
                            </svg>
                            <p class="text-sm text-white/90 font-medium">Jam Rame</p>
                        </div>

                        <!-- TABS -->
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">
                            <button @click="tab='year'"
                                :class="tab === 'year' ? 'bg-white/10 text-white' : 'text-white/40 hover:text-white/70'"
                                class="px-3 py-1 rounded-lg transition">
                                Year
                            </button>
                            <button @click="tab='month'"
                                :class="tab === 'month' ? 'bg-white/10 text-white' : 'text-white/40 hover:text-white/70'"
                                class="px-3 py-1 rounded-lg transition">
                                Month
                            </button>
                            <button @click="tab='today'"
                                :class="tab === 'today' ? 'bg-white/10 text-white' : 'text-white/40 hover:text-white/70'"
                                class="px-3 py-1 rounded-lg transition">
                                Today
                            </button>
                        </div>
                    </div>

                    <!-- PEAK -->
                    <div>
                        <span class="text-xs bg-white/5 text-white/70 px-3 py-1 rounded-full">
                            Peak: 12:00
                        </span>
                    </div>

                    <!-- CHART -->
                    <div class="flex justify-center">
                        <div class="flex items-end gap-2 h-32 w-full max-w-md">

                            <template x-for="(val, i) in data[tab]" :key="i">
                                <div class="flex flex-col items-center justify-end h-full">

                                    <!-- BAR -->
                                    <div class="w-3 rounded bg-white/30 hover:bg-white/60 transition-all duration-500"
                                        :style="'height:' + val + 'px'">
                                    </div>

                                    <!-- LABEL -->
                                    <span class="text-[10px] text-white/40 mt-1" x-text="labels[tab][i]">
                                    </span>

                                </div>
                            </template>

                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                <!-- 💰 PROFIT OVERVIEW -->
                <div x-data="{
                    tab: 'month',
                    data: {
                        year: [40, 60, 55, 70, 65, 80, 75, 90, 85, 95, 88, 100],
                        month: [20, 35, 50, 45],
                        week: [10, 15, 20, 18, 25, 30, 28],
                        today: [5, 10, 15, 12, 18, 22]
                    },
                    profit: {
                        year: 'Rp 2.400.000.000',
                        month: 'Rp 182.400.000',
                        week: 'Rp 48.200.000',
                        today: 'Rp 8.500.000'
                    }
                }" class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-5">

                    <!-- HEADER -->
                    <div class="flex justify-between items-center">

                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M3 17l6-6 4 4 8-8" />
                            </svg>
                            <p class="text-sm font-medium text-white/90">Profit Overview</p>
                        </div>

                        <!-- TABS -->
                        <div class="bg-white/5 rounded-xl p-1 flex text-xs">
                            <button @click="tab='year'"
                                :class="tab === 'year' ? 'bg-white/10 text-white' : 'text-white/40'"
                                class="px-3 py-1 rounded-lg transition">Year</button>
                            <button @click="tab='month'"
                                :class="tab === 'month' ? 'bg-white/10 text-white' : 'text-white/40'"
                                class="px-3 py-1 rounded-lg transition">Month</button>
                            <button @click="tab='week'"
                                :class="tab === 'week' ? 'bg-white/10 text-white' : 'text-white/40'"
                                class="px-3 py-1 rounded-lg transition">Week</button>
                            <button @click="tab='today'"
                                :class="tab === 'today' ? 'bg-white/10 text-white' : 'text-white/40'"
                                class="px-3 py-1 rounded-lg transition">Today</button>
                        </div>

                    </div>

                    <!-- CONTENT -->
                    <div class="flex justify-between items-end">

                        <!-- LEFT -->
                        <div>
                            <h2 class="text-2xl font-semibold text-white" x-text="profit[tab]"></h2>

                            <div class="mt-4 text-sm space-y-1">
                                <p class="text-white/40">
                                    Transactions
                                    <span class="text-white ml-2">2.340</span>
                                </p>
                                <p class="text-white/40">
                                    Avg / Txn
                                    <span class="text-white ml-2">Rp 77.900</span>
                                </p>
                            </div>
                        </div>

                        <!-- CHART -->
                        <div class="flex items-end gap-2 h-20">
                            <template x-for="(val,i) in data[tab]" :key="i">
                                <div class="w-3 rounded bg-white/30 hover:bg-white/60 transition-all duration-500"
                                    :style="'height:' + val + 'px'">
                                </div>
                            </template>
                        </div>

                    </div>

                </div>


                <!-- 🧠 SMART INSIGHTS -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-4">

                    <!-- HEADER -->
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12c.5.5 1 1.5 1 2h6c0-.5.5-1.5 1-2a7 7 0 00-4-12z" />
                        </svg>
                        <p class="text-sm font-medium text-white/90">Smart Insights</p>
                    </div>

                    <!-- LIST -->
                    <div class="space-y-3 text-sm">

                        <!-- ITEM -->
                        <div class="flex items-start gap-3 bg-white/5 rounded-xl p-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10">
                                ↑
                            </div>
                            <div>
                                <p class="text-white">Accessories sales increased <span class="font-semibold">+32%</span>
                                </p>
                                <p class="text-white/40 text-xs">vs last week</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 rounded-xl p-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10">
                                ↓
                            </div>
                            <div>
                                <p class="text-white">Sales dropped <span class="font-semibold">-12%</span></p>
                                <p class="text-white/40 text-xs">vs yesterday</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 rounded-xl p-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10">
                                •
                            </div>
                            <div>
                                <p class="text-white">Top category this month</p>
                                <p class="text-white/40 text-xs">Smartphone</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 rounded-xl p-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10">
                                ↑
                            </div>
                            <div>
                                <p class="text-white">Average transaction value up <span class="font-semibold">+18%</span>
                                </p>
                                <p class="text-white/40 text-xs">vs last month</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 rounded-xl p-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10">
                                ↑
                            </div>
                            <div>
                                <p class="text-white">New customers acquired <span class="font-semibold">+8%</span></p>
                                <p class="text-white/40 text-xs">vs last week</p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- 🏷️ PRODUCTS BY CATEGORY -->
            <div x-data="{
                data: [
                    { name: 'Smartphone', value: 320, opacity: 0.3 },
                    { name: 'Accessories', value: 580, opacity: 0.45 },
                    { name: 'Tablet', value: 120, opacity: 0.6 },
                    { name: 'Laptop', value: 85, opacity: 0.75 },
                    { name: 'Audio', value: 210, opacity: 0.9 },
                ],
                total: 0,
                radius: 70,
                circumference: 0,
            
                init() {
                    this.total = this.data.reduce((a, b) => a + b.value, 0)
                    this.circumference = 2 * Math.PI * this.radius
                },
            
                getDash(val) {
                    return (val / this.total) * this.circumference
                },
            
                getOffset(index) {
                    let sum = 0
                    for (let i = 0; i < index; i++) {
                        sum += this.data[i].value
                    }
                    return (sum / this.total) * this.circumference
                }
            }" x-init="init()"
                class="bg-white/5 border border-white/10 rounded-2xl p-5 mt-4">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M11 3a9 9 0 100 18V3z" />
                        </svg>
                        <p class="text-sm font-medium text-white/90">
                            Products by Category
                        </p>
                    </div>

                    <span class="text-xs text-white/40">
                        Total: <span class="text-white font-medium" x-text="total"></span>
                    </span>
                </div>

                <!-- CONTENT -->
                <div class="flex flex-col md:flex-row items-center gap-6">

                    <!-- DONUT -->
                    <div class="relative w-44 h-44">

                        <svg viewBox="0 0 160 160" class="rotate-[-90deg]">

                            <!-- BASE -->
                            <circle cx="80" cy="80" r="70" stroke="rgba(255,255,255,0.08)"
                                stroke-width="14" fill="none" />

                            <!-- SEGMENTS -->
                            <template x-for="(item, i) in data" :key="i">
                                <circle cx="80" cy="80" r="70" fill="none"
                                    :stroke="'rgba(255,255,255,' + (0.25 + i * 0.12) + ')'" stroke-width="14"
                                    stroke-linecap="round" :stroke-dasharray="getDash(item.value) + ' ' + circumference"
                                    :stroke-dashoffset="-getOffset(i)"
                                    class="opacity-90 hover:opacity-100 transition-all duration-700"
                                    x-init="$nextTick(() => {
                                        $el.style.strokeDasharray = getDash(item.value) + ' ' + circumference
                                    })"></circle>
                            </template>

                        </svg>

                        <!-- CENTER -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-semibold text-white" x-text="total"></span>
                            <span class="text-xs text-white/40">Products</span>
                        </div>

                    </div>

                    <!-- LEGEND -->
                    <div class="flex-1 space-y-3 w-full">

                        <template x-for="(item, i) in data" :key="i">
                            <div class="flex justify-between items-center text-sm">

                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full"
                                        :style="'background: rgba(255,255,255,' + (0.25 + i * 0.12) + ')'"></span>

                                    <span class="text-white/70" x-text="item.name"></span>
                                </div>

                                <span class="text-white font-medium tabular-nums" x-text="item.value"></span>

                            </div>
                        </template>

                    </div>

                </div>

            </div>

            <!-- 🕒 RECENT ACTIVITY -->
            <div x-data="{
                limit: 6,
                activities: [
                    { type: 'sale', text: 'Sold iPhone 15 Pro Max', time: '2 mins ago' },
                    { type: 'restock', text: 'Restocked Samsung Galaxy A54 (×10)', time: '15 mins ago' },
                    { type: 'price', text: 'Price updated: AirPods Pro 2', time: '32 mins ago' },
                    { type: 'sale', text: 'Sold Anker PowerBank 20K', time: '1 hour ago' },
                    { type: 'return', text: 'Returned: Screen Protector iPhone 14', time: '1.5 hours ago' },
                    { type: 'sale', text: 'Sold Samsung Charger 25W (×3)', time: '2 hours ago' },
                    { type: 'restock', text: 'Restocked Lightning Cable (×50)', time: '3 hours ago' },
                    { type: 'new', text: 'New product added: Xiaomi Buds 5', time: '4 hours ago' },
                ]
            }" class="bg-white/5 border border-white/10 rounded-2xl p-5 mt-4">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-5">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                        <p class="text-sm font-medium text-white/90">Recent Activity</p>
                    </div>

                    <span class="text-xs text-white/40">
                        <span x-text="activities.length"></span> items
                    </span>
                </div>

                <!-- LIST -->
                <div class="space-y-2 max-h-72 overflow-y-auto pr-1">

                    <template x-for="(item, i) in activities.slice(0, limit)" :key="i">
                        <div class="flex items-center justify-between px-3 py-3 rounded-xl hover:bg-white/5 transition">

                            <!-- LEFT -->
                            <div class="flex items-center gap-3">

                                <!-- ICON -->
                                <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 text-sm">

                                    <!-- SALE -->
                                    <svg x-show="item.type === 'sale'" class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4" />
                                        <circle cx="7" cy="21" r="1" />
                                        <circle cx="17" cy="21" r="1" />
                                    </svg>

                                    <!-- RESTOCK -->
                                    <svg x-show="item.type === 'restock'" class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 17l9 4 9-4" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9 4 9-4" />
                                    </svg>

                                    <!-- PRICE -->
                                    <svg x-show="item.type === 'price'" class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h6l4 4-6 6-4-4V7z" />
                                    </svg>

                                    <!-- RETURN -->
                                    <svg x-show="item.type === 'return'" class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l-4-4 4-4" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 20a8 8 0 00-8-8H5" />
                                    </svg>

                                    <!-- NEW -->
                                    <svg x-show="item.type === 'new'" class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 3v6m0 6v6m-6-6h6m6 0h-6" />
                                    </svg>

                                </div>

                                <!-- TEXT -->
                                <p class="text-sm text-white/80" x-text="item.text"></p>

                            </div>

                            <!-- TIME -->
                            <span class="text-xs text-white/40" x-text="item.time"></span>

                        </div>
                    </template>

                </div>
            </div>
        </div>

        <!-- ⚙️ CALCULATOR -->
        <div x-show="page === 'calculator'" x-data="assetApp()" class="h-full overflow-y-auto p-4 space-y-4">

            <!-- TITLE -->
            <h1 class="text-xl font-bold text-white/90">Asset Calculator</h1>

            <!-- TOTAL CARD -->
            <div class="relative rounded-2xl border border-white/10 bg-white/[0.04] p-6 text-center space-y-3">

                <!-- COPY BUTTON -->
                <button @click="copy()"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg 
    hover:bg-white/10 active:scale-90 transition overflow-hidden">

                    <div class="relative w-5 h-5">

                        <!-- ICON COPY -->
                        <svg :class="copied
                            ?
                            'opacity-0 scale-75 rotate-[-20deg]' :
                            'opacity-100 scale-100 rotate-0'"
                            class="absolute inset-0 w-5 h-5 text-white/60 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"></path>
                        </svg>

                        <!-- ICON CHECK -->
                        <svg :class="copied
                            ?
                            'opacity-100 scale-110 rotate-0' :
                            'opacity-0 scale-50 rotate-[20deg]'"
                            class="absolute inset-0 w-5 h-5 text-green-400 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7"></path>
                        </svg>

                    </div>

                </button>

                <!-- BADGE -->
                <div class="inline-flex px-3 py-1 rounded-full bg-white/10 text-xs text-white/70">
                    Santuy Cell
                </div>

                <p class="text-xs tracking-widest text-white/40 uppercase">
                    Total Assets
                </p>

                <!-- TOTAL DINAMIS -->
                <h2 class="text-4xl font-semibold text-white tracking-tight" x-text="format(totalAssets())">
                </h2>

            </div>

            <!-- FORM CARD -->
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5 space-y-4">

                <!-- HEADER -->
                <div class="flex justify-between items-center">
                    <p class="text-sm font-semibold text-white/80">Assets</p>

                    <button @click="addField()"
                        class="text-xs px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white/80 transition">
                        + Add
                    </button>
                </div>

                <!-- LIST -->
                <div class="space-y-3">

                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center justify-between gap-3">

                            <!-- LABEL -->
                            <div class="flex-1">
                                <!-- VIEW -->
                                <p x-show="!item.editing" @click="item.editing = true"
                                    class="text-sm text-white/70 cursor-text">
                                    <span x-text="item.label || 'Unnamed'"></span>
                                </p>

                                <!-- EDIT -->
                                <input x-show="item.editing" x-model="item.label" @blur="item.editing = false"
                                    @keydown.enter="item.editing = false" type="text" placeholder="Label..."
                                    class="w-full bg-transparent border-none outline-none text-sm text-white/90 placeholder-white/30">
                            </div>

                            <!-- VALUE -->
                            <input type="text" inputmode="numeric" :value="formatNumber(item.value)"
                                @input="updateValue($event, item)"
                                class="w-40 text-right px-3 py-2 rounded-xl 
                        bg-white/[0.05] border border-white/10 text-white 
                        focus:outline-none 
                        focus:border-white/40 
                        focus:ring-2 focus:ring-white/20 
                        transition">
                        </div>
                    </template>

                </div>
            </div>

        </div>

        <script>
            function assetApp() {
                return {
                    copied: false,

                    items: [{
                            label: 'Bank BCA',
                            value: 15000000,
                            editing: false
                        },
                        {
                            label: 'Bank Mandiri',
                            value: 8500000,
                            editing: false
                        },
                        {
                            label: 'E-Wallet (GoPay)',
                            value: 2300000,
                            editing: false
                        },
                        {
                            label: 'E-Wallet (OVO)',
                            value: 1200000,
                            editing: false
                        },
                        {
                            label: 'Cash on Hand',
                            value: 5000000,
                            editing: false
                        },
                    ],

                    addField() {
                        this.items.push({
                            label: '',
                            value: 0,
                            editing: true
                        });
                    },

                    updateValue(e, item) {
                        let raw = e.target.value.replace(/\D/g, '');
                        item.value = parseInt(raw || 0);
                    },

                    totalAssets() {
                        return this.items.reduce((sum, item) => sum + (item.value || 0), 0);
                    },

                    format(number) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
                    },

                    formatNumber(number) {
                        return new Intl.NumberFormat('id-ID').format(number || 0);
                    },

                    // 🔥 COPY FUNCTION (DYNAMIC)
                    copy() {
                        let text = `Santuy Cell

                        Total Assets
                        ${this.format(this.totalAssets())}

                        Assets:
                        `;

                        this.items.forEach(item => {
                            if (item.label) {
                                text += `${item.label}: ${this.format(item.value)}\n`;
                            }
                        });

                        navigator.clipboard.writeText(text);

                        this.copied = true;

                        setTimeout(() => {
                            this.copied = false;
                        }, 1200);
                    }
                }
            }

            const ctxSantuy = document.getElementById('salesChartSantuy');

            new Chart(ctxSantuy, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        data: [12, 19, 8, 15, 10, 18, 14],
                        borderRadius: 6,
                        barThickness: 18,
                        backgroundColor: '#3b82f6',
                        hoverBackgroundColor: '#60a5fa',
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            display: false,
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            document.addEventListener("DOMContentLoaded", () => {
                const items = document.querySelectorAll('.category-item');

                items.forEach((el, i) => {
                    el.style.animation = `fadeUpScale 0.5s ease forwards`;
                    el.style.animationDelay = `${i * 80}ms`; // 👉 ini bikin satu-satu muncul
                });
            });
        </script>
    @endsection

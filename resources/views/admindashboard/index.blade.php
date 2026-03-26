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
    </style>
    <div class="relative min-h-[300px] overflow-x-hidden">

        <!-- 🏠 HOME -->
        <div x-show="page === 'home'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="pb-24 space-y-6 text-xs md:text-sm">

            <!-- 🔥 ALL OUTLETS -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl p-5 shadow-xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-base md:text-lg lg:text-xl flex items-center gap-2">
                        <!-- icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        All Outlets Combined
                    </h2>
                    <span class="text-xs text-green-200">+11.2%</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs md:text-sm">
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Today's Sales</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl">Rp 84.000.000</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Transactions</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl">270</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Monthly Sales</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl">Rp 1.648.000.000</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/70">Total Profit</p>
                        <p class="font-semibold text-sm md:text-lg lg:text-xl">Rp 22.200.000</p>
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
                            <span class="text-green-400 text-xs md:text-sm font-medium">+12.5%</span>

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

                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:30%"></div>
                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:45%"></div>
                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:25%"></div>
                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:60%"></div>
                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:50%"></div>
                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:70%"></div>
                            <div class="w-full bg-[#3b82f6] rounded-sm opacity-80 hover:opacity-100 transition"
                                style="height:45%"></div>

                        </div>

                    </div>

                </div>

                <!-- 🟩 LIVE ACTIVITY -->
                <div class="bg-[#111827] rounded-2xl p-4 border border-white/5">

                    <!-- TITLE -->
                    <h3 class="font-semibold text-base md:text-lg lg:text-xl mb-4 flex items-center gap-2 text-white">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
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
                                <div class="bg-green-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
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
                            <p class="text-green-400 font-semibold text-sm md:text-lg">
                                Rp 8.500.000
                            </p>
                        </div>

                        <!-- ITEM -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <div class="flex items-center gap-3">
                                <div class="bg-green-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
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

                            <p class="text-green-400 font-semibold text-sm md:text-lg">
                                Rp 4.200.000
                            </p>
                        </div>

                        <!-- ITEM -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <div class="flex items-center gap-3">
                                <div class="bg-green-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
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

                            <p class="text-green-400 font-semibold text-sm md:text-lg">
                                Rp 2.100.000
                            </p>
                        </div>

                        <!-- ITEM (RESTOCK - beda icon biru) -->
                        <div class="flex items-center justify-between bg-black/30 rounded-xl p-3">

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
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
                            class="absolute bottom-0 h-[2px] bg-blue-500 transition-all duration-300 ease-out">
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
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2"
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
                                class="absolute bottom-0 h-[2px] bg-blue-500 transition-all duration-300">
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
                                <div class="bg-blue-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-1 rounded-full">
                                3 left
                            </span>
                        </div>


                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-orange-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M13 7h8m0 0v8m0-8L10 18l-4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">USB-C Cable</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-orange-500/20 text-orange-400 px-2 py-1 rounded-full">
                                8 left
                            </span>
                        </div>


                        <!-- TABLET -->
                        <div x-show="active==='all'||active==='tablet'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-purple-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="4" y="3" width="16" height="18" rx="2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">iPad Air M2</p>
                                    <p class="text-xs text-gray-400">Tablet</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-1 rounded-full">
                                1 left
                            </span>
                        </div>


                        <!-- LAPTOP -->
                        <div x-show="active==='all'||active==='laptop'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">

                            <div class="flex gap-3 items-center">
                                <div class="bg-cyan-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-1 rounded-full">
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
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
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
                                class="absolute bottom-0 h-[2px] bg-blue-500 transition-all duration-300">
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
                                <div class="bg-blue-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                120 sold
                            </span>
                        </div>


                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-purple-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-purple-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-purple-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-purple-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                210 sold
                            </span>
                        </div>

                        <!-- ACCESSORIES -->
                        <div x-show="active==='all'||active==='accessories'"
                            class="bg-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-purple-500/20 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
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

                            <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
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
            x-transition:leave-end="opacity-0 -translate-y-2" class="absolute inset-0">

            <h1 class="text-xl font-bold mb-4">Detail</h1>

            <div class="bg-gray-800 p-4 rounded-xl">
                Jam Rame + Hari Terlaris + Bulan Terbaik
            </div>
        </div>

        <!-- ⚙️ SETTINGS -->
        <div x-show="page === 'settings'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="absolute inset-0">

            <h1 class="text-xl font-bold mb-4">Menu</h1>

            <div class="bg-gray-800 p-4 rounded-xl">
                Settings / Config / Master Data
            </div>
        </div>

    </div>

    <script>
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
    </script>
@endsection

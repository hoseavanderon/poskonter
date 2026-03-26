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
    </style>
    <div class="relative min-h-[300px]">

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
            class="absolute inset-0 p-4 md:p-6 space-y-6 bg-[#020617] text-white">

            <!-- HEADER -->
            <div class="flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
                <div>
                    <h1 class="text-lg md:text-2xl font-semibold tracking-tight">
                        Inventory Insights
                    </h1>
                    <p class="text-gray-400 text-xs md:text-sm">
                        Monitor low stock and best-selling products
                    </p>
                </div>

                <!-- FILTER -->
                <div class="flex gap-2 bg-white/5 p-1 rounded-xl w-fit">
                    <button class="px-3 py-1 text-xs rounded-lg bg-blue-500 text-white">
                        Today
                    </button>
                    <button class="px-3 py-1 text-xs rounded-lg text-gray-400 hover:text-white">
                        Weekly
                    </button>
                    <button class="px-3 py-1 text-xs rounded-lg text-gray-400 hover:text-white">
                        Monthly
                    </button>
                </div>
            </div>

            <!-- CATEGORY -->
            <div class="flex gap-2 overflow-x-auto pb-1">
                <button class="px-4 py-1.5 rounded-full text-xs bg-blue-500 text-white whitespace-nowrap">
                    All
                </button>
                <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 text-gray-300 whitespace-nowrap">
                    Smartphone
                </button>
                <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 text-gray-300 whitespace-nowrap">
                    Accessories
                </button>
                <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 text-gray-300 whitespace-nowrap">
                    Tablet
                </button>
                <button class="px-4 py-1.5 rounded-full text-xs bg-white/5 text-gray-300 whitespace-nowrap">
                    Laptop
                </button>
            </div>

            <!-- CONTENT -->
            <div class="grid md:grid-cols-2 gap-5">

                <!-- 🔴 LOW STOCK -->
                <div class="space-y-4">

                    <div class="flex items-center gap-2">
                        <span class="text-yellow-400 text-lg">⚠️</span>
                        <h2 class="font-semibold text-sm md:text-base">
                            Low Stock Alert
                        </h2>
                        <span class="text-[10px] bg-orange-500/20 text-orange-400 px-2 py-0.5 rounded-full">
                            NEEDS ATTENTION
                        </span>
                    </div>

                    <!-- CARD -->
                    <div class="space-y-3">

                        <!-- ITEM -->
                        <div
                            class="bg-white/5 border border-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/10 p-2 rounded-lg">
                                    📦
                                </div>

                                <div>
                                    <p class="text-sm font-medium">Samsung Galaxy S24 Ultra</p>
                                    <p class="text-xs text-gray-400">Smartphone</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-1 rounded-full">
                                3 left
                            </span>
                        </div>

                        <!-- ITEM -->
                        <div
                            class="bg-white/5 border border-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/10 p-2 rounded-lg">
                                    📦
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

                        <!-- ITEM -->
                        <div
                            class="bg-white/5 border border-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-white/10 p-2 rounded-lg">
                                    📦
                                </div>

                                <div>
                                    <p class="text-sm font-medium">USB-C Cable 2m</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-[10px] bg-orange-500/20 text-orange-400 px-2 py-1 rounded-full">
                                8 left
                            </span>
                        </div>

                    </div>

                </div>

                <!-- 🟢 BEST SELLER -->
                <div class="space-y-4">

                    <div class="flex items-center gap-2">
                        <span class="text-green-400 text-lg">📈</span>
                        <h2 class="font-semibold text-sm md:text-base">
                            Top Selling Products
                        </h2>
                        <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">
                            TRENDING
                        </span>
                    </div>

                    <div class="space-y-3">

                        <!-- ITEM -->
                        <div
                            class="bg-white/5 border border-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-green-500/20 p-2 rounded-lg text-green-400">
                                    🛒
                                </div>

                                <div>
                                    <p class="text-sm font-medium">Samsung Galaxy S24</p>
                                    <p class="text-xs text-gray-400">Smartphone</p>
                                </div>
                            </div>

                            <span class="text-xs text-green-400 font-medium">
                                ↑ 120 sold
                            </span>
                        </div>

                        <!-- ITEM -->
                        <div
                            class="bg-white/5 border border-white/5 rounded-2xl p-4 flex justify-between items-center hover:bg-white/10 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-green-500/20 p-2 rounded-lg text-green-400">
                                    🛒
                                </div>

                                <div>
                                    <p class="text-sm font-medium">iPad Pro M4</p>
                                    <p class="text-xs text-gray-400">Tablet</p>
                                </div>
                            </div>

                            <span class="text-xs text-green-400 font-medium">
                                ↑ 62 sold
                            </span>
                        </div>

                        <!-- ITEM -->
                        <div
                            class="bg-green-500/10 border border-green-500/30 rounded-2xl p-4 flex justify-between items-center hover:bg-green-500/20 transition">

                            <div class="flex gap-3 items-center">
                                <div class="bg-green-500/20 p-2 rounded-lg text-green-400">
                                    🛒
                                </div>

                                <div>
                                    <p class="text-sm font-medium">MagSafe Charger</p>
                                    <p class="text-xs text-gray-400">Accessories</p>
                                </div>
                            </div>

                            <span class="text-xs text-green-400 font-medium">
                                ↑ 210 sold
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

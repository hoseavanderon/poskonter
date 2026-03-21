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
                    <h3 class="font-semibold text-base md:text-lg lg:text-xl mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M4 12h4l2-5 4 10 2-5h4" />
                        </svg>
                        Live Activity
                    </h3>

                    <div class="space-y-3 text-xs md:text-sm">

                        <!-- ITEM -->
                        <div class="flex justify-between items-center bg-black/30 rounded-xl p-3">
                            <div>
                                <p class="font-medium">iPhone 15 Pro Max</p>
                                <p class="text-gray-400 text-[10px] md:text-xs lg:text-sm">Santuy Cell • 2 min ago</p>
                            </div>
                            <p class="text-green-400 font-semibold text-sm md:text-lg">Rp 8.500.000</p>
                        </div>

                        <div class="flex justify-between items-center bg-black/30 rounded-xl p-3">
                            <div>
                                <p class="font-medium">Samsung S24</p>
                                <p class="text-gray-400 text-[10px] md:text-xs lg:text-sm">Tian Cell • 5 min ago</p>
                            </div>
                            <p class="text-green-400 font-semibold text-sm md:text-lg">Rp 4.200.000</p>
                        </div>

                        <div class="flex justify-between items-center bg-black/30 rounded-xl p-3">
                            <div>
                                <p class="font-medium">AirPods Pro</p>
                                <p class="text-gray-400 text-[10px] md:text-xs lg:text-sm">Santuy Cell • 12 min ago</p>
                            </div>
                            <p class="text-green-400 font-semibold text-sm md:text-lg">Rp 2.100.000</p>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- 🛒 PRODUK -->
        <div x-show="page === 'product'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="absolute inset-0">

            <h1 class="text-xl font-bold mb-4">Produk</h1>

            <div class="bg-gray-800 p-4 rounded-xl">
                Produk Terlaris + Stok Hampir Habis
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

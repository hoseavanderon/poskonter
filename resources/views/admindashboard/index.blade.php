@extends('layouts.admin')

@section('content')
    <div class="relative min-h-[300px]">

        <!-- 🏠 HOME -->
        <div x-show="page === 'home'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="px-4 pb-24 space-y-6 max-w-7xl mx-auto">

            <!-- 🔥 ALL OUTLETS -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl p-5 md:p-6 shadow-xl">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="flex items-center gap-2 text-base md:text-lg font-semibold">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        All Outlets Combined
                    </h2>
                    <span class="text-xs md:text-sm text-green-200 font-medium">+11.2%</span>
                </div>

                <div class="grid grid-cols-2 gap-3 md:gap-4">
                    <div class="bg-white/10 rounded-xl p-3 md:p-4">
                        <p class="text-white/70 text-xs md:text-sm mb-1">Today's Sales</p>
                        <p class="text-lg md:text-xl font-bold">Rp 84.000.000</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 md:p-4">
                        <p class="text-white/70 text-xs md:text-sm mb-1">Transactions</p>
                        <p class="text-lg md:text-xl font-bold">270</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 md:p-4">
                        <p class="text-white/70 text-xs md:text-sm mb-1">Monthly Sales</p>
                        <p class="text-lg md:text-xl font-bold">Rp 1.648.000.000</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 md:p-4">
                        <p class="text-white/70 text-xs md:text-sm mb-1">Total Profit</p>
                        <p class="text-lg md:text-xl font-bold">Rp 22.200.000</p>
                    </div>
                </div>
            </div>

            <!-- 🔥 SANTUY + LIVE -->
            <div class="grid md:grid-cols-2 gap-4">

                <!-- 🟦 SANTUY CELL -->
                <div class="bg-[#111827] rounded-2xl p-4 md:p-5 border border-white/5">
                    <div class="flex justify-between mb-5">
                        <h3 class="text-base md:text-lg font-semibold">Santuy Cell</h3>
                        <span class="text-green-400 text-xs md:text-sm font-medium">+12.5%</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 text-xs md:text-sm mb-1">Today's Sales</p>
                            <p class="text-base md:text-lg font-semibold">Rp 45.800.000</p>
                        </div>
                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 text-xs md:text-sm mb-1">Transactions</p>
                            <p class="text-base md:text-lg font-semibold">147</p>
                        </div>
                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 text-xs md:text-sm mb-1">Monthly</p>
                            <p class="text-base md:text-lg font-semibold">Rp 892.000.000</p>
                        </div>
                        <div class="bg-black/30 rounded-xl p-3 md:p-4">
                            <p class="text-gray-400 text-xs md:text-sm mb-1">Profit</p>
                            <p class="text-base md:text-lg font-semibold">Rp 12.400.000</p>
                        </div>
                    </div>
                </div>

                <!-- 🟩 LIVE ACTIVITY -->
                <div class="bg-[#111827] rounded-2xl p-4 md:p-5 border border-white/5">
                    <h3 class="text-base md:text-lg font-semibold mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-green-400" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 12h4l2-5 4 10 2-5h4" />
                        </svg>
                        Live Activity
                    </h3>

                    <div class="space-y-3">

                        <div class="flex justify-between items-center bg-black/30 rounded-xl p-3 md:p-4">
                            <div>
                                <p class="text-sm md:text-base font-medium">iPhone 15 Pro Max</p>
                                <p class="text-gray-400 text-[11px] md:text-xs">Santuy Cell • 2 min ago</p>
                            </div>
                            <p class="text-green-400 text-sm md:text-base font-semibold">Rp 8.500.000</p>
                        </div>

                        <div class="flex justify-between items-center bg-black/30 rounded-xl p-3 md:p-4">
                            <div>
                                <p class="text-sm md:text-base font-medium">Samsung S24</p>
                                <p class="text-gray-400 text-[11px] md:text-xs">Tian Cell • 5 min ago</p>
                            </div>
                            <p class="text-green-400 text-sm md:text-base font-semibold">Rp 4.200.000</p>
                        </div>

                        <div class="flex justify-between items-center bg-black/30 rounded-xl p-3 md:p-4">
                            <div>
                                <p class="text-sm md:text-base font-medium">AirPods Pro</p>
                                <p class="text-gray-400 text-[11px] md:text-xs">Santuy Cell • 12 min ago</p>
                            </div>
                            <p class="text-green-400 text-sm md:text-base font-semibold">Rp 2.100.000</p>
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

        <!-- 📊 INSIGHT -->
        <div x-show="page === 'insight'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="absolute inset-0">

            <h1 class="text-xl font-bold mb-4">Insight</h1>

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
@endsection

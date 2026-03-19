<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin POS</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#0f172a] text-white">

    <div x-data="{
        page: 'dashboard',
        selectedOutlet: 'all'
    }" class="min-h-screen pb-28">

        <!-- CONTENT -->
        <div class="p-5">

            <div x-data="{
                selectedOutlet: 'all',
                tabs: ['all', 'santuy', 'tian']
            }" class="mb-6">

                <!-- TOP -->
                <div class="flex items-center justify-between">

                    <!-- LEFT -->
                    <div>
                        <h1 class="text-xl font-semibold">Dashboard</h1>
                        <p class="text-xs text-gray-400">Multi-outlet system</p>
                    </div>

                    <!-- RIGHT (TAB TEXT STYLE) -->
                    <div class="relative">

                        <!-- TAB TEXT -->
                        <div class="flex gap-6 text-sm font-medium">
                            <template x-for="tab in tabs" :key="tab">
                                <button @click="selectedOutlet = tab" class="relative pb-1"
                                    :class="selectedOutlet === tab ? 'text-white' : 'text-gray-400'"
                                    x-text="tab === 'all' ? 'All' : (tab === 'santuy' ? 'Santuy Cell' : 'Tian Cell')">
                                </button>
                            </template>
                        </div>

                        <!-- UNDERLINE -->
                        <div class="absolute bottom-0 left-0 h-[2px] bg-blue-500 transition-all duration-300"
                            :style="{
                                width: selectedOutlet === 'all' ? '30px' : selectedOutlet === 'santuy' ? '90px' :
                                    '70px',
                                transform: selectedOutlet === 'all' ? 'translateX(0px)' :
                                    selectedOutlet === 'santuy' ? 'translateX(50px)' : 'translateX(160px)'
                            }">
                        </div>

                    </div>

                </div>
            </div>

            <!-- 🔥 PAGE CONTENT -->
            @yield('content')

        </div>

        <!-- 🔥 BOTTOM NAV (MOBILE STYLE) -->
        <div
            class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[92%] max-w-md 
                bg-[#111827]/90 backdrop-blur-md 
                border border-white/10 
                rounded-2xl px-4 py-2 
                flex justify-between items-center 
                shadow-xl z-50">

            <!-- DASHBOARD -->
            <button @click="page = 'dashboard'" class="flex flex-col items-center text-xs transition"
                :class="page === 'dashboard' ? 'text-blue-400' : 'text-gray-400'">
                <span>🏠</span>
                <span>Home</span>
            </button>

            <!-- ANALYTICS -->
            <button @click="page = 'analytics'" class="flex flex-col items-center text-xs transition"
                :class="page === 'analytics' ? 'text-blue-400' : 'text-gray-400'">
                <span>📊</span>
                <span>Stats</span>
            </button>

            <!-- CENTER BUTTON -->
            <button
                class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg shadow-lg -mt-6 active:scale-95 transition">
                +
            </button>

            <!-- OUTLET -->
            <button @click="page = 'outlet'" class="flex flex-col items-center text-xs transition"
                :class="page === 'outlet' ? 'text-blue-400' : 'text-gray-400'">
                <span>🏪</span>
                <span>Outlet</span>
            </button>

            <!-- SETTINGS -->
            <button @click="page = 'settings'" class="flex flex-col items-center text-xs transition"
                :class="page === 'settings' ? 'text-blue-400' : 'text-gray-400'">
                <span>⚙️</span>
                <span>Menu</span>
            </button>

        </div>

    </div>

</body>

</html>

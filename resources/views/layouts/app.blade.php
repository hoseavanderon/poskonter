{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        isFullscreen: false,
        sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'false'),
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            document.documentElement.classList.toggle('dark', this.darkMode);
        },
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                this.isFullscreen = true;
            } else {
                document.exitFullscreen();
                this.isFullscreen = false;
            }
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        }
    }"
    x-init="
        document.documentElement.classList.toggle('dark', darkMode);
        document.addEventListener('fullscreenchange', () => { isFullscreen = !!document.fullscreenElement; });
    "
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS App' }}</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>

    {{-- AlpineJS --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        * { transition: background-color .25s ease, color .25s ease; }
        body { opacity: 0; animation: fadeIn .3s ease-in-out forwards; overflow-x: hidden; }
        @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
    </style>

    @stack('head')
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    {{-- NAVBAR --}}
    <header class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shadow-sm z-40 relative">
        <div class="flex items-center gap-2">
            {{-- Sidebar toggle (mobile & desktop) --}}
            <button @click="toggleSidebar"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h1 class="font-semibold text-lg">Santuy Cell</h1>
        </div>

        <div class="flex items-center gap-3">
            {{-- Fullscreen toggle --}}
            <button @click="toggleFullscreen"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Toggle Fullscreen">
                <template x-if="!isFullscreen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                    </svg>
                </template>
                <template x-if="isFullscreen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9l-5-5m0 0v4m0-4h4M15 9l5-5m0 0v4m0-4h-4M9 15l-5 5m0 0v-4m0 4h4M15 15l5 5m0 0v-4m0 4h-4" />
                    </svg>
                </template>
            </button>

            {{-- Dark mode toggle --}}
            <button @click="toggleDarkMode" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Dark Mode">
                <template x-if="!darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m8.66-8.66h1M3.34 12.34h1M16.24 7.76l.7-.7M7.76 16.24l-.7.7M16.24 16.24l.7.7M7.76 7.76l-.7-.7M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </template>
                <template x-if="darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </template>
            </button>
        </div>
    </header>

    {{-- LAYOUT --}}
    <div class="relative flex min-h-[calc(100vh-64px)] overflow-hidden">
        {{-- Sidebar floating --}}
        <aside
            class="fixed top-[64px] left-0 h-[calc(100vh-64px)] w-60 bg-gray-50 dark:bg-gray-800 border-r dark:border-gray-700 shadow-xl transform transition-transform duration-300 ease-in-out z-30"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <nav class="space-y-2 p-4">
                <a href="{{ route('pos') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-home class="w-5 h-5" /> <span>POS</span>
                </a>

                <a href="{{ route('riwayat') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-clock class="w-5 h-5" /> <span>Riwayat</span>
                </a>

                <a href="{{ route('pembukuan') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-banknotes class="w-5 h-5" /> <span>Pembukuan</span>
                </a>

                <a href="{{ route('stokbarang') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-cube class="w-5 h-5" /> <span>Stok Barang</span>
                </a>

                <a href="{{ route('history_inventory') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-archive-box class="w-5 h-5" /> <span>History Stok Barang</span>
                </a>

                <a href="{{ route('barangmasuk') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" /> <span>Barang Masuk</span>
                </a>

                <a href="{{ route('customer') }}"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-user-group class="w-5 h-5" /> <span>Langganan</span>
                </a>

                {{-- 🔐 Link menuju halaman Admin --}}
                <a href="/admin"
                @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" /> <span>Admin</span>
                </a>
            </nav>

        </aside>

        {{-- Overlay ketika sidebar terbuka --}}
        <div x-show="sidebarOpen" @click="toggleSidebar"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20"
            x-transition.opacity></div>

        {{-- Konten utama --}}
        <main class="flex-1 p-4 overflow-y-auto relative z-10">
            @yield('content')
        </main>
    </div>

</body>
</html>

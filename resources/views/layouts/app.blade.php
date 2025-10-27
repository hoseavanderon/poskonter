{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    isFullscreen: false,
    sidebarOpen: false,
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
    }
}" x-init="document.documentElement.classList.toggle('dark', darkMode);
document.addEventListener('fullscreenchange', () => { isFullscreen = !!document.fullscreenElement; });">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS App' }}</title> {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script> {{-- AlpineJS --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        * {
            transition: background-color .25s ease, color .25s ease;
        }

        body {
            opacity: 0;
            animation: fadeIn .3s ease-in-out forwards;
            overflow-x: hidden;
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"> {{-- NAVBAR --}} <header
        class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shadow-sm">
        <div class="flex items-center gap-2"> {{-- Sidebar toggle (mobile) --}} <button @click="sidebarOpen = !sidebarOpen"
                class="md:hidden p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"> {{-- menu icon --}} <svg
                    xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg> </button>
            <h1 class="font-semibold text-lg">Santuy Cell</h1>
        </div>
        <div class="flex items-center gap-3"> {{-- ✅ Fullscreen toggle --}} <button @click="toggleFullscreen"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Toggle Fullscreen"> <template
                    x-if="!isFullscreen"> {{-- expand icon --}} <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                    </svg> </template> <template x-if="isFullscreen"> {{-- compress icon --}} <svg
                        xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9l-5-5m0 0v4m0-4h4M15 9l5-5m0 0v4m0-4h-4M9 15l-5 5m0 0v-4m0 4h4M15 15l5 5m0 0v-4m0 4h-4" />
                    </svg> </template> </button> {{-- 🌙 Dark mode toggle --}} <button @click="toggleDarkMode"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Dark Mode"> <template
                    x-if="!darkMode"> <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m8.66-8.66h1M3.34 12.34h1M16.24 7.76l.7-.7M7.76 16.24l-.7.7M16.24 16.24l.7.7M7.76 7.76l-.7-.7M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg> </template> <template x-if="darkMode"> <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg> </template> </button> {{-- User Dropdown --}} <div x-data="{ openUserMenu: false, showLogoutConfirm: false }"
                class="relative hidden sm:flex items-center gap-3"> {{-- Avatar --}} <button
                    @click="openUserMenu = !openUserMenu"
                    class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <div
                        class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }} </div> <span
                        class="text-sm font-medium">{{ Auth::user()->name }}</span> {{-- Icon dropdown --}} <svg
                        xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button> {{-- Dropdown menu --}} <div x-show="openUserMenu" @click.away="openUserMenu = false"
                    x-transition.origin.top.right
                    class="absolute right-0 top-10 w-40 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg overflow-hidden z-50">
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        👤 Profil </a> <button @click="openUserMenu = false; showLogoutConfirm = true;"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        🚪 Logout </button>
                </div> {{-- Logout Confirmation Modal --}} <div x-show="showLogoutConfirm" x-transition.opacity.duration.300ms
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                    <div x-show="showLogoutConfirm" x-transition.scale.duration.300ms
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-[90%] max-w-sm text-center">
                        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">Konfirmasi Logout</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">Apakah kamu yakin ingin keluar?</p>
                        <div class="flex justify-center gap-3"> <button @click="showLogoutConfirm = false"
                                class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                Batal </button>
                            <form method="POST" action="{{ route('logout') }}"> @csrf <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                    Keluar </button> </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    {{-- MAIN --}}
    <div class="flex min-h-[calc(100vh-64px)]"> {{-- Sidebar --}}
        <aside class="hidden lg:block w-60 bg-gray-50 dark:bg-gray-800 border-r dark:border-gray-700 p-4">
            <nav class="space-y-2">

                <!-- POS -->
                <a href="{{ route('pos') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-home class="w-5 h-5" />
                    <span>POS</span>
                </a>

                <!-- RIWAYAT TRANSAKSI -->
                <a href="{{ route('riwayat') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    <span>Riwayat</span>
                </a>

                <!-- PEMBUKUAN -->
                <a href="{{ route('pembukuan') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-banknotes class="w-5 h-5" />
                    <span>Pembukuan</span>
                </a>

                <!-- HISTORY STOK BARANG -->
                <a href="{{ route('history_inventory') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-archive-box class="w-5 h-5" />
                    <span>History Stok Barang</span>
                </a>

                <!-- BARANG MASUK -->
                <a href="{{ route('barangmasuk') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                    <span>Barang Masuk</span>
                </a>

                <!-- STOK BARANG -->
                <a href="{{ route('stokbarang') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-cube class="w-5 h-5" />
                    <span>Stok Barang</span>
                </a>

                <!-- LANGGANAN -->
                <a href="{{ route('customer') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    <span>Langganan</span>
                </a>

            </nav>
        </aside> {{-- Mobile sidebar --}}

        <div x-show="sidebarOpen" x-transition class="fixed inset-0 bg-black/50 z-40 md:hidden"
            @click="sidebarOpen=false">
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-white dark:bg-gray-800 p-4">
                <h2 class="text-lg font-semibold mb-4">Menu</h2>
                <nav class="space-y-2"> <a href="{{ route('pos') }}"
                        class="block px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">🏠 POS</a>
                    <a href="#" class="block px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">📦
                        Produk</a>
                    <a href="#" class="block px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">🧾
                        Transaksi</a>
                </nav>
            </div>
        </div> {{-- Content --}} <main class="flex-1 p-4 overflow-y-auto"> @yield('content') </main>
    </div>
</body>

</html>

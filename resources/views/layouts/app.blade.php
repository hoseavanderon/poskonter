@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // Jika user login dan role-nya Admin → redirect ke halaman Admin
    if ($user && strtolower($user->role) === 'admin') {
        header('Location: ' . route('admin-pos'));
        exit();
    }
@endphp
<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    isFullscreen: false,
    sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'false'),

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        document.documentElement.classList.toggle('dark', this.darkMode);
    },

    toggleFullscreen() {
        const el = document.body;

        if (!document.fullscreenElement) {
            if (el.requestFullscreen) {
                el.requestFullscreen({ navigationUI: 'hide' });
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            }
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
}" x-init="document.documentElement.classList.toggle('dark', darkMode);
document.addEventListener('fullscreenchange', () => {
    isFullscreen = !!document.fullscreenElement;
});">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ?? 'POS App' }}</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    {{-- AlpineJS --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        * {
            transition: background-color .25s ease, color .25s ease;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #000 !important;
            min-height: 100vh;
            height: 100%;
        }

        :fullscreen {
            background: #000 !important;
        }

        body:fullscreen {
            background: #000 !important;
            overscroll-behavior: none;
        }

        :root {
            /* Hilangkan white-bottom area saat fullscreen */
            padding-bottom: env(safe-area-inset-bottom);
            background-color: #000 !important;
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

        /* 🌙 Scrollbar modern dan halus untuk dark mode */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
            /* warna dasar latar belakang */
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #334155, #1e293b);
            border-radius: 9999px;
            border: 2px solid #0f172a;
            /* ruang kecil agar thumb terlihat floating */
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #475569, #334155);
        }

        /* 🦊 Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: #334155 #0f172a;
        }

        /* ✨ Efek halus saat scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>

    @stack('head')
</head>

<body class="h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    {{-- NAVBAR --}}
    <header
        class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shadow-sm z-10 relative">

        <div class="flex items-center gap-2">
            {{-- Sidebar toggle (mobile & desktop) --}}
            <button @click="toggleSidebar" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h1 class="font-semibold text-lg">
                {{ Auth::user()->outlet->name ?? (Auth::user()->name ?? 'Outlet') }}
            </h1>
        </div>

        <div class="flex items-center gap-3" x-data="{ openUserMenu: false }">
            {{-- Fullscreen toggle --}}
            <button @click="toggleFullscreen" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                title="Toggle Fullscreen">
                <template x-if="!isFullscreen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                    </svg>
                </template>
                <template x-if="isFullscreen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9l-5-5m0 0v4m0-4h4M15 9l5-5m0 0v4m0-4h-4M9 15l-5 5m0 0v-4m0 4h4M15 15l5 5m0 0v-4m0 4h-4" />
                    </svg>
                </template>
            </button>

            {{-- Dark mode toggle --}}
            <button @click="toggleDarkMode" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                title="Dark Mode">
                <template x-if="!darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m8.66-8.66h1M3.34 12.34h1M16.24 7.76l.7-.7M7.76 16.24l-.7.7M16.24 16.24l.7.7M7.76 7.76l-.7-.7M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </template>
                <template x-if="darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </template>
            </button>

            {{-- User Menu --}}
            <div class="relative">
                <button @click="openUserMenu = !openUserMenu"
                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ Auth::user()->name ?? 'User' }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 dark:text-gray-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="openUserMenu" @click.outside="openUserMenu = false" x-transition.scale.origin.top.right
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-2 z-50">

                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- LAYOUT --}}
    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        {{-- Sidebar floating --}}
        <aside
            class="fixed top-[64px] left-0 h-[calc(100vh-64px)] w-60 bg-gray-50 dark:bg-gray-800 border-r dark:border-gray-700 shadow-xl transform transition-transform duration-300 ease-in-out z-30"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">


            <nav class="space-y-2 p-4">
                @php
                    $user = Auth::user();
                @endphp

                @if ($user && in_array($user->role, ['kasir']))
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

                    <a href="{{ route('cetakbarcode') }}"
                        @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                        class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                        <x-heroicon-o-qr-code class="w-5 h-5" />
                        <span>Cetak Barcode</span>
                    </a>
                @endif
                <a href="/admin" @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', 'false')"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" /> <span>Admin</span>
                </a>
            </nav>

        </aside>

        {{-- Overlay ketika sidebar terbuka --}}
        <div x-show="sidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20"
            x-transition.opacity></div>

        {{-- Konten utama --}}
        <main class="flex-1 p-4 overflow-y-auto relative z-10">
            @yield('content')
        </main>
    </div>

</body>

</html>

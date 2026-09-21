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
<html lang="id" class="{{ request()->routeIs('pos') ? 'is-pos' : '' }}" x-data="{
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
    <title>{{ $title ?? 'POS App' }}</title>
    @include('partials.pwa')
    <script>
        (function() {
            try {
                if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
            } catch (e) {}
            var skip = false;
            try { skip = sessionStorage.getItem('pos-boot-done') === '1'; } catch (e) {}
            if (skip) document.documentElement.classList.add('alpine-ready');
            else document.documentElement.classList.add('is-boot');
        })();
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        html.is-boot .app-sidebar-overlay,
        html.is-boot .pos-modal-overlay,
        html.is-boot .pos-ok-overlay {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        .app-sidebar {
            transform: translate3d(-100%, 0, 0);
            transition: transform 300ms ease;
        }

        .app-sidebar.translate-x-0 {
            transform: translate3d(0, 0, 0);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/app-boot.css') }}">
    <script src="{{ asset('js/app-boot.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/sf-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
    <script src="{{ asset('js/vendor/alpine-collapse.min.js') }}" defer></script>

    {{-- AlpineJS --}}
    <script src="{{ asset('js/vendor/alpine.min.js') }}" defer></script>

    <style>
        :root {
            --app-bg: #F5F5F7;
            --surface: #FFFFFF;
            --surface-secondary: #F2F2F7;
            --border: #D1D1D6;
            --divider: #E5E5EA;
            --text-primary: #1D1D1F;
            --text-secondary: #6E6E73;
            --text-muted: #86868B;
            --accent: #007AFF;
            --accent-soft: rgba(0, 122, 255, 0.10);
            --border-hairline: rgba(0, 0, 0, 0.04);
            --shadow-card: 0 4px 20px rgba(0, 0, 0, 0.06);
            --shadow-card-lg: 0 8px 30px rgba(0, 0, 0, 0.06);
            --shadow-modal: 0 20px 60px rgba(0, 0, 0, 0.12);
            --shadow-focus: 0 0 0 3px rgba(0, 122, 255, 0.12);
            --icon: #3A3A3C;
        }

        html.dark {
            --app-bg: #000000;
            --surface: #1C1C1E;
            --surface-secondary: #2C2C2E;
            --border: #38383A;
            --divider: #38383A;
            --text-primary: #F5F5F7;
            --text-secondary: #AEAEB2;
            --text-muted: #8E8E93;
            --accent: #0A84FF;
            --accent-soft: rgba(10, 132, 255, 0.15);
            --border-hairline: rgba(255, 255, 255, 0.08);
            --shadow-card: 0 4px 24px rgba(0, 0, 0, 0.45);
            --shadow-card-lg: 0 8px 32px rgba(0, 0, 0, 0.50);
            --shadow-modal: 0 20px 60px rgba(0, 0, 0, 0.65);
            --shadow-focus: 0 0 0 3px rgba(10, 132, 255, 0.16);
            --icon: #D1D1D6;
        }

        * {
            transition: background-color .25s ease, color .25s ease;
        }

        html.is-pos,
        html.is-pos body {
            animation: none !important;
            opacity: 1 !important;
        }

        @media (max-width: 1023px) {
            html.is-pos * {
                transition: none !important;
            }
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: var(--app-bg);
            color: var(--text-primary);
            min-height: 100vh;
            height: 100%;
            font-family: var(--font-sf);
        }

        body:fullscreen {
            overscroll-behavior: none;
        }

        :root {
            /* Hilangkan white-bottom area saat fullscreen */
            padding-bottom: env(safe-area-inset-bottom);
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

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--surface-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
            border: 2px solid var(--surface-secondary);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--border) var(--surface-secondary);
        }

        header {
            background: var(--surface) !important;
            border-color: var(--border-hairline) !important;
            box-shadow: 0 1px 0 var(--border-hairline) !important;
            color: var(--text-primary) !important;
        }

        aside {
            background: var(--surface) !important;
            border-color: var(--border-hairline) !important;
            box-shadow: var(--shadow-card) !important;
            color: var(--text-primary) !important;
        }

        main {
            background: var(--app-bg) !important;
            color: var(--text-primary) !important;
        }

        input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
        textarea,
        select {
            background-color: var(--surface) !important;
            border-color: var(--border) !important;
            color: var(--text-primary) !important;
            border-radius: 12px;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent) !important;
            box-shadow: var(--shadow-focus) !important;
            outline: none !important;
        }

        .bg-white {
            background-color: var(--surface) !important;
        }

        .bg-gray-800,
        .bg-gray-900,
        .bg-slate-800,
        .bg-\[\#020617\] {
            background-color: var(--surface) !important;
            color: var(--text-primary) !important;
        }

        .bg-gray-700 {
            background-color: var(--surface) !important;
            color: var(--text-primary) !important;
        }

        .bg-gray-50,
        .bg-gray-100 {
            background-color: var(--surface-secondary) !important;
        }

        .text-gray-100,
        .text-gray-200,
        .dark\:text-gray-100 {
            color: var(--text-primary) !important;
        }

        .border-gray-700,
        .border-gray-200,
        .border-gray-300 {
            border-color: var(--border-hairline) !important;
        }

        .rounded-2xl {
            border-radius: 20px;
        }

        .shadow-md,
        .shadow-lg,
        .shadow-sm {
            box-shadow: var(--shadow-card) !important;
        }

        .shadow-2xl,
        .shadow-xl {
            box-shadow: var(--shadow-modal) !important;
        }

        .bg-black\/70,
        .bg-black\/60 {
            background-color: rgba(0, 0, 0, 0.30) !important;
        }

        .bg-gradient-to-r,
        .bg-gradient-to-br {
            background-image: none !important;
        }

        .from-blue-600,
        .to-blue-400 {
            background-color: var(--accent) !important;
            color: #FFFFFF !important;
        }

        /* ✨ Efek halus saat scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>

    @stack('head')
</head>

<body class="text-gray-900 dark:text-gray-100{{ request()->routeIs('pos') ? ' is-pos' : ' h-screen' }}">
    @include('partials.app-boot')
    {{-- NAVBAR --}}
    <header
        class="flex items-center justify-between h-16 px-3 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shadow-sm z-40 relative">

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
                <div class="relative z-[9999]">
                    <div x-show="openUserMenu" x-cloak @click.outside="openUserMenu = false" x-transition.scale.origin.top.right
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-2 z-[9999]">

                        <a href="#"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" @click.stop
                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- LAYOUT --}}
    <div class="app-shell flex h-[calc(100vh-64px)] overflow-hidden relative z-0">
        {{-- Konten utama --}}
        <main class="flex-1 p-4 overflow-y-auto relative z-0 min-w-0">
            @yield('content')
        </main>
    </div>

    {{-- Sidebar di luar shell supaya tidak terpotong --}}
    <aside
        class="app-sidebar fixed top-16 left-0 bottom-0 w-60 bg-gray-50 dark:bg-gray-800 border-r dark:border-gray-700 shadow-xl z-30"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <nav class="space-y-2 p-4 h-full overflow-y-auto">
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

    <div x-show="sidebarOpen" x-cloak @click="toggleSidebar"
        class="app-sidebar-overlay fixed top-16 inset-x-0 bottom-0 bg-black/50 z-20"
        x-transition.opacity></div>

</body>

</html>

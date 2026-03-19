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

    <div x-data="{ page: 'dashboard' }" class="min-h-screen pb-20">

        <!-- CONTENT -->
        <div class="p-5">
            @yield('content')
        </div>

        <!-- 🔥 BOTTOM NAV -->
        <div class="fixed bottom-0 left-0 right-0 bg-[#0E1524] border-t border-gray-700 flex justify-around py-2">

            <button @click="page = 'dashboard'" :class="page === 'dashboard' ? 'text-blue-400' : 'text-gray-400'">
                🏠 Dashboard
            </button>

            <button @click="page = 'analytics'" :class="page === 'analytics' ? 'text-blue-400' : 'text-gray-400'">
                📊 Analytics
            </button>

            <button @click="page = 'outlet'" :class="page === 'outlet' ? 'text-blue-400' : 'text-gray-400'">
                🏪 Outlet
            </button>

        </div>

    </div>

</body>

</html>

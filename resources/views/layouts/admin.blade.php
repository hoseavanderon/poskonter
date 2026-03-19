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
        <div class="px-5 pt-5">

            <div x-data="tabNav()" x-init="init()" class="mb-6">

                <div class="flex gap-6 text-sm font-medium relative">

                    <template x-for="tab in tabs" :key="tab">
                        <button @click="select(tab, $event)" class="relative pb-1"
                            :class="selected === tab ? 'text-white' : 'text-gray-400'"
                            x-text="tab === 'all' ? 'All' : (tab === 'santuy' ? 'Santuy Cell' : 'Tian Cell')">
                        </button>
                    </template>

                    <!-- UNDERLINE -->
                    <div class="absolute bottom-0 h-[2px] bg-blue-500 transition-all duration-300"
                        :style="`width:${width}px; transform:translateX(${left}px)`">
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

            <!-- HOME -->
            <button @click="page = 'home'" class="flex flex-col items-center text-xs transition"
                :class="page === 'home' ? 'text-white' : 'text-gray-500'">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 4l9 6.5M5 9.5V20h14V9.5" />
                </svg>

                Home
            </button>

            <!-- PRODUK -->
            <button @click="page = 'product'" class="flex flex-col items-center text-xs transition"
                :class="page === 'product' ? 'text-white' : 'text-gray-500'">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M6 7v13m12-13v13M4 20h16" />
                </svg>

                Produk
            </button>

            <!-- INSIGHT -->
            <button @click="page = 'insight'" class="flex flex-col items-center text-xs transition"
                :class="page === 'insight' ? 'text-white' : 'text-gray-500'">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3v18M4 12h16" />
                </svg>

                Insight
            </button>

            <!-- SETTINGS -->
            <button @click="page = 'settings'" class="flex flex-col items-center text-xs transition"
                :class="page === 'settings' ? 'text-white' : 'text-gray-500'">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.325 4.317a1 1 0 011.35-.936l1.7.98a1 1 0 001.1 0l1.7-.98a1 1 0 011.35.936l.325 1.89a1 1 0 00.757.77l1.89.325a1 1 0 01.936 1.35l-.98 1.7a1 1 0 000 1.1l.98 1.7a1 1 0 01-.936 1.35l-1.89.325a1 1 0 00-.77.757l-.325 1.89a1 1 0 01-1.35.936l-1.7-.98a1 1 0 00-1.1 0l-1.7.98a1 1 0 01-1.35-.936l-.325-1.89a1 1 0 00-.757-.77l-1.89-.325a1 1 0 01-.936-1.35l.98-1.7a1 1 0 000-1.1l-.98-1.7a1 1 0 01.936-1.35l1.89-.325a1 1 0 00.77-.757l.325-1.89z" />
                </svg>

                Menu
            </button>

        </div>

    </div>

    <script>
        function tabNav() {
            return {
                tabs: ['all', 'santuy', 'tian'],
                selected: 'all',
                width: 0,
                left: 0,

                init() {
                    this.$nextTick(() => {
                        let el = this.$el.querySelector('button')
                        this.setIndicator(el)
                    })
                },

                select(tab, event) {
                    this.selected = tab
                    this.setIndicator(event.target)
                },

                setIndicator(el) {
                    this.width = el.offsetWidth
                    this.left = el.offsetLeft
                }
            }
        }
    </script>

</body>

</html>

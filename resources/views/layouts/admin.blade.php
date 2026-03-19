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

<body class="bg-[#020617] text-white">

    <div x-data="{
        page: 'home',
        selectedOutlet: 'all'
    }" class="min-h-screen pb-28">

        <!-- CONTENT -->
        <div class="px-4 sm:px-6 pt-4 pb-24">

            <div x-data="tabNav()" x-init="init()" class="mb-6">

                <div class="flex gap-5 text-sm font-medium relative overflow-x-auto no-scrollbar">

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
        <div x-data="{
            width: 0,
            left: 0,
            init() {
                this.$nextTick(() => {
                    let el = this.$el.querySelector('button')
                    this.set(el)
                })
            },
            select(page, event) {
                this.page = page
                this.set(event.currentTarget)
            },
            set(el) {
                let inner = el.querySelector('div')
        
                this.width = inner.offsetWidth
        
                let parentRect = this.$el.getBoundingClientRect()
                let elRect = el.getBoundingClientRect()
        
                this.left = (elRect.left - parentRect.left) + (elRect.width / 2) - (inner.offsetWidth / 2)
            }
        }" x-init="init()"
            class="fixed bottom-3 left-1/2 -translate-x-1/2 w-[94%] max-w-md z-50 px-1">

            <div
                class="relative 
            bg-[#020617]/90 backdrop-blur-2xl 
            border border-white/5 
            rounded-2xl px-2 py-2 
            flex items-center shadow-[0_10px_40px_rgba(0,0,0,0.6)]">

                <!-- HOME -->
                <button @click="select('home', $event)" class="relative z-10 flex justify-center w-full">

                    <div class="flex flex-col items-center text-xs px-4 py-1"
                        :class="page === 'home' ? 'text-white' : 'text-gray-400'">

                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10.5L12 4l9 6.5M5 9.5V20h14V9.5" />
                        </svg>

                        Home
                    </div>
                </button>

                <!-- PRODUK -->
                <button @click="select('product', $event)" class="relative z-10 flex justify-center w-full">

                    <div class="flex flex-col items-center text-xs px-4 py-1"
                        :class="page === 'product' ? 'text-white' : 'text-gray-400'">

                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M9 19a1 1 0 100 2 1 1 0 000-2zm6 0a1 1 0 100 2 1 1 0 000-2z" />
                        </svg>

                        Produk
                    </div>
                </button>

                <!-- INSIGHT -->
                <button @click="select('insight', $event)" class="relative z-10 flex justify-center w-full">

                    <div class="flex flex-col items-center text-xs px-4 py-1"
                        :class="page === 'insight' ? 'text-white' : 'text-gray-400'">

                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3v18M4 12h16" />
                        </svg>

                        Insight
                    </div>
                </button>

                <!-- SETTINGS -->
                <button @click="select('settings', $event)" class="relative z-10 flex justify-center w-full">

                    <div class="flex flex-col items-center text-xs px-4 py-1"
                        :class="page === 'settings' ? 'text-white' : 'text-gray-400'">

                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317a1 1 0 011.35-.936l1.7.98a1 1 0 001.1 0l1.7-.98a1 1 0 011.35.936l.325 1.89a1 1 0 00.757.77l1.89.325a1 1 0 01.936 1.35l-.98 1.7a1 1 0 000 1.1l.98 1.7a1 1 0 01-.936 1.35l-1.89.325a1 1 0 00-.77.757l-.325 1.89a1 1 0 01-1.35.936l-1.7-.98a1 1 0 00-1.1 0l-1.7.98a1 1 0 01-1.35-.936l-.325-1.89a1 1 0 00-.757-.77l-1.89-.325a1 1 0 01-.936-1.35l.98-1.7a1 1 0 000-1.1l-.98-1.7a1 1 0 01.936-1.35l1.89-.325a1 1 0 00.77-.757l.325-1.89z" />
                        </svg>

                        Menu
                    </div>
                </button>

            </div>
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
                        requestAnimationFrame(() => {
                            let el = this.$el.querySelector('button')
                            this.set(el)
                        })
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
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Admin POS</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#020617] text-white">

    <main class="w-full min-h-screen bg-[#020617] relative overflow-hidden md:max-w-none md:mx-0">
        <div x-data="tabNav()" x-init="init()" class="min-h-screen pb-28">

            <!-- CONTENT -->
            <div class="px-4 md:px-8 lg:px-12 pt-4 pb-24 max-w-7xl mx-auto">

                <!-- TOP RIGHT USER DROPDOWN -->
                <div class="flex justify-end mb-4" x-data="{ open: false }">

                    <div class="relative">

                        <!-- BUTTON -->
                        <button @click="open = !open"
                            class="flex items-center gap-2 bg-white/5 hover:bg-white/10 
                   border border-white/10 px-3 py-2 rounded-xl transition">

                            <!-- ICON -->
                            <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-7 2-7 4v2h14v-2c0-2-3-4-7-4z" />
                            </svg>

                            <!-- USER NAME -->
                            <span class="text-sm text-white/80">
                                {{ Auth::user()->name ?? 'User' }}
                            </span>

                            <!-- ARROW -->
                            <svg class="w-3 h-3 text-white/50" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <!-- DROPDOWN -->
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 
                   bg-[#020617]/95 backdrop-blur-xl 
                   border border-white/10 
                   rounded-xl shadow-lg overflow-hidden z-50">

                            <!-- USER INFO -->
                            <div class="px-4 py-3 text-xs text-white/50 border-b border-white/10">
                                Logged in as <br>
                                <span class="text-white text-sm font-medium">
                                    {{ Auth::user()->name ?? '-' }}
                                </span>
                            </div>

                            <!-- LOGOUT -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit"
                                    class="w-full text-left px-4 py-3 text-sm text-red-400 
                           hover:bg-white/5 transition flex items-center gap-2">

                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                                    </svg>

                                    Logout
                                </button>
                            </form>

                        </div>

                    </div>
                </div>

                <div class="mb-6 mt-3">

                    <div class="flex gap-5 text-sm font-medium relative overflow-x-auto no-scrollbar">

                        <template x-for="tab in tabs" :key="tab.key">
                            <button class="tab-button relative pb-1" @click="select(tab, $event)"
                                :class="selected === tab.key ? 'text-white' : 'text-white/40'" x-text="tab.name">
                            </button>
                        </template>

                        <!-- UNDERLINE -->
                        <div class="absolute bottom-0 h-[2px] bg-white/70 rounded-full transition-all duration-300"
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
                            :class="page === 'home' ? 'text-white' : 'text-white/40'">

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
                            :class="page === 'product' ? 'text-white' : 'text-white/40'">

                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M9 19a1 1 0 100 2 1 1 0 000-2zm6 0a1 1 0 100 2 1 1 0 000-2z" />
                            </svg>

                            Produk
                        </div>
                    </button>

                    <!-- DETAIL -->
                    <button @click="select('detail', $event)" class="relative z-10 flex justify-center w-full">

                        <div class="flex flex-col items-center text-xs px-4 py-1"
                            :class="page === 'detail' ? 'text-white' : 'text-white/40'">

                            <!-- Store Icon -->
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 9l1-4h16l1 4M4 9h16v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9z" />
                            </svg>

                            Detail
                        </div>
                    </button>

                    <!-- ASSET CALCULATOR -->
                    <button @click="select('calculator', $event)" class="relative z-10 flex justify-center w-full">

                        <div class="flex flex-col items-center text-xs px-4 py-1"
                            :class="page === 'calculator' ? 'text-white' : 'text-white/40'">

                            <!-- ICON CALCULATOR -->
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 2h6a2 2 0 012 2v16a2 2 0 01-2 2H9a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 7h6M9 11h2m2 0h2M9 15h2m2 0h2" />
                            </svg>

                            Asset
                        </div>
                    </button>

                </div>
            </div>

        </div>
    </main>

    <script>
        window.addEventListener('error', function(e) {
            console.log('🔥 GLOBAL ERROR:', e.message)
            console.log(e)
        })

        function tabNav() {
            return {
                data: [],
                tabs: [{
                        key: 'all',
                        name: 'All'
                    },
                    @foreach ($outlets as $outlet)
                        {
                            key: '{{ $outlet->id }}',
                            name: '{{ $outlet->name }}'
                        },
                    @endforeach
                ],

                // 🔥 default selected (no URL lagi, pure state)
                selected: 'all',
                period: 'day',

                // 🔥 data dari backend
                stats: {
                    todaySales: 0,
                    totalTransactions: 0,
                    monthlySales: 0,
                    todayProfit: 0,
                    growth: 0,
                },

                width: 0,
                left: 0,
                page: 'home',

                // 🚀 INIT
                init() {
                    console.log('🚀 INIT START')

                    this.$nextTick(() => {
                        setTimeout(() => {

                            console.log('👉 selected:', this.selected)

                            let index = this.tabs.findIndex(t => t.key == this.selected)
                            let buttons = this.$el.querySelectorAll('.tab-button')

                            console.log('👉 buttons:', buttons)

                            if (buttons[index]) {
                                this.setIndicator(buttons[index])
                            }

                            if (this.$refs.day) {
                                this.updateIndicator(this.$refs.day)
                            } else {
                                console.warn('⚠️ day ref tidak ditemukan')
                            }

                            this.loadData(this.selected)

                        }, 50)
                    })
                },

                // 🚀 CLICK TAB
                async select(tab, event) {
                    this.selected = tab.key
                    this.setIndicator(event.currentTarget)

                    await this.loadData(tab.key)
                },

                // 🚀 FETCH DATA
                async loadData(outlet, period = this.period) {
                    console.log('📡 FETCH START:', {
                        outlet,
                        period
                    })

                    try {
                        let res = await fetch(`/admin/dashboard-data?outlet=${outlet}&period=${period}`)
                        console.log('📡 RESPONSE STATUS:', res.status)

                        let data = await res.json()
                        console.log('📦 DATA:', data)

                        this.stats.todaySales = data.todaySales
                        this.stats.totalTransactions = data.totalTransactions
                        this.stats.monthlySales = data.monthlySales
                        this.stats.todayProfit = data.todayProfit
                        this.stats.growth = data.growth

                        console.log('✅ STATS UPDATED')

                    } catch (e) {
                        console.error('❌ FETCH ERROR:', e)
                    }
                },

                get activeOutletName() {
                    let found = this.tabs.find(t => t.key == this.selected)
                    return found ? found.name : 'All'
                },

                // 🎯 INDICATOR
                setIndicator(el) {
                    this.width = el.offsetWidth
                    this.left = el.offsetLeft
                },

                changePeriod(p, el) {
                    this.period = p

                    this.$nextTick(() => {
                        this.updateIndicator(el)
                    })

                    this.loadData(this.selected, p)
                },
                updateIndicator(el) {
                    const indicator = this.$refs.mainPeriodIndicator

                    if (!indicator || !el) {
                        console.warn('❌ indicator not ready', indicator)
                        return
                    }

                    indicator.style.width = el.offsetWidth + 'px'
                    indicator.style.left = el.offsetLeft + 'px'
                },
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

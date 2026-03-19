<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin POS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#0f172a] text-white">

    <div x-data="app()" x-init="init()" class="min-h-screen pb-28">

        <!-- CONTENT -->
        <div class="px-5 pt-5">

            <!-- TOP TAB -->
            <div x-data="tabNav()" x-init="init()" class="mb-6">
                <div class="flex gap-6 text-sm font-medium relative">

                    <template x-for="tab in tabs" :key="tab">
                        <button @click="select(tab, $event)" :class="selected === tab ? 'text-white' : 'text-gray-400'">
                            <span
                                x-text="tab === 'all' ? 'All' : (tab === 'santuy' ? 'Santuy Cell' : 'Tian Cell')"></span>
                        </button>
                    </template>

                    <div class="absolute bottom-0 h-[2px] bg-blue-500 transition-all duration-300"
                        :style="`width:${width}px; transform:translateX(${left}px)`">
                    </div>

                </div>
            </div>

            <!-- PAGE -->
            @yield('content')

        </div>

        <!-- 🔥 BOTTOM NAV -->
        <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[92%] max-w-md z-50">

            <div class="relative bg-[#0b1220]/80 backdrop-blur-xl border border-white/10 rounded-2xl px-2 py-2 flex">

                <!-- INDICATOR -->
                <div class="absolute left-0 top-1 bottom-1 rounded-xl bg-white/10 transition-all duration-300"
                    :style="`width:${navWidth}px; transform:translateX(${navLeft}px)`">
                </div>

                <!-- NAV BUTTON -->
                <template x-for="item in navItems" :key="item.page">
                    <button @click="changePage(item.page, $event)" class="flex-1 flex justify-center relative z-10">

                        <div class="flex flex-col items-center text-xs px-4 py-1"
                            :class="page === item.page ? 'text-white' : 'text-gray-400'">

                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path :d="item.icon" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <span x-text="item.label"></span>
                        </div>

                    </button>
                </template>

            </div>
        </div>

    </div>

    <script>
        function app() {
            return {
                page: 'home',

                navWidth: 0,
                navLeft: 0,

                navItems: [{
                        page: 'home',
                        label: 'Home',
                        icon: 'M3 10.5L12 4l9 6.5M5 9.5V20h14V9.5'
                    },
                    {
                        page: 'product',
                        label: 'Produk',
                        icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4'
                    },
                    {
                        page: 'insight',
                        label: 'Insight',
                        icon: 'M11 3v18M4 12h16'
                    },
                    {
                        page: 'settings',
                        label: 'Menu',
                        icon: 'M12 6v6l4 2'
                    }
                ],

                init() {
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            this.updateIndicator()
                        })
                    })
                },

                changePage(page, event) {
                    this.page = page
                    this.updateIndicator(event?.currentTarget)
                },

                updateIndicator(el = null) {
                    if (!el) {
                        let buttons = this.$el.querySelectorAll('button')
                        el = Array.from(buttons).find(btn =>
                            btn.innerText.toLowerCase().includes(this.page)
                        )
                    }

                    if (!el) return

                    let inner = el.querySelector('div')

                    let parent = el.closest('.relative')
                    let parentRect = parent.getBoundingClientRect()
                    let elRect = el.getBoundingClientRect()

                    this.navWidth = inner.offsetWidth
                    this.navLeft = (elRect.left - parentRect.left) + (elRect.width / 2) - (inner.offsetWidth / 2)
                }
            }
        }

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

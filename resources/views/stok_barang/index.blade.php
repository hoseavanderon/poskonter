    @extends('layouts.app')

    @section('content')
        <div x-data="stokBarang()" x-init="init()" class="p-6 max-w-7xl mx-auto">
            <!-- 🏷️ Judul -->
            <div class="mb-8 text-left space-y-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">📦 Stok Barang</h2>
                    <p class="text-sm text-gray-500">Monitor semua jenis barang</p>
                </div>

                <!-- 🔍 Form Pencarian -->
                <div class="relative max-w-md">
                    <input 
                        type="text"
                        placeholder="Cari produk... (contoh: LCD Samsung A02)"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                    >
                    
                    <!-- Icon Search -->
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        🔍
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="(rack, i) in shelves" :key="i">
                    <div @click="openShelf(rack)"
                        class="relative bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-blue-400 transition-all duration-300 cursor-pointer text-center">

                        <!-- 🔢 Jumlah Items -->
                        <div
                            class="absolute top-3 left-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-full shadow-sm">
                            <span x-text="rack.products.length"></span>
                            <span x-text="rack.products.length === 1 ? 'item' : 'items'"></span>
                        </div>

                        <!-- 🔴 Low Stock -->
                        <template x-if="rack.lowStock > 0">
                            <div
                                class="absolute top-3 right-3 bg-red-50 dark:bg-red-900/40 text-red-500 text-xs font-semibold px-2 py-1 rounded-full animate-pulse">
                                <span x-text="rack.lowStock"></span> Low
                            </div>
                        </template>

                        <!-- 🧺 Icon di Tengah -->
                        <div class="flex justify-center mb-4 mt-2">
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-xl flex items-center justify-center">
                                <x-heroicon-o-archive-box class="w-7 h-7 text-gray-600 dark:text-gray-300" />
                            </div>
                        </div>

                        <!-- 📋 Nama & Kode Rak -->
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-0.5" x-text="rack.name"></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="rack.code"></p>
                    </div>
                </template>
            </div>

            <!-- Modal Detail Rak -->
            <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                @click.self="closeModal" x-transition.opacity>

                <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-3xl p-6 shadow-2xl overflow-y-auto max-h-[85vh]"
                    x-transition.scale>

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="selectedRack?.name">
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                x-text="selectedRack ? selectedRack.products.length + ' products' : ''"></p>
                        </div>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            ✕
                        </button>
                    </div>

                    <!-- Daftar Produk -->
                    <template x-if="filteredProducts.length > 0">
                        <div class="space-y-5">
                            <template x-for="(product, i) in filteredProducts" :key="i">
                                <div
                                    class="rounded-2xl bg-gray-50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700 p-5">

                                    <!-- Nama Produk -->
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-[15px] font-semibold text-gray-800 dark:text-gray-100"
                                            x-text="product.name"></h4>

                                        <!-- 🔵 Tombol Copy -->
                                        <button 
                                            @click="copyProduct(product, i)"
                                            :class="copiedId === i 
                                                ? 'bg-green-500 text-white scale-105' 
                                                : 'bg-gray-200 dark:bg-gray-700 hover:bg-blue-500 hover:text-white'"
                                            class="text-xs px-3 py-1 rounded-lg transition-all duration-300 font-medium">

                                            <span x-show="copiedId !== i">Copy</span>
                                            <span x-show="copiedId === i">Copied ✓</span>
                                        </button>
                                    </div>

                                    <!-- Barcode dan Total Stok -->
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span
                                            class="text-xs px-3 py-1 rounded-md bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-medium">
                                            Barcode: <span x-text="product.barcode"></span>
                                        </span>
                                        <span
                                            class="text-xs px-3 py-1 rounded-md bg-blue-50 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-300 font-medium">
                                            Total Stok: <span x-text="product.stok + ' pcs'"></span>
                                        </span>

                                        <span
                                            class="text-xs px-3 py-1 rounded-md bg-green-50 dark:bg-green-900/40 border border-green-200 dark:border-green-700 text-green-600 dark:text-green-300 font-medium">
                                            Harga: <span x-text="formatRupiah(product.price)"></span>
                                        </span>                                        
                                    </div>

                                    <!-- Loop Attribute -->
                                    <template x-for="attr in product.attributes" :key="attr.name">
                                        <div class="mb-3">
                                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium mb-2"
                                                x-text="attr.name"></p>
                                            <div class="flex flex-wrap gap-3">
                                                <template x-for="val in attr.values" :key="val.value">
                                                    <div
                                                        class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-center flex flex-col justify-center shadow-sm hover:shadow-md transition">
                                                        <p class="text-[13px] font-medium text-gray-800 dark:text-gray-100"
                                                            x-text="val.value"></p>
                                                        <p class="text-[12px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                            <span x-text="val.stok"></span> pcs
                                                        </p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Tidak Ada Produk -->
                    <template x-if="filteredProducts.length === 0">
                        <div class="text-center py-10 text-gray-500 dark:text-gray-400 italic">
                            Tidak ada barang ditemukan.
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <script>
            function stokBarang() {
                return {
                    showModal: false,
                    shelves: [],
                    selectedRack: null,
                    searchQuery: '',
                    isLoading: true, // 🔹 Tambahkan flag loading
                    copiedId: null,

                    async init() {
                        try {
                            const res = await fetch("{{ route('stok.data') }}");
                            const data = await res.json();

                            this.shelves = data.map(shelf => ({
                                id: shelf.id,
                                name: shelf.name,
                                code: shelf.code,
                                icon: shelf.icon ?? '📦',
                                products: shelf.products.map(p => ({
                                    name: p.name,
                                    barcode: p.barcode,
                                    stok: p.stok,
                                    minimal_stok: p.minimal_stok,
                                    price: p.price, // 🔥 tambahkan ini
                                    attributes: p.attributes
                                })),
                                lowStock: shelf.products.filter(p => p.stok < p.minimal_stok).length
                            }));
                        } catch (e) {
                            console.error('Gagal memuat data rak:', e);
                        } finally {
                            this.isLoading = false; // 🔹 Selesai loading
                        }
                    },

                    copyProduct(product, index) {
                        const text = `${product.name} - ${this.formatRupiah(product.price)}`;
                        navigator.clipboard.writeText(text);

                        this.copiedId = index;

                        setTimeout(() => {
                            this.copiedId = null;
                        }, 1500);
                    },

                    get filteredProducts() {
                        if (!this.selectedRack) return [];
                        if (!this.searchQuery) return this.selectedRack.products;

                        const q = this.searchQuery.toLowerCase();
                        return this.selectedRack.products.filter(p =>
                            p.name.toLowerCase().includes(q) ||
                            p.barcode.toLowerCase().includes(q)
                        );
                    },

                    formatRupiah(value) {
                        if (!value) return 'Rp 0';

                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(value);
                    },

                    openShelf(rack) {
                        this.selectedRack = rack;
                        this.searchQuery = '';
                        this.showModal = true;
                        document.body.classList.add('overflow-hidden');
                    },

                    closeModal() {
                        this.showModal = false;
                        document.body.classList.remove('overflow-hidden');
                    },
                };
            }
        </script>
    @endsection

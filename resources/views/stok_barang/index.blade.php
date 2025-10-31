@extends('layouts.app')

@section('content')
<div x-data="stokBarang()" x-init="init()" class="p-6 max-w-7xl mx-auto">
    <!-- 🏷️ Judul -->
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">📦 Stok Barang</h2>
        <p class="text-sm text-gray-500">Monitor semua jenis barang</p>
    </div>

    <!-- 🧱 Daftar Rak -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 🔁 Loop daftar rak -->
        <template x-for="(rack, i) in shelves" :key="i">
            <div @click="openShelf(rack)"
                class="relative bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-blue-400 cursor-pointer"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                
                <!-- 🔴 Badge low stock -->
                <template x-if="rack.lowStock > 0">
                    <div
                        class="absolute top-3 right-3 bg-red-100 text-red-600 text-xs font-semibold px-2 py-1 rounded-full shadow-sm animate-pulse">
                        <span x-text="rack.lowStock"></span> Low
                    </div>
                </template>

                <!-- 🧺 Icon -->
                <div class="mb-3 flex justify-center">
                    <template x-if="rack.icon_component">
                        <component :is="`x-heroicon-o-${rack.icon_component}`" class="w-8 h-8 text-blue-400"></component>
                    </template>
                </div>

                <!-- 📋 Info -->
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1" x-text="rack.name"></h3>
                <p class="text-sm text-gray-500 mb-3" x-text="rack.code"></p>

                <div class="flex justify-between items-center text-sm text-gray-500">
                    <div class="flex items-center gap-1">
                        <x-heroicon-o-cube class="w-4 h-4" />
                        <span x-text="rack.products.length + ' products'"></span>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4" />
                </div>
            </div>
        </template>

        <!-- 🟡 Pesan jika belum ada rak -->
        <template x-if="!isLoading && shelves.length === 0">
            <div class="col-span-full text-center py-16 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                <x-heroicon-o-archive-box class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3" />
                <p class="text-base font-medium">Belum ada rak</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tambahkan rak baru untuk mulai menyimpan produk.</p>
            </div>
        </template>

    </div>

    <!-- 📋 Modal Detail Rak -->
    <div x-show="showModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        @click.self="closeModal">

        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-6"
            class="bg-white dark:bg-gray-800 rounded-2xl max-w-3xl w-full p-6 shadow-2xl overflow-y-auto max-h-[85vh] transform">

            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-700 pb-3">
                <div>
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="selectedRack?.name"></h3>
                    <p class="text-sm text-gray-500" x-text="selectedRack?.code"></p>
                </div>
                <button @click="closeModal"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 transform hover:rotate-90">
                    ✕
                </button>
            </div>

            <!-- 🔍 Search -->
            <div class="mb-5">
                <input type="text" x-model="searchQuery" placeholder="Cari nama barang..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm text-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <!-- Daftar Produk -->
            <template x-if="filteredProducts.length > 0">
                <div class="space-y-5">
                    <template x-for="(product, i) in filteredProducts" :key="i">
                        <div
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            :class="{'border-red-300 bg-red-50 dark:bg-red-900/20': product.stok < product.minimal_stok}"
                            class="border rounded-xl p-4 dark:border-gray-700 shadow-sm hover:shadow-md transition">

                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-100" x-text="product.name"></h4>
                                    <p class="text-xs text-gray-500">Barcode: <span x-text="product.barcode"></span></p>
                                </div>
                                <template x-if="product.stok < product.minimal_stok">
                                    <span
                                        class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full font-semibold animate-pulse">
                                        ⚠ Low Stock
                                    </span>
                                </template>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                <div>
                                    <p class="text-gray-500">Stok Sekarang</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                                        <span x-text="product.stok"></span> pcs
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Minimum Stock</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                                        <span x-text="product.minimal_stok"></span> pcs
                                    </p>
                                </div>
                            </div>

                            <!-- 🧩 Attributes -->
                            <div class="flex flex-wrap gap-2">
                                <template x-for="attr in product.attributes" :key="attr.name">
                                    <span
                                        class="text-xs border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 transition hover:scale-105">
                                        <span class="font-semibold" x-text="attr.name + ':'"></span>
                                        <span x-text="attr.value"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- 🔸 Tidak ada hasil -->
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

        get filteredProducts() {
            if (!this.selectedRack) return [];
            if (!this.searchQuery) return this.selectedRack.products;

            const q = this.searchQuery.toLowerCase();
            return this.selectedRack.products.filter(p =>
                p.name.toLowerCase().includes(q) ||
                p.barcode.toLowerCase().includes(q)
            );
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

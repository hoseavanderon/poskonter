@extends('layouts.app')

@section('content')
    <style>
        /* ==== 🌙 Modern Scrollbar Style ==== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #9ca3af, #6b7280);
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4b5563, #374151);
        }

        /* 🔹 Dark mode scrollbar */
        .dark ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #4b5563, #374151);
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #6b7280, #9ca3af);
        }

        /* 🔹 Smooth scrolling behavior */
        html {
            scroll-behavior: smooth;
        }

        /* 🔹 Scroll area khusus modal */
        .modal-scroll {
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: #9ca3af transparent;
        }

        [x-cloak] { display: none !important; }
    </style>

    <main>
        <div x-data="posApp()" x-init="init()" class="flex flex-col gap-4">

            {{-- Tabs atas --}}
            <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <!-- 🧭 Tombol Tab -->
                <div class="flex gap-2">
                    <button @click="activeTab = 'physical'"
                        :class="activeTab === 'physical'
                            ? 'bg-blue-600 text-white dark:bg-blue-700'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                        class="px-4 py-2 rounded-lg font-semibold transition">
                        🛍️ Produk Fisik
                    </button>

                    <button @click="activeTab = 'digital'"
                        :class="activeTab === 'digital'
                            ? 'bg-blue-600 text-white dark:bg-blue-700'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                        class="px-4 py-2 rounded-lg font-semibold transition">
                        ⚡ Produk Digital
                    </button>
                    <!-- 🧾 Tombol Tutup Buku -->
                    <button @click="handleCloseBook()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-4 py-2 rounded-lg shadow transition">
                        📘 Tutup Buku
                    </button>
                </div>
            </div>

            <!-- 📘 Modal Tutup Buku -->
            <div
                x-show="showCloseBookModal"
                x-transition.opacity
                @keydown.escape.window="showCloseBookModal = false"
                @click.self="showCloseBookModal = false"
                class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4"
                x-cloak
            >
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-gray-800 dark:text-gray-200">

                    <!-- ❌ Tombol close -->
                    <button
                        @click="showCloseBookModal = false"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        ✖
                    </button>

                    <!-- 🧾 Isi laporan -->
                    <template x-if="closeBookData">
                        <div>
                            <h2 class="text-lg font-bold mb-5 text-center leading-tight">
                                Rincian Transaksi Tanggal <span x-text="closeBookData.tanggal"></span>
                            </h2>

                            <div class="space-y-3 text-sm">
                                <!-- Barang -->
                                <div class="flex justify-between">
                                    <span>Barang :</span>
                                    <span x-text="formatRupiah(closeBookData.barangTotal)"></span>
                                </div>

                                <!-- Digital per App -->
                                <template x-for="app in closeBookData.digitalPerApp" :key="app.name">
                                    <div class="flex justify-between">
                                        <span x-text="app.name + ':'"></span>
                                        <span x-text="formatRupiah(app.total)"></span>
                                    </div>
                                </template>

                                <hr class="my-2 border-gray-600">
                                <div class="flex justify-between font-semibold">
                                    <span>Total Penjualan :</span>
                                    <span x-text="formatRupiah(closeBookData.totalPenjualan)"></span>
                                </div>

                                <hr class="my-3 border-gray-700 opacity-70">

                                <!-- Utang -->
                                <template x-if="closeBookData.utangList.length > 0">
                                    <div class="pt-1">
                                        <div class="font-semibold mb-1">Utang :</div>
                                        <template x-for="u in closeBookData.utangList" :key="u.name">
                                            <div class="flex justify-between">
                                                <span x-text="u.name"></span>
                                                <span class="text-red-500" x-text="'(' + formatRupiah(u.subtotal) + ')'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <hr class="my-3 border-gray-700 opacity-70">

                                <div class="flex justify-between font-semibold">
                                    <span>Total Setelah Utang :</span>
                                    <span x-text="formatRupiah(closeBookData.totalSetelahUtang)"></span>
                                </div>

                                <div class="flex justify-between font-bold text-lg border-t pt-3 mt-2">
                                    <span>Grand Total :</span>
                                    <span x-text="formatRupiah(closeBookData.grandTotal)"></span>
                                </div>

                                <!-- Lebih input -->
                                <div class="flex justify-between items-center mt-2">
                                    <span>Lebih :</span>
                                    <input
                                        type="text"
                                        x-on:input="formatLebihInput($event)"
                                        class="w-32 text-right bg-transparent border-0 border-b border-gray-700 focus:border-blue-400 focus:outline-none focus:ring-0 text-gray-300 appearance-none transition-colors duration-150"
                                        placeholder="0"
                                    >
                                </div>

                                <div class="flex justify-between font-bold text-lg border-t pt-3 mt-2 text-green-400">
                                    <span>Grand Total Akhir :</span>
                                    <span x-text="formatRupiah((closeBookData.grandTotal ?? 0) + (lebih || 0))"></span>
                                </div>

                                <hr class="my-2 border-gray-700 opacity-60">

                                <div class="flex justify-between text-sm text-gray-400">
                                    <span>Total Transfer :</span>
                                    <span x-text="formatRupiah(closeBookData.totalTransfer)"></span>
                                </div>

                                <div class="flex justify-between text-sm text-gray-400">
                                    <span>Total Tarik :</span>
                                    <span x-text="formatRupiah(closeBookData.totalTarik)"></span>
                                </div>
                            </div>

                            <!-- Tombol aksi -->
                            <div class="flex justify-end gap-3 mt-6 border-t border-gray-700 pt-4">
                                <button
                                    @click="copyCloseBook"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300"
                                    :class="copied
                                        ? 'bg-green-600 text-white scale-105 shadow-[0_0_15px_rgba(16,185,129,0.6)]'
                                        : 'bg-gray-700 hover:bg-gray-600 text-white'">
                                    <template x-if="!copied">
                                        <span class="flex items-center gap-2">📋 Copy</span>
                                    </template>
                                    <template x-if="copied">
                                        <span class="flex items-center gap-2">✅ Disalin!</span>
                                    </template>
                                </button>

                                <button
                                    @click="handleFinalCloseBook()"
                                    class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm transition">
                                    ✅ Tutup Buku
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Loading state -->
                    <div x-show="!closeBookData" class="text-center py-4 text-gray-500">
                        Memuat data...
                    </div>
                </div>
            </div>

           {{-- ============================= --}}
            {{-- TAB: PRODUK FISIK --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'physical'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-3" class="flex flex-col md:flex-row gap-4">

                {{-- Sidebar kategori --}}
                <aside
                    class="md:w-52 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 hidden md:block">
                    <h2 class="text-lg font-semibold mb-3">Kategori Barang</h2>
                    <ul class="space-y-2">
                        <li>
                            <button @click="switchCategory(null)"
                                :class="{
                                    'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedCategory ===
                                        null
                                }"
                                class="w-full text-left px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <i class="fa-solid fa-layer-group mr-1"></i> Semua Produk
                            </button>
                        </li>
                        <template x-for="cat in categories" :key="cat.id">
                            <li>
                                <button @click="switchCategory(cat)"
                                    :class="{
                                        'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedCategory
                                            ?.id === cat.id
                                    }"
                                    class="w-full text-left px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <i class="fa-solid fa-folder mr-1"></i>
                                    <span x-text="cat.name"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </aside>

                {{-- Produk --}}
                <div class="flex-1 relative">
                    {{-- 🧾 Form Scan Barcode --}}
                    <div class="relative mb-4">
                        <input 
                            id="barcodeInput"
                            type="text"
                            placeholder="Scan barcode..."
                            @keydown.enter.prevent="
                                handleBarcodeInput($event);
                                $event.target.value = '';
                            "
                            class="w-full pl-14 pr-4 py-2 rounded-lg border dark:border-gray-700 bg-white dark:bg-gray-800 
                                focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                            autofocus
                        >

                        <div class="absolute left-4 top-2.5 text-blue-600 dark:text-blue-400">
                            {{-- Heroicon barcode --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10M8 7v10M12 7v10M16 7v10M20 7v10" />
                            </svg>
                        </div>
                    </div>

                    {{-- Judul kategori aktif --}}
                    <h2 class="text-xl font-semibold mb-3">
                        <span x-text="selectedCategoryPhysical ? selectedCategoryPhysical.name : 'Semua Produk'"></span>
                    </h2>

                    {{-- Grid produk --}}
                    <div class="relative">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"
                            x-show="!transitioning"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0">

                            <template x-for="product in filteredProducts" :key="product.id">
                                <div @click="openProductOptions(product)"
                                    :class="{ 'opacity-60 pointer-events-none grayscale': product.stock <= 0 }"
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                                        rounded-xl shadow-sm hover:shadow-md transition-all duration-200 
                                        cursor-pointer flex flex-col p-5">

                                    {{-- Header (ikon + nama produk + stok) --}}
                                    <div class="flex items-start justify-between mb-4">
                                        {{-- Kiri: Icon + Nama + Kategori --}}
                                        <div class="flex items-start gap-3">
                                            {{-- Cube icon --}}
                                            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.6" stroke="currentColor"
                                                    class="w-6 h-6 text-gray-500 dark:text-gray-300">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16zM3.3 7.25l8.7 4.88 8.7-4.88M12 12v8" />
                                                </svg>
                                            </div>

                                            {{-- Nama & Kategori --}}
                                            <div class="flex flex-col">
                                                <h3 class="text-gray-800 dark:text-gray-100 font-semibold text-sm leading-tight line-clamp-1"
                                                    x-text="product.name"></h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                                    x-text="product.category_name"></p>
                                            </div>
                                        </div>

                                        {{-- Kanan: Stock Label (dengan satuan pcs) --}}
                                        <template x-if="product.stock > 10">
                                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md 
                                                        bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300"
                                                x-text="product.stock"></span>
                                        </template>
                                        <template x-if="product.stock <= 10 && product.stock > 0">
                                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md 
                                                        bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300"
                                                x-text="product.stock"></span>
                                        </template>
                                        <template x-if="product.stock <= 0">
                                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md 
                                                        bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                0 pcs
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Harga --}}
                                    <div class="mb-2">
                                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400"
                                            x-text="'Rp ' + Number(product.price).toLocaleString()"></p>
                                    </div>

                                    {{-- Barcode --}}
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 5v14M7 5v14m4-14v14m4-14v14m4-14v14" />
                                        </svg>
                                        <span x-text="product.code"></span>
                                    </div>

                                    {{-- Multiple Options Button --}}
                                    <div class="mt-auto flex items-start justify-start">
                                        <template x-if="(product.attribute_values?.length || 0) > 1">
                                            <button @click.stop="openProductOptions(product)"
                                                class="inline-block text-xs font-semibold text-blue-600 dark:text-blue-400 
                                                    bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-800/50
                                                    py-1 px-3 rounded-md transition text-left w-fit">
                                                Multiple Options
                                            </button>
                                        </template>

                                        {{-- Kalau tidak ada tombol, beri ruang agar konten bawah turun sedikit --}}
                                        <template x-if="(product.attribute_values?.length || 0) <= 1">
                                            <div class="h-5"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Jika kosong --}}
                        <template x-if="filteredProducts.length === 0">
                            <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                                Tidak ada produk untuk kategori ini.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- TOAST GLOBAL (paste setelah header) -->
                <div 
                    x-show="showToast"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="fixed right-6 z-[9999] pointer-events-auto"
                    style="top: calc(64px + 0.75rem);"
                >
                    <div class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-semibold">
                        <span x-text="toastMsg"></span>
                    </div>
                </div>

                {{-- Keranjang --}}
                <aside
                    class="md:w-1/3 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 flex flex-col justify-between">
                    {{-- Pilihan Pembayaran / Pelanggan --}}
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold mb-3">Pelanggan : </h2>

                        <select x-model="selectedCustomer"
                            class="w-full border dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">💵 Tunai</option>
                            <template x-for="cust in customers" :key="cust.id">
                                <option :value="cust.id" x-text="'👤 ' + cust.name"></option>
                            </template>
                        </select>

                        <template x-if="selectedCustomer">
                            <p class="mt-1 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Transaksi akan dicatat sebagai <strong>utang</strong>.
                            </p>
                        </template>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold mb-3">Keranjang Belanja</h2>
                        <template x-if="cart.length === 0">
                            <div class="text-center text-gray-500 py-10">
                                <i class="fa-solid fa-cart-shopping text-3xl mb-2"></i>
                                <p>Keranjang Kosong</p>
                            </div>
                        </template>

                        <template x-for="(item, index) in cart" :key="item.id + '-' + (item.variant_id ?? 'default')">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <div>
                                    <div x-text="item.name" class="font-medium"></div>
                                    <div class="text-sm text-gray-500"
                                        x-text="'Rp ' + (item.price * item.qty).toLocaleString()"></div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="decreaseQty(index)"
                                        class="px-2 bg-gray-200 dark:bg-gray-700 rounded">-</button>
                                    <span x-text="item.qty"></span>
                                    <button @click="increaseQty(index)"
                                        class="px-2 bg-gray-200 dark:bg-gray-700 rounded">+</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Kalkulator & Tombol Bayar --}}
                    <div class="mt-4 border-t pt-4">
                        <div class="flex justify-between mb-2">
                            <span>Subtotal:</span>
                            <span x-text="'Rp ' + total().toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between font-semibold mb-3">
                            <span>Total:</span>
                            <span class="text-blue-600" x-text="'Rp ' + total().toLocaleString()"></span>
                        </div>

                        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg text-center">
                            <h3 class="font-bold mb-1">Total Bayar</h3>
                            <div class="text-3xl font-bold text-blue-600 mb-3" x-text="'Rp ' + paid.toLocaleString()">
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                                <template x-for="n in [1000,2000,5000,10000,20000,50000,100000]">
                                    <button @click="addPayment(n)"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900 dark:hover:bg-blue-800 dark:text-blue-300 py-2 rounded text-sm font-semibold"
                                        x-text="'Rp ' + n.toLocaleString()"></button>
                                </template>
                                <button @click="payExact()"
                                    class="col-span-2 sm:col-span-3 bg-green-600 hover:bg-green-700 text-white py-2 rounded">UANG
                                    PAS</button>
                            </div>

                            <div class="grid grid-cols-3 gap-2 text-lg mb-3">
                                <template x-for="btn in ['1','2','3','4','5','6','7','8','9','00','0','⌫']">
                                    <button @click="handleKey(btn)"
                                        class="bg-gray-200 dark:bg-gray-600 py-3 rounded font-bold hover:bg-gray-300 dark:hover:bg-gray-500">
                                        <span x-text="btn"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="mt-4 flex justify-between font-semibold text-lg">
                                <span>Kembalian:</span>
                                <span x-text="'Rp ' + change().toLocaleString()"></span>
                            </div>

                            <div class="flex gap-3 mt-4">
                                <button @click="loadTodayTransactions()"
                                    class="flex-1 flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 
                                dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 
                                py-3 rounded-lg font-semibold text-sm transition active:scale-95 shadow-sm border border-gray-300 dark:border-gray-600">
                                    <i class="fa-solid fa-clock-rotate-left text-base"></i>
                                    <span>Riwayat</span>
                                </button>

                                <button @click="openReviewModal()"
                                    class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
                                text-white py-3 rounded-lg font-semibold text-sm transition active:scale-95 shadow-sm">
                                    <i class="fa-solid fa-cash-register text-base"></i>
                                    <span>Bayar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- ============================= --}}
            {{-- MODAL: PILIH VARIAN PRODUK --}}
            {{-- ============================= --}}
            <div 
                x-show="showOptionModal"
                x-transition
                @keydown.window.escape="showOptionModal = false"
                @click.self="showOptionModal = false"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            >
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 p-8 relative">
                    {{-- Tombol Close --}}
                    <button 
                        @click="showOptionModal = false" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                    >
                        <i class="fa-solid fa-xmark text-3xl"></i>
                    </button>

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="selectedProduct?.name"></h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedProduct?.code"></p>
                    </div>

                    <div class="mb-4 text-blue-700 bg-blue-50 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg p-3 text-sm">
                        Produk ini memiliki beberapa varian. Silakan pilih salah satu:
                    </div>

                    {{-- Grid varian lebih besar --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="opt in selectedProductOptions" :key="opt.id">
                            <button 
                                @click="opt.stok > 0 && chooseOption(opt)"
                                :disabled="opt.stok <= 0"
                                :class="opt.stok > 0 
                                    ? 'border-2 rounded-xl p-4 text-left hover:bg-blue-50 dark:hover:bg-blue-800 transition flex flex-col justify-between min-h-[110px]' 
                                    : 'border-2 rounded-xl p-4 text-left bg-gray-100 dark:bg-gray-700 opacity-60 cursor-not-allowed flex flex-col justify-between min-h-[110px]'">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg" 
                                    x-text="opt.attribute_value"></div>
                                <div class="text-sm" 
                                    :class="opt.stok > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'"
                                    x-text="opt.stok > 0 ? ('Stok: ' + opt.stok) : 'Stok Habis'"></div>
                                <div class="text-base font-bold text-blue-600"
                                    x-text="'Rp ' + Number(selectedProduct.price).toLocaleString()"></div>
                            </button>
                        </template>
                    </div>

                    <div class="text-xs text-gray-500 mt-6 border-t pt-4 text-center">
                        Klik varian untuk menambah ke keranjang.<br>
                        <span class="block text-gray-400 mt-1">Tekan <strong>ESC</strong> atau klik di luar area untuk menutup.</span>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- TAB: PRODUK DIGITAL (Final Enhanced Version) --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'digital'" x-transition
                class="flex flex-col gap-6 p-6 text-gray-800 dark:text-gray-100">

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold flex items-center gap-2 text-gray-800 dark:text-gray-100">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                            <path d="M3 7h18M3 12h18M3 17h10" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                        ⚡ POS Produk Digital
                    </h2>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Langkah <span x-text="step"></span> dari 6
                    </div>
                </div>

                {{-- Current selection summary --}}
                <div class="text-sm text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex flex-wrap gap-x-4 gap-y-2 items-center">
                        <template x-if="selectedDevice">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Device:</span>
                                <div class="flex items-center gap-2">
                                    <template x-if="selectedDevice.icon">
                                        <img :src="selectedDevice.icon" class="w-4 h-4 rounded" alt="">
                                    </template>
                                    <template x-if="!selectedDevice.icon">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24"
                                            fill="none">
                                            <rect x="6" y="3" width="12" height="18" rx="2"
                                                stroke="currentColor" stroke-width="1.2" />
                                        </svg>
                                    </template>
                                    <span class="font-medium text-gray-700 dark:text-gray-200"
                                        x-text="selectedDevice.name"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedApp">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">App:</span>
                                <div class="flex items-center gap-2">
                                    <template x-if="selectedApp.logo">
                                        <img :src="selectedApp.logo" class="w-4 h-4 rounded" alt="">
                                    </template>
                                    <template x-if="!selectedApp.logo">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24"
                                            fill="none">
                                            <rect x="4" y="4" width="16" height="16" rx="3"
                                                stroke="currentColor" stroke-width="1.2" />
                                        </svg>
                                    </template>
                                    <span class="font-medium text-gray-700 dark:text-gray-200"
                                        x-text="selectedApp.name"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedCategory">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Kategori:</span>
                                <span class="font-medium text-gray-700 dark:text-gray-200"
                                    x-text="selectedCategory.name"></span>
                            </div>
                        </template>

                        <template x-if="selectedBrand">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Brand:</span>
                                <div class="flex items-center gap-2">
                                    <template x-if="selectedBrand.logo">
                                        <img :src="selectedBrand.logo.startsWith('/storage') ? selectedBrand.logo : '/storage/' +
                                            selectedBrand.logo"
                                            class="w-4 h-4 rounded-full ring-1 ring-gray-300 dark:ring-gray-600"
                                            alt="">
                                    </template>
                                    <span class="font-medium text-gray-700 dark:text-gray-200"
                                        x-text="selectedBrand.name"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedProduct">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Produk:</span>
                                <span class="font-medium text-gray-700 dark:text-gray-200"
                                    x-text="selectedProduct.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Progress Wizard --}}
                <div class="flex items-center justify-between relative">
                    <div class="absolute top-5 left-0 w-full h-[2px] bg-gray-300 dark:bg-gray-700 z-0">
                        {{-- Progress Line with Smooth Animation --}}
                        <div class="absolute top-0 left-0 h-[2px] bg-blue-600 dark:bg-blue-400 transition-all duration-500 ease-in-out"
                            :style="`width: ${((step - 1) / 5) * 100}%`"></div>
                    </div>
                    <template
                            x-for="(item, index) in [
                                { icon: 'device', label: 'Device' },
                                { icon: 'app', label: 'Aplikasi' },
                                { icon: 'category', label: 'Kategori' },
                                { icon: 'brand', label: 'Brand' },
                                { icon: 'product', label: 'Produk' },
                                { icon: 'payment', label: 'Pembayaran' }
                        ]"
                        :key="index">
                        <div class="flex flex-col items-center w-full">
                            <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300 ease-in-out"
                                :class="{
                                    'bg-blue-600 border-blue-600 text-white shadow-md': step > index,
                                    'bg-white dark:bg-gray-800 border-gray-400 text-gray-500': step <= index
                                }">
                                {{-- Icons remain for clarity, but use monochrome/primary color --}}
                                <template x-if="item.icon === 'device'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <rect x="7" y="2" width="10" height="20" rx="2"
                                            stroke="currentColor" stroke-width="1.3" />
                                    </svg>
                                </template>
                                <template x-if="item.icon === 'app'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <rect x="3" y="3" width="18" height="18" rx="4"
                                            stroke="currentColor" stroke-width="1.3" />
                                    </svg>
                                </template>
                                <template x-if="item.icon === 'category'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 5h8v8H4zM14 5h6v8h-6zM4 15h8v4H4zM14 15h6v4h-6z" stroke="currentColor"
                                            stroke-width="1.1" />
                                    </svg>
                                </template>
                                <template x-if="item.icon === 'brand'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.3" />
                                        <path d="M8 12a4 4 0 018 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                        <path d="M12 8v8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                    </svg>
                                </template>
                                <template x-if="item.icon === 'product'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2l7 4v6l-7 4-7-4V6z" stroke="currentColor" stroke-width="1.2" />
                                    </svg>
                                </template>
                                <template x-if="item.icon === 'payment'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <rect x="2" y="6" width="20" height="12" rx="2"
                                            stroke="currentColor" stroke-width="1.1" />
                                        <path d="M2 10h20" stroke="currentColor" stroke-width="1.1" />
                                    </svg>
                                </template>
                            </div>
                            <span class="text-xs mt-2 text-center transition-colors duration-300"
                                :class="step > index ? 'text-blue-600 dark:text-blue-400 font-semibold' :
                                    'text-gray-500 dark:text-gray-400'">
                                <span x-text="item.label"></span>
                            </span>
                        </div>
                    </template>
                </div>

                {{-- Step Content Container with Directional Animation --}}
                <div class="relative overflow-hidden">
                    {{-- Use x-show and x-transition for directional slide --}}
                    <template x-for="s in [1, 2, 3, 4, 5, 6]" :key="s">
                        <div x-show="step === s"
                            :class="{
                                'absolute inset-0 w-full': step !== s,
                            }"
                            x-transition:enter="transition ease-out duration-500"
                            :x-transition:enter-start="s > $el.parentNode.__x_original_step ? 'opacity-0 translate-x-full' : (s < $el.parentNode.__x_original_step ? 'opacity-0 -translate-x-full' : 'opacity-0')"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-500 absolute inset-0 w-full"
                            :x-transition:leave-end="s < $el.parentNode.__x_original_step ? 'opacity-0 translate-x-full' : (s > $el.parentNode.__x_original_step ? 'opacity-0 -translate-x-full' : 'opacity-0')"
                            x-init="$el.parentNode.__x_original_step = step">
                            {{-- Store original step for comparison, ensuring the initial state is set --}}
                            <div x-show="step === s">
                                <div x-init="$el.parentNode.parentNode.__x_original_step = step">

                                    {{-- Step 1: Pilih Device --}}
                                    <template x-if="s === 1">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <h3
                                                    class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                                    1️⃣ Pilih Device
                                                </h3>

                                                {{-- Tombol Riwayat Transaksi Digital --}}
                                                <button @click="showHistoryDigital = true"
                                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg 
                                                    bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                                                    hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm font-semibold shadow-sm active:scale-[0.98]">
                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-9-9 9 9 0 019 9z" />
                                                    </svg>
                                                    <span>Riwayat</span>
                                                </button>
                                            </div>

                                            {{-- ✅ Jika ada device --}}
                                            <template x-if="devices.length > 0">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                    <template x-for="dev in devices" :key="dev.id">
                                                        <div @click="selectedDevice = dev; $el.parentNode.parentNode.__x_original_step = 1; step = 2; selectedApp = null; selectedCategory = null; selectedProduct = null;"
                                                            class="border dark:border-gray-700 rounded-xl p-4 cursor-pointer bg-white dark:bg-gray-800 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                                                            :class="selectedDevice?.id === dev.id ?
                                                                'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                                'border-gray-300 dark:border-gray-700'">
                                                            <div class="flex flex-col items-center text-center">
                                                                <div
                                                                    class="w-12 h-12 rounded-full flex items-center justify-center mb-2 bg-gray-100 dark:bg-gray-700">
                                                                    <template x-if="dev.icon && window.heroicons[dev.icon]">
                                                                        <div
                                                                            class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                                                            <div x-html="window.heroicons[dev.icon]"
                                                                                class="w-6 h-6 text-blue-600 dark:text-blue-400 [&>svg]:w-6 [&>svg]:h-6 [&>svg]:stroke-current">
                                                                            </div>
                                                                        </div>
                                                                    </template>

                                                                    <template x-if="!dev.icon || !window.heroicons[dev.icon]">
                                                                        <svg class="w-6 h-6 text-gray-500" viewBox="0 0 24 24" fill="none">
                                                                            <rect x="7" y="2" width="10" height="20"
                                                                                rx="2" stroke="currentColor" stroke-width="1.2" />
                                                                        </svg>
                                                                    </template>
                                                                </div>
                                                                <h4 class="font-semibold text-gray-800 dark:text-gray-100" x-text="dev.name"></h4>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="dev.notes"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            {{-- 🚫 Jika tidak ada device --}}
                                            <template x-if="devices.length === 0">
                                                <div class="flex flex-col items-center justify-center py-8 text-center text-gray-500 dark:text-gray-400">
                                                    <svg class="w-10 h-10 mb-3 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="text-sm font-medium">Belum ada device</p>
                                                    <p class="text-xs text-gray-400">Silakan tambahkan device di halaman admin</p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Step 2: Pilih Aplikasi --}}
                                    <template x-if="s === 2">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 2; step = 1"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">←
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">2️⃣
                                                    Pilih
                                                    Aplikasi</h3>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="app in (selectedDevice?.apps || [])"
                                                    :key="app.id">
                                                    <div @click="selectedApp = app; $el.parentNode.parentNode.__x_original_step = 2; step = 3; selectedCategory = null; selectedProduct = null;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedApp?.id === app.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">
                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                                            <template x-if="app.logo">
                                                                <img :src="`/storage/${app.logo}`"
                                                                    class="w-12 h-12 object-cover rounded-full ring-2 ring-gray-300 dark:ring-gray-600 shadow-sm bg-white"
                                                                    alt="Logo Aplikasi">
                                                            </template>
                                                            <template x-if="!app.logo">
                                                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                                                    viewBox="0 0 24 24">
                                                                    <rect x="3" y="3" width="18" height="18"
                                                                        rx="3" stroke="currentColor"
                                                                        stroke-width="1.2" />
                                                                </svg>
                                                            </template>
                                                        </div>
                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100"
                                                            x-text="app.name"></h4>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                                            x-text="app.description"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 3: Pilih Kategori --}}
                                    <template x-if="s === 3">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 3; step = 2"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">←
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">3️⃣
                                                    Pilih
                                                    Kategori Digital</h3>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="cat in categoriesForSelectedApp" :key="cat.id">
                                                    <div @click="selectedCategory = cat; $el.parentNode.parentNode.__x_original_step = 3; step = 4; selectedProduct = null;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedCategory?.id === cat.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">
                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700">
                                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <path d="M4 5h8v8H4zM14 5h6v8h-6zM4 15h8v4H4zM14 15h6v4h-6z"
                                                                    stroke="currentColor" stroke-width="1.2" />
                                                            </svg>
                                                        </div>
                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100"
                                                            x-text="cat.name"></h4>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 4: Pilih Brand Digital --}}
                                    <template x-if="s === 4">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 4; step = 3"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">←
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">4️⃣
                                                    Pilih
                                                    Brand Digital</h3>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="brand in filteredBrandsForSelectedAppAndCategory"
                                                    :key="brand.id">
                                                    <div @click="selectedBrand = brand; $el.parentNode.parentNode.__x_original_step = 4; step = 5;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedBrand?.id === brand.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">
                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                                            <template x-if="brand.icon">
                                                                <img :src="brand.icon.startsWith('/storage') ? brand.icon :
                                                                    '/storage/' + brand.icon"
                                                                    class="w-10 h-10 object-cover rounded-full ring-2 ring-gray-300 dark:ring-gray-600 bg-white shadow-sm"
                                                                    alt="Brand Icon">
                                                            </template>
                                                            <template x-if="brand.logo">
                                                                <img :src="brand.logo.startsWith('/storage') ? brand.logo :
                                                                    '/storage/' + brand.logo"
                                                                    class="w-10 h-10 object-cover rounded-full ring-2 ring-gray-300 dark:ring-gray-600 bg-white shadow-sm"
                                                                    alt="Brand Logo">
                                                            </template>
                                                        </div>
                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100 mb-1"
                                                            x-text="brand.name"></h4>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                                            x-text="brand.description || '-'"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 4: Pilih Produk --}}
                                    <template x-if="s === 5">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 4; step = 3"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">
                                                    ← Kembali
                                                </button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                                    5️⃣ Pilih Produk Digital
                                                </h3>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="prod in digitalProductsForSelectedCategoryAndApp"
                                                    :key="prod.id">
                                                    <div @click="selectedProduct = prod; payment.total = parseInt(prod.base_price) || 0; $el.parentNode.parentNode.__x_original_step = 5; step = 6;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedProduct?.id === prod.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">

                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700">
                                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <path d="M12 2l7 4v6l-7 4-7-4V6z" stroke="currentColor"
                                                                    stroke-width="1.2" />
                                                            </svg>
                                                        </div>

                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100 mb-1"
                                                            x-text="prod.name"></h4>

                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Rp <span
                                                                x-text="new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(prod.base_price)">
                                                            </span>
                                                        </p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 5: Pembayaran --}}
                                    <template x-if="s === 6">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 5; step = 4"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">←
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                                    Pembayaran</h3>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                {{-- LEFT SIDE (Monokrom Modern) --}}
                                                <div
                                                    class="bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg p-5 shadow-xl">
                                                    <h4
                                                        class="font-semibold mb-4 flex items-center gap-2 text-lg text-gray-700 dark:text-gray-100">
                                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                            viewBox="0 0 24 24" fill="none">
                                                            <path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round" />
                                                        </svg>
                                                        Rincian Transaksi
                                                    </h4>

                                                    {{-- 🧾 Pelanggan --}}
                                                    <div class="mb-4">
                                                        <h2
                                                            class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2 flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1.5"
                                                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                                                            </svg>
                                                            Pelanggan
                                                        </h2>

                                                        <select x-model="selectedCustomer"
                                                            class="w-full border dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                                                            <option value="">💵 Tunai</option>
                                                            <template x-for="cust in customers" :key="cust.id">
                                                                <option :value="cust.id" x-text="'👤 ' + cust.name">
                                                                </option>
                                                            </template>
                                                        </select>

                                                        <template x-if="selectedCustomer">
                                                            <p
                                                                class="mt-2 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    stroke-width="2" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Transaksi akan dicatat sebagai <strong>utang</strong>.
                                                            </p>
                                                        </template>
                                                    </div>

                                                    {{-- 💻 Detail Transaksi --}}
                                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                                        {{-- Device --}}
                                                        <div
                                                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700">
                                                            <div
                                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full">
                                                                <template x-if="selectedDevice?.icon">
                                                                    <div class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                                                        x-html="window.heroicons[selectedDevice.icon] || window.heroicons['device-phone-mobile']">
                                                                    </div>
                                                                </template>
                                                                <template x-if="!selectedDevice?.icon">
                                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                                        viewBox="0 0 24 24" fill="none">
                                                                        <rect x="7" y="2" width="10" height="20"
                                                                            rx="2" stroke="currentColor"
                                                                            stroke-width="1.3" />
                                                                    </svg>
                                                                </template>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">Device
                                                                </p>
                                                                <p class="font-semibold text-gray-800 dark:text-gray-200"
                                                                    x-text="selectedDevice?.name || '-'"></p>
                                                            </div>
                                                        </div>

                                                        {{-- Aplikasi --}}
                                                        <div
                                                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700">
                                                            <div
                                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                                <template x-if="selectedApp?.logo">
                                                                    <img :src="selectedApp.logo.startsWith('/storage') ?
                                                                        selectedApp
                                                                        .logo : '/storage/' + selectedApp.logo"
                                                                        class="w-8 h-8 object-cover rounded-full ring-2 ring-gray-300 dark:ring-gray-600 bg-white"
                                                                        alt="Logo Aplikasi">
                                                                </template>
                                                                <template x-if="!selectedApp?.logo">
                                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                                        viewBox="0 0 24 24" fill="none">
                                                                        <rect x="4" y="4" width="16" height="16"
                                                                            rx="3" stroke="currentColor"
                                                                            stroke-width="1.2" />
                                                                    </svg>
                                                                </template>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Aplikasi
                                                                </p>
                                                                <p class="font-semibold text-gray-800 dark:text-gray-200"
                                                                    x-text="selectedApp?.name || '-'"></p>
                                                            </div>
                                                        </div>

                                                        {{-- Kategori --}}
                                                        <div
                                                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700">
                                                            <div
                                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full">
                                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                                    viewBox="0 0 24 24" fill="none">
                                                                    <path
                                                                        d="M4 5h8v8H4zM14 5h6v8h-6zM4 15h8v4H4zM14 15h6v4h-6z"
                                                                        stroke="currentColor" stroke-width="1.2" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Kategori
                                                                </p>
                                                                <p class="font-semibold text-gray-800 dark:text-gray-200"
                                                                    x-text="selectedCategory?.name || '-'"></p>
                                                            </div>
                                                        </div>

                                                        {{-- Brand --}}
                                                        <div
                                                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700">
                                                            <div
                                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                                <template x-if="selectedBrand?.logo">
                                                                    <img :src="selectedBrand.logo.startsWith('/storage') ?
                                                                        selectedBrand.logo : '/storage/' + selectedBrand
                                                                        .logo"
                                                                        class="w-8 h-8 object-cover rounded-full ring-2 ring-gray-300 dark:ring-gray-600 bg-white"
                                                                        alt="Logo Brand">
                                                                </template>
                                                                <template x-if="!selectedBrand?.logo">
                                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                                        viewBox="0 0 24 24" fill="none">
                                                                        <circle cx="12" cy="12" r="10"
                                                                            stroke="currentColor" stroke-width="1.2" />
                                                                    </svg>
                                                                </template>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">Brand
                                                                </p>
                                                                <p class="font-semibold text-gray-800 dark:text-gray-200"
                                                                    x-text="selectedBrand?.name || '-'"></p>
                                                            </div>
                                                        </div>

                                                        {{-- Produk --}}
                                                        <div
                                                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700">
                                                            <div
                                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full">
                                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                                    viewBox="0 0 24 24" fill="none">
                                                                    <path d="M12 2l7 4v6l-7 4-7-4V6z" stroke="currentColor"
                                                                        stroke-width="1.2" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">Produk
                                                                </p>
                                                                <p class="font-semibold text-gray-800 dark:text-gray-200"
                                                                    x-text="selectedProduct?.name || '-'"></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- 💰 Total --}}
                                                    <div class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4">
                                                        <p
                                                            class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                                            Total Pembayaran</p>
                                                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                                                            Rp <span x-text="payment.total.toLocaleString()"></span>
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- RIGHT SIDE: Kalkulator --}}
                                                <div
                                                    class="bg-gray-100 dark:bg-gray-800 p-5 rounded-lg shadow-lg text-center flex flex-col justify-between h-full border border-gray-300 dark:border-gray-700">
                                                    <div>
                                                        <h3
                                                            class="font-bold mb-1 text-lg text-gray-700 dark:text-gray-200">
                                                            Total Bayar</h3>
                                                        <div class="mb-5">
                                                            <template x-if="!editingTotal">
                                                                <div @click="editingTotal = true"
                                                                    class="text-4xl font-bold text-blue-600 dark:text-blue-400 cursor-pointer select-none hover:opacity-80 transition"
                                                                    x-text="'Rp ' + payment.total.toLocaleString()"
                                                                    title="Klik untuk ubah total secara manual">
                                                                </div>
                                                            </template>

                                                            <template x-if="editingTotal">
                                                                <input type="number" x-model.number="payment.total"
                                                                    @blur="editingTotal = false"
                                                                    class="w-full text-center text-3xl font-bold border border-blue-400 bg-white dark:bg-gray-700 
                                                                    text-blue-600 dark:text-blue-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500 outline-none transition"
                                                                    placeholder="Masukkan total bayar">
                                                            </template>
                                                        </div>
                                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                                                            <template
                                                                x-for="n in [1000,2000,5000,10000,20000,50000,100000]">
                                                                <button @click="payment.paid += n"
                                                                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 py-2 rounded text-sm font-semibold transition">Rp
                                                                    <span x-text="n.toLocaleString()"></span></button>
                                                            </template>
                                                            <button @click="payment.paid = payment.total"
                                                                class="col-span-2 sm:col-span-3 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-700 dark:hover:bg-blue-800 py-2 rounded font-semibold transition">UANG
                                                                PAS</button>
                                                        </div>
                                                        <div class="grid grid-cols-3 gap-2 text-lg mb-3">
                                                            <template
                                                                x-for="btn in ['1','2','3','4','5','6','7','8','9','00','0','⌫']">
                                                                <button
                                                                    @click="btn==='⌫'?payment.paid=Math.floor(payment.paid/10):payment.paid=Number(String(payment.paid)+btn)"
                                                                    class="bg-white dark:bg-gray-600 py-3 rounded font-bold hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-100 transition"><span
                                                                        x-text="btn"></span></button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="mt-4 text-left font-semibold space-y-1 text-lg border-t border-gray-300 dark:border-gray-700 pt-4">
                                                        <div class="flex justify-between text-gray-700 dark:text-gray-200">
                                                            <span>Dibayar:</span>
                                                            <span x-text="'Rp ' + payment.paid.toLocaleString()"></span>
                                                        </div>
                                                        <div class="flex justify-between"
                                                            :class="(payment.paid - payment.total) >= 0 ?
                                                                'text-green-600 dark:text-green-400' :
                                                                'text-red-600 dark:text-red-400'">
                                                            <span>Kembalian:</span>
                                                            <span
                                                                x-text="'Rp ' + (payment.paid - payment.total).toLocaleString()"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-6">
                                                        <button @click="showDigitalReviewModal = true"
                                                            :disabled="payment.paid < payment.total"
                                                            class="w-full py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition active:scale-[0.98] shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed dark:disabled:bg-gray-600">
                                                            <svg class="w-5 h-5 inline mr-1" viewBox="0 0 24 24"
                                                                fill="none">
                                                                <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor"
                                                                    stroke-width="1.5" stroke-linecap="round" />
                                                            </svg>
                                                            BAYAR
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- 🧾 Modal Konfirmasi Transaksi --}}
            <div x-show="showReview"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50"
                x-transition>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 w-[95%] max-w-2xl shadow-2xl relative overflow-hidden">

                    {{-- Judul --}}
                    <div class="border-b border-gray-300 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar text-green-500"></i>
                            Konfirmasi Transaksi
                        </h2>
                    </div>

                    {{-- Daftar Produk --}}
                    <div class="text-sm md:text-base space-y-3 max-h-[50vh] overflow-y-auto pr-2 pb-2">
                        <template x-for="item in cart" :key="item.id + '-' + (item.variant_id ?? 'default')">
                            <div>
                                <div class="flex justify-between items-center">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100" x-text="item.name"></div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-100"
                                        x-text="'Rp ' + (item.price * item.qty).toLocaleString()"></div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                    x-text="item.qty + ' × Rp ' + item.price.toLocaleString()"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Total & Info Pembayaran --}}
                    <div class="mt-5 pt-4 border-t border-gray-300 dark:border-gray-700 space-y-3 text-base">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <span>💰</span> Total:
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white"
                                x-text="'Rp ' + total().toLocaleString()"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <span>💵</span> Dibayar:
                            </span>
                            <span class="text-gray-900 dark:text-white"
                                x-text="'Rp ' + paid.toLocaleString()"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <span>🔄</span> Kembalian:
                            </span>
                            <span class="text-blue-600 dark:text-blue-400 font-semibold"
                                x-text="'Rp ' + change().toLocaleString()"></span>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-3 mt-6 pt-4">
                        <button @click="showReview = false"
                            class="px-5 py-2.5 rounded-lg bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 font-medium hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                            <i class="fa-solid fa-times mr-1"></i> Batalkan
                        </button>
                        <button @click="confirmCheckout()"
                            class="px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold shadow">
                            <i class="fa-solid fa-check mr-1"></i> Konfirmasi
                        </button>
                    </div>

                </div>
            </div>

            {{-- Modal Success --}}
            <div x-show="showSuccess" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                x-transition>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-8 w-[90%] md:w-[480px] text-center shadow-2xl transform transition-all scale-100">
                    <div class="flex justify-center mb-3">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 111.414-1.414L8.5 11.086l6.543-6.543a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Transaksi Berhasil!</h2>

                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300 text-left">
                        <div class="flex justify-between font-semibold text-base">
                            <span>Total</span>
                            <span x-text="'Rp ' + lastTransaction.total.toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Dibayar</span>
                            <span x-text="'Rp ' + lastTransaction.dibayar.toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between font-bold text-green-600 dark:text-green-400 text-base">
                            <span>Kembalian</span>
                            <span x-text="'Rp ' + lastTransaction.kembalian.toLocaleString()"></span>
                        </div>
                    </div>

                    <button @click="showSuccess=false"
                        class="mt-6 w-full py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
                        OK
                    </button>
                </div>
            </div>

            {{-- Modal Riwayat Transaksi --}}
            <div x-show="showHistory" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
                x-transition>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-[95%] md:w-[800px] 
                max-h-[90vh] overflow-y-auto shadow-2xl scrollbar-thin 
                scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 
                scrollbar-track-transparent">

                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            🧾 Riwayat Transaksi Hari Ini
                        </h2>
                        <button @click="showHistory=false"
                            class="text-gray-400 hover:text-gray-200 text-2xl font-bold">&times;</button>
                    </div>

                    {{-- Ringkasan Penjualan (modern minimalist) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                        {{-- Total Penjualan --}}
                        <div
                            class="flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 
                            dark:from-blue-900/40 dark:to-blue-800/20 text-blue-700 dark:text-blue-300 
                            rounded-2xl p-4 shadow-sm border border-blue-100 dark:border-blue-700/50 hover:shadow-md transition-all">
                            <div class="flex items-center gap-2 text-sm opacity-80">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                <span>Total Penjualan</span>
                            </div>
                            <div class="text-2xl font-bold mt-1"
                                x-text="'Rp ' + summary.total_penjualan.toLocaleString()">
                            </div>
                        </div>

                        {{-- Jumlah Transaksi --}}
                        <div
                            class="flex flex-col items-center justify-center bg-gradient-to-br from-green-50 to-green-100 
                            dark:from-green-900/40 dark:to-green-800/20 text-green-700 dark:text-green-300 
                            rounded-2xl p-4 shadow-sm border border-green-100 dark:border-green-700/50 hover:shadow-md transition-all">
                            <div class="flex items-center gap-2 text-sm opacity-80">
                                <i class="fa-solid fa-receipt"></i>
                                <span>Jumlah Transaksi</span>
                            </div>
                            <div class="text-2xl font-bold mt-1" x-text="summary.jumlah_transaksi"></div>
                        </div>

                        {{-- Produk Terjual --}}
                        <div
                            class="flex flex-col items-center justify-center bg-gradient-to-br from-cyan-50 to-blue-100 
                            dark:from-cyan-900/40 dark:to-blue-800/20 text-cyan-700 dark:text-cyan-300 
                            rounded-2xl p-4 shadow-sm border border-cyan-100 dark:border-cyan-700/50 hover:shadow-md transition-all">
                            <div class="flex items-center gap-2 text-sm opacity-80">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span>Produk Terjual</span>
                            </div>
                            <div class="text-2xl font-bold mt-1" x-text="summary.total_produk_terjual"></div>
                        </div>
                    </div>

                    {{-- Ringkasan Kategori yang Terjual --}}
                    <template x-if="summary.categories && summary.categories.length > 0">
                        <div class="mb-6">
                            <h3 class="text-gray-700 dark:text-gray-300 font-semibold mb-3">
                                Kategori Terjual:
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                <template x-for="cat in summary.categories" :key="cat.name">
                                    <div
                                        class="flex flex-col items-center justify-center px-4 py-3 
                                    bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                                    rounded-xl shadow-sm text-center min-w-[110px] transform transition-all duration-300 
                                    hover:scale-105 hover:shadow-md hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer">

                                        {{-- Nama kategori uppercase --}}
                                        <span
                                            class="text-gray-800 dark:text-gray-100 font-bold text-xs tracking-wide uppercase"
                                            x-text="cat.name">
                                        </span>

                                        {{-- Jumlah pcs --}}
                                        <span class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                            (<span x-text="cat.pcs"></span> pcs)
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Daftar Transaksi --}}
                    <template x-if="transactionsToday.length === 0">
                        <p class="text-gray-500 text-center py-8">Belum ada transaksi hari ini.</p>
                    </template>

                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="trx in transactionsToday" :key="trx.id">
                            <div
                                :class="[
                                    'p-3 rounded-lg cursor-pointer transition border',
                                    trx.customer_id ?
                                    'bg-red-50/80 dark:bg-red-900/30 border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-800/40' :
                                    'hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700'
                                ]">

                                <div
                                    class="flex justify-between items-center font-semibold text-gray-800 dark:text-gray-100 mb-1">
                                    <div class="flex flex-col">
                                        <span class="flex items-center gap-2">
                                            <span x-text="trx.nomor_nota"></span>

                                            <template x-if="trx.customer_id">
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 dark:text-red-400">
                                                    <i class="fa-solid fa-clock"></i> Belum Lunas
                                                </span>
                                            </template>

                                            <template x-if="!trx.customer_id">
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 dark:text-green-400">
                                                    <i class="fa-solid fa-circle-check"></i> Lunas
                                                </span>
                                            </template>
                                        </span>

                                        <template x-if="trx.customer_id && trx.customer">
                                            <span class="text-xs text-red-700 dark:text-red-300 font-medium mt-0.5">
                                                <i class="fa-solid fa-user mr-1"></i>
                                                <span x-text="trx.customer.name"></span>
                                            </span>
                                        </template>
                                    </div>

                                    <div class="relative flex items-center justify-between">
                                        <!-- Jam -->
                                        <span x-text="trx.created_at + ' WIB'"
                                            class="text-sm text-gray-500 dark:text-gray-400"></span>

                                        <!-- ⋮ Tombol Dropdown -->
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open"
                                                class="ml-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 5h.01M12 12h.01M12 19h.01" />
                                                </svg>
                                            </button>

                                            <!-- Dropdown -->
                                            <div x-show="open" @click.away="open = false"
                                                class="absolute right-0 mt-2 w-40 backdrop-blur-md bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50"
                                                x-transition>
                                                <button @click="confirmDelete(trx); open=false"
                                                    class="w-full flex items-center gap-2 px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/40 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h10" />
                                                    </svg>
                                                    <span>Hapus Transaksi</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Detail Produk: nama + qty pcs --}}
                                <template x-for="item in trx.details.slice(0, 3)" :key="item.product">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                                        <span>
                                            <span x-text="item.product"></span> ×
                                            <span x-text="item.qty"></span> pcs
                                        </span>
                                        <span x-text="'Rp ' + item.subtotal.toLocaleString()"></span>
                                    </div>
                                </template>

                                {{-- Jika lebih dari 3 produk, tampilkan indikator tambahan --}}
                                <template x-if="trx.details.length > 3">
                                    <div class="text-xs text-gray-400 italic mt-1">
                                        + <span x-text="trx.details.length - 3"></span> produk lainnya...
                                    </div>
                                </template>

                                {{-- Total transaksi --}}
                                <div class="text-right font-semibold text-blue-600 dark:text-blue-400 mt-2">
                                    <span x-text="'Rp ' + trx.subtotal.toLocaleString()"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus -->
            <div x-show="showDeleteConfirm" x-transition.opacity.duration.300ms
                class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm">
                <div x-show="showDeleteConfirm" x-transition.scale.duration.300ms
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-[90%] max-w-sm text-center">

                    <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">
                        Hapus Transaksi?
                    </h3>

                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">
                        Nomor nota:
                        <span class="font-semibold text-red-600 dark:text-red-400"
                            x-text="transactionToDelete?.nomor_nota || '-'"></span>
                    </p>

                    <div class="flex justify-center gap-3">
                        <button @click="showDeleteConfirm = false"
                            class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100
                           hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Batal
                        </button>

                        <button @click="deleteTransaction(transactionToDelete)"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 active:scale-[0.97]
                           transition shadow-md">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal Detail Transaksi --}}
            <div x-show="showDetailModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                x-transition>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-[90%] md:w-[600px] max-h-[85vh] overflow-y-auto shadow-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100"
                            x-text="'Detail ' + (selectedTransaction?.nomor_nota ?? '')"></h2>
                        <button @click="showDetailModal=false"
                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-xl font-bold">&times;</button>
                    </div>

                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="item in selectedTransaction?.details ?? []" :key="item.product">
                            <div class="py-2 flex justify-between text-gray-700 dark:text-gray-200 text-sm">
                                <span>
                                    <span class="font-medium" x-text="item.product"></span>
                                    <span class="text-xs text-gray-500 ml-1">(x<span x-text="item.qty"></span>)</span>
                                </span>
                                <span x-text="'Rp ' + item.subtotal.toLocaleString()"></span>
                            </div>
                        </template>
                    </div>

                    <hr class="my-4">

                    <div class="text-right space-y-1 text-gray-700 dark:text-gray-300">
                        <div>Total: <span class="font-semibold"
                                x-text="'Rp ' + selectedTransaction?.subtotal.toLocaleString()"></span></div>
                        <div>Dibayar: <span class="font-semibold"
                                x-text="'Rp ' + selectedTransaction?.dibayar.toLocaleString()"></span></div>
                        <div>Kembalian: <span class="font-semibold text-green-600 dark:text-green-400"
                                x-text="'Rp ' + selectedTransaction?.kembalian.toLocaleString()"></span></div>
                    </div>
                </div>
            </div>

            <!-- 🌟 MODAL REVIEW TRANSAKSI DIGITAL -->
            <div x-show="showDigitalReviewModal"
                x-transition
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">

                <div @click.away="showDigitalReviewModal = false"
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 w-[95%] max-w-2xl shadow-2xl relative overflow-hidden border border-gray-300 dark:border-gray-700">

                    <!-- Judul -->
                    <div class="border-b border-gray-300 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-mobile-screen-button text-blue-500"></i>
                            Konfirmasi Pembayaran Digital
                        </h2>
                    </div>

                    <!-- Informasi Produk Digital -->
                    <div class="text-sm md:text-base space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-laptop text-gray-400"></i> Device:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedDevice?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-circle-nodes text-gray-400"></i> Aplikasi:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedApp?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-gray-400"></i> Kategori:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedCategory?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-tags text-gray-400"></i> Brand:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedBrand?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center pb-3">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-box text-gray-400"></i> Produk:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedProduct?.name ?? '-'"></span>
                        </div>
                    </div>

                    <!-- Rangkuman Pembayaran -->
                    <div class="mt-5 pt-4 border-t border-gray-300 dark:border-gray-700 space-y-3 text-base">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <span>💰</span> Total:
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white"
                                x-text="'Rp ' + payment.total.toLocaleString()"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <span>💵</span> Dibayar:
                            </span>
                            <span class="text-gray-900 dark:text-white"
                                x-text="'Rp ' + payment.paid.toLocaleString()"></span>
                        </div>

                        <div class="flex justify-between items-center"
                            :class="(payment.paid - payment.total) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                            <span class="flex items-center gap-2">
                                <span>🔄</span> Kembalian:
                            </span>
                            <span x-text="'Rp ' + (payment.paid - payment.total).toLocaleString()"></span>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-3 mt-6 pt-4">
                        <button @click="showDigitalReviewModal = false"
                            class="px-5 py-2.5 rounded-lg bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 font-medium hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                            <i class="fa-solid fa-times mr-1"></i> Batalkan
                        </button>
                        <button @click="showDigitalReviewModal = false; confirmDigitalTransaction()"
                            class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow">
                            <i class="fa-solid fa-check mr-1"></i> Konfirmasi Pembayaran
                        </button>
                    </div>

                </div>
            </div>

            {{-- Modal Riwayat Transaksi Digital --}}
            <div x-show="showHistoryDigital" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
                x-transition>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-[95%] md:w-[850px] 
        max-h-[90vh] overflow-y-auto shadow-2xl transition-all duration-300 ease-out">

                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="text-2xl font-bold flex items-center gap-2 text-gray-800 dark:text-gray-100">
                            ⚡ Riwayat Transaksi Produk Digital
                        </h2>
                        <button @click="showHistoryDigital=false"
                            class="text-gray-400 hover:text-gray-200 text-2xl font-bold">&times;</button>
                    </div>

                    {{-- Step 1: Pilih Aplikasi --}}
                    <template x-if="!selectedAppFilter">
                        <div x-transition>
                            <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">Pilih Aplikasi</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <template x-for="app in apps" :key="app.id">
                                    <div @click="selectedAppFilter = app.id; loadDigitalTransactions();"
                                        class="flex flex-col items-center justify-center p-4 border dark:border-gray-700 rounded-xl cursor-pointer 
                        bg-gray-50 dark:bg-gray-900 hover:bg-blue-50 dark:hover:bg-blue-900/30 
                        transition-all duration-200 shadow-sm hover:shadow-md">

                                        {{-- Logo --}}
                                        <div
                                            class="w-16 h-16 rounded-full flex items-center justify-center bg-white dark:bg-gray-800 overflow-hidden mb-2 border border-gray-300 dark:border-gray-700">
                                            <template x-if="app.logo">
                                                <img :src="`/storage/${app.logo}`" class="w-14 h-14 object-contain"
                                                    alt="App Logo">
                                            </template>
                                            <template x-if="!app.logo">
                                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24"
                                                    fill="none">
                                                    <rect x="3" y="3" width="18" height="18" rx="3"
                                                        stroke="currentColor" stroke-width="1.5" />
                                                </svg>
                                            </template>
                                        </div>

                                        {{-- Nama App --}}
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 text-center"
                                            x-text="app.name"></p>

                                        {{-- Total & Jumlah Transaksi --}}
                                        <template x-if="digitalAppSummary && digitalAppSummary[app.id]">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">
                                                (
                                                <span class="font-medium text-blue-600 dark:text-blue-400">
                                                    Rp <span
                                                        x-text="digitalAppSummary[app.id].total.toLocaleString()"></span>
                                                </span>
                                                •
                                                <span x-text="digitalAppSummary[app.id].count"></span> trx
                                                )
                                            </p>
                                        </template>
                                        <template x-if="!digitalAppSummary || !digitalAppSummary[app.id]">
                                            <p class="text-xs text-gray-400 italic">(Belum ada transaksi)</p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Step 2: Riwayat Transaksi Aplikasi Terpilih --}}
                    <template x-if="selectedAppFilter">
                        <div x-transition>
                            {{-- Header + Tombol Kembali --}}
                            <div class="flex items-center justify-between mb-4">
                                <button @click="selectedAppFilter=''; digitalTransactions=[]; digitalSummary=null"
                                    class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Kembali
                                </button>
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24"
                                        fill="none">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 6h16M4 12h16M4 18h10" />
                                    </svg>
                                    Riwayat Transaksi
                                </h3>
                            </div>

                            {{-- Ringkasan Total Hari Ini --}}
                            <template x-if="digitalAppSummary">
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-5 text-center shadow-sm">

                                    <p class="text-sm text-blue-600 dark:text-blue-300 font-medium mb-1">
                                        Total <span class="font-semibold"
                                            x-text="apps.find(a => a.id == selectedAppFilter)?.name || 'Aplikasi'"></span>
                                        Hari
                                        Ini
                                    </p>

                                    <h2 class="text-3xl font-bold text-blue-700 dark:text-blue-400">
                                        Rp <span x-text="digitalSummary.toLocaleString()"></span>
                                    </h2>
                                </div>
                            </template>

                            {{-- Loading --}}
                            <template x-if="loadingDigitalTransactions">
                                <p class="text-center py-8 text-gray-500 dark:text-gray-400 animate-pulse">Memuat data...
                                </p>
                            </template>

                            {{-- Tidak Ada Data --}}
                            <template x-if="!loadingDigitalTransactions && digitalTransactions.length === 0">
                                <p class="text-gray-500 text-center py-8">Belum ada transaksi hari ini untuk aplikasi ini.
                                </p>
                            </template>

                            {{-- Daftar Transaksi --}}
                            <div class="space-y-3" x-show="digitalTransactions.length > 0">
                                <template x-for="trx in digitalTransactions" :key="trx.id">
                                    <div
                                        class="p-4 border dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">

                                        {{-- Header --}}
                                        <div class="flex justify-between items-center mb-1">
                                            <div>
                                                <h3 class="font-semibold text-gray-800 dark:text-gray-100"
                                                    x-text="trx.nomor_nota"></h3>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                                    x-text="trx.created_at + ' WIB'"></span>

                                                {{-- Menu Tiga Titik --}}
                                                <div x-data="{ open: false }" class="relative">
                                                    <button @click="open = !open"
                                                        class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="w-5 h-5 text-gray-500 dark:text-gray-300"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6h.01M12 12h.01M12 18h.01" />
                                                        </svg>
                                                    </button>

                                                    <div x-show="open" @click.away="open = false" x-transition
                                                        class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg sh76adow-lg overflow-hidden z-50">
                                                        <button @click="confirmDeleteDigital(trx); open=false"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 dark:text-red-400
                               hover:bg-red-50 dark:hover:bg-red-900/40 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Daftar Detail Produk --}}
                                        <template x-if="trx.details && trx.details.length > 0">
                                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1 space-y-0.5">
                                                <template x-for="item in trx.details.slice(0, 3)" :key="item.product">
                                                    <div class="flex justify-between">
                                                        <span>
                                                            <span x-text="item.product"></span>
                                                            × <span x-text="item.qty"></span> pcs
                                                        </span>
                                                        <span x-text="'Rp ' + item.subtotal.toLocaleString()"></span>
                                                    </div>
                                                </template>
                                                <template x-if="trx.details.length > 3">
                                                    <p class="text-xs text-gray-400 italic">+ <span
                                                            x-text="trx.details.length - 3"></span> produk lainnya...</p>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Total --}}
                                        <div class="text-right font-bold text-blue-600 dark:text-blue-400 mt-2">
                                            Rp <span x-text="trx.subtotal.toLocaleString()"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Digital -->
            <div x-show="showDeleteConfirmDigital" x-transition.opacity.duration.300ms
                class="fixed inset-0 z-[999] flex items-center justify-center bg-black/40 backdrop-blur-sm">
                <div x-show="showDeleteConfirmDigital" x-transition.scale.duration.300ms
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-[90%] max-w-sm text-center">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">
                        Hapus Transaksi Digital?
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">
                        Nomor nota:
                        <span class="font-semibold text-red-600 dark:text-red-400"
                            x-text="digitalToDelete?.nomor_nota || '-'"></span>
                    </p>
                    <div class="flex justify-center gap-3">
                        <button @click="showDeleteConfirmDigital = false"
                            class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100
                           hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Batal
                        </button>
                        <button @click="deleteDigitalTransaction(digitalToDelete)"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700
                           active:scale-[0.97] transition shadow-md">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function posApp() {
            return {
                // ======== STATE GLOBAL ========
                activeTab: 'physical', // 'physical' | 'digital'
                transitioning: false,

                // ======== WIZARD PRODUK DIGITAL ========
                step: 1,
                devices: [],
                apps: [],
                digitalCategories: [],
                digitalBrands: [],
                digitalProducts: [],
                digitalRules: [],
                selectedDevice: null,
                selectedApp: null,
                selectedCategory: null,
                selectedBrand: null,
                selectedProduct: null,
                payment: {
                    customer: '',
                    total: 0,
                    paid: 0
                },
                customers: @json($customers ?? []),

                // ======== PRODUK FISIK ========
                _rawProducts: @json($products),
                categories: @json($categories),
                products: [],
                cart: JSON.parse(localStorage.getItem('cart') || '[]'),
                selectedCategoryPhysical: null,
                paid: 0,
                showToast: false,
                toastMsg: '',
                showReview: false,
                showSuccess: false,
                showHistory: false,
                showDetailModal: false,
                lastTransaction: {
                    total: 0,
                    dibayar: 0,
                    kembalian: 0
                },
                selectedCustomer: '',
                transactionsToday: [],
                selectedTransaction: null,
                summary: {
                    total_penjualan: 0,
                    jumlah_transaksi: 0,
                    total_produk_terjual: 0
                },
                showDigitalReviewModal: false,
                editingTotal: false,
                showHistoryDigital: false,
                apps: [],
                selectedAppFilter: '',
                digitalTransactions: [],
                loadingDigitalTransactions: false,
                digitalAppSummary: {},
                showDeleteConfirm: false,
                transactionToDelete: null,
                showDeleteConfirmDigital: false,
                digitalToDelete: null,
                digitalSummary: null,
                showOptionModal: false,
                selectedProduct: null,
                selectedProductOptions: [],
                showCloseBookModal: false,
                closeBookData: null,
                lebih: 0,
                copied: false,

                // ======== INIT UTAMA ========
                async init() {
                    this.loadCart();

                    // === AUTO BARCODE SCANNER DETECTION ===
                    let buffer = "";
                    let lastTime = Date.now();

                    document.addEventListener("keydown", (e) => {
                        const now = Date.now();
                        const diff = now - lastTime;

                        // Kalau jeda antar tombol lebih dari 50ms → reset buffer
                        if (diff > 50) buffer = "";

                        // Abaikan tombol non-karakter
                        if (e.key.length === 1) {
                            buffer += e.key;
                        }

                        // Saat tekan Enter → kirim ke handleBarcodeInput
                        if (e.key === "Enter" && buffer.length > 3) {
                            e.preventDefault();

                            const code = buffer.trim();
                            buffer = "";

                            // Panggil handler yang sudah kamu buat
                            if (this.handleBarcodeInput) {
                                this.handleBarcodeInput({ target: { value: code } });
                            }
                        }

                        lastTime = now;
                    });
                    // === END AUTO BARCODE SCANNER ===

                    // Produk fisik
                    const raw = Array.isArray(this._rawProducts)
                        ? this._rawProducts
                        : Object.values(this._rawProducts || {});
                    this.products = raw.map(p => ({
                        id: p.id,
                        name: p.name ?? '',
                        code: p.code ?? p.barcode ?? '',
                        price: Number(String(p.price ?? '0').replace(/[^\d]/g, '')),
                        stock: Number(p.stock ?? 0),
                        category_id: p.category_id,
                        category_name: p.category_name ?? 'Tanpa Kategori',
                        category_code: p.category_code ?? '',
                        attribute_values: p.attribute_values ?? [],
                    }));

                    await this.loadDigitalData();

                    this.$watch('showHistoryDigital', value => {
                        if (value) this.loadAppSummaries();
                    });
                },

                // ======== MUAT DATA DIGITAL ========
                async loadDigitalData() {
                    try {
                        const res = await fetch("{{ route('pos.digital.data') }}");
                        const data = await res.json();
                        if (data.success) {
                            this.devices = data.devices;
                            this.apps = data.apps;
                            this.digitalCategories = data.categories;
                            this.digitalBrands = data.brands || [];
                            this.digitalProducts = data.products;
                            this.digitalRules = data.rules;
                            console.log('%c✅ Digital data loaded successfully', 'color:#10b981', data);
                        } else {
                            console.error('⚠️ Gagal memuat data digital:', data);
                        }
                    } catch (err) {
                        console.error('❌ Kesalahan jaringan saat memuat data digital:', err);
                    }
                },

                // ======== GETTER (Dynamic Filtering) ========
                get categoriesForSelectedApp() {
                    if (!this.selectedApp) return [];
                    const catIds = [...new Set(this.digitalProducts
                        .filter(p => Number(p.app_id) === Number(this.selectedApp.id))
                        .map(p => Number(p.digital_category_id)))];
                    return this.digitalCategories.filter(c => catIds.includes(Number(c.id)));
                },

                // 🧩 Filter brand berdasarkan kategori & app dari relasi pivot
                get filteredBrandsForSelectedAppAndCategory() {
                    if (!this.selectedApp || !this.selectedCategory) return [];

                    // Ambil produk yang sesuai app & kategori
                    const matchedProducts = this.digitalProducts.filter(p =>
                        Number(p.app_id) === Number(this.selectedApp.id) &&
                        Number(p.digital_category_id) === Number(this.selectedCategory.id)
                    );

                    // Ambil semua brand unik dari produk-produk tersebut
                    const brandMap = new Map();
                    for (const prod of matchedProducts) {
                        if (Array.isArray(prod.digital_brands)) {
                            for (const b of prod.digital_brands) {
                                brandMap.set(b.id, b);
                            }
                        }
                    }
                    return Array.from(brandMap.values());
                },

                // 🧩 Filter Produk berdasarkan app + kategori + brand
                get digitalProductsForSelectedCategoryAndApp() {
                    if (!this.selectedApp || !this.selectedCategory || !this.selectedBrand) return [];

                    return this.digitalProducts.filter(p =>
                        Number(p.app_id) === Number(this.selectedApp.id) &&
                        Number(p.digital_category_id) === Number(this.selectedCategory.id) &&
                        Array.isArray(p.digital_brands) &&
                        p.digital_brands.some(b => Number(b.id) === Number(this.selectedBrand.id))
                    );
                },

                // ======== PRODUK FISIK: KERANJANG ========
                addToCart(p) {
                    const master = this.products.find(x => x.id === p.id);
                    if (!master) return alert('Produk tidak ditemukan.');
                    const existing = this.cart.find(i => i.id === p.id);
                    const desiredQty = (existing ? existing.qty : 0) + 1;

                    if (master.stock < desiredQty) {
                        this.toastMsg = `Stok ${master.name} tidak cukup (tersisa ${master.stock}).`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        return;
                    }

                    if (existing) existing.qty++;
                    else this.cart.push({
                        id: p.id,
                        name: p.name,
                        price: p.price,
                        qty: 1
                    });
                    this.saveCart();
                },

                increaseQty(i) {
                    const item = this.cart[i];
                    const master = this.products.find(x => x.id === item.id);
                    if (!master) return;
                    if (master.stock < item.qty + 1) {
                        this.toastMsg = `Stok ${master.name} tidak cukup (tersisa ${master.stock}).`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        return;
                    }
                    this.cart[i].qty++;
                    this.saveCart();
                },
                decreaseQty(i) {
                    const item = this.cart[i];

                    // ✅ Cari produk master di daftar produk
                    const master = this.products.find(p => p.id === item.id);
                    if (master) {
                        master.stock += 1; // kembalikan stok 1 ke UI
                    }

                    // ✅ Kalau produk punya varian
                    if (item.variant_id) {
                        const prod = this.products.find(p => p.id === item.id);
                        const attr = prod?.attribute_values?.find(a => a.id === item.variant_id);
                        if (attr) attr.stok += 1;
                    }

                    // Hapus / kurangi dari cart
                    if (item.qty > 1) {
                        this.cart[i].qty--;
                    } else {
                        this.cart.splice(i, 1);
                    }

                    this.saveCart();
                },
                total() {
                    return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
                },
                saveCart() {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                },
                loadCart() {
                    this.cart = JSON.parse(localStorage.getItem('cart') || '[]');
                },
                clearCart() {
                    // ✅ Kembalikan semua stok produk ke UI
                    this.cart.forEach(item => {
                        const prod = this.products.find(p => p.id === item.id);
                        if (prod) prod.stock += item.qty;

                        if (item.variant_id) {
                            const attr = prod?.attribute_values?.find(a => a.id === item.variant_id);
                            if (attr) attr.stok += item.qty;
                        }
                    });

                    // Kosongkan keranjang
                    this.cart = [];
                    this.saveCart();
                },
                // ======== KALKULATOR FISIK ========
                addPayment(n) {
                    this.paid += n;
                },
                payExact() {
                    this.paid = this.total();
                },
                handleKey(b) {
                    b === '⌫' ?
                        this.paid = Math.floor(this.paid / 10) :
                        this.paid = Number(String(this.paid) + b);
                },
                change() {
                    return this.paid - this.total();
                },

                // ======== FILTER PRODUK FISIK ========
                switchCategory(cat) {
                    this.transitioning = true;
                    setTimeout(() => {
                        this.selectedCategoryPhysical = cat;
                        this.transitioning = false;
                    }, 150);
                },
                get filteredProducts() {
                    if (!this.selectedCategoryPhysical) return this.products;
                    return this.products.filter(p => p.category_id === this.selectedCategoryPhysical.id);
                },

                // ======== MODAL REVIEW FISIK ========
                openReviewModal() {
                    if (this.cart.length === 0) {
                        alert("Keranjang masih kosong.");
                        return;
                    }

                    if (this.total() === 0) {
                        alert("Total transaksi tidak valid.");
                        return;
                    }

                    if (this.paid < this.total()) {
                        alert("Uang yang dibayar belum cukup.");
                        return;
                    }

                    this.showReview = true;
                },

                // ======== CHECKOUT PRODUK FISIK (sinkron dengan backend) ========
                async confirmCheckout() {
                    try {
                        if (this.cart.length === 0) {
                            alert("Keranjang kosong.");
                            return;
                        }

                        const payload = {
                            cart: this.cart.map(i => ({
                                id: i.id,
                                qty: i.qty,
                                price: i.price,
                                product_attribute_value_id: i.variant_id ?? null, // ubah jadi nama field sesuai backend
                            })),
                            subtotal: this.total(),
                            dibayar: this.paid,
                            kembalian: this.change(),
                            customer_id: this.selectedCustomer || null,
                        };

                        const res = await fetch("{{ route('pos.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload),
                        });

                        const result = await res.json();

                        if (res.ok && result.success) {
                            // ✅ Transaksi berhasil
                            this.showReview = false;
                            this.showSuccess = true;
                            this.lastTransaction = {
                                total: payload.subtotal,
                                dibayar: payload.dibayar,
                                kembalian: payload.kembalian
                            };
                            this.clearCart();
                            this.paid = 0;

                        } else {
                            // ⚠️ Tampilkan detail error
                            console.error("❌ Transaksi gagal:", {
                                status: res.status,
                                statusText: res.statusText,
                                result,
                            });

                            // Jika Laravel mengembalikan error validasi
                            if (result.errors) {
                                console.table(result.errors);
                                const firstError = Object.values(result.errors)[0][0];
                                alert(`Validasi gagal: ${firstError}`);
                            } 
                            // Jika ada pesan umum dari controller
                            else if (result.message) {
                                alert(`Gagal: ${result.message}`);
                            } 
                            // Jika tidak ada detail message
                            else {
                                alert(`Terjadi kesalahan (HTTP ${res.status}): ${res.statusText}`);
                            }
                        }

                    } catch (err) {
                        console.error("🔥 Error confirmCheckout:", err);
                        alert("Kesalahan saat memproses transaksi. Cek console untuk detail.");
                    }
                },

                // ======== RIWAYAT TRANSAKSI HARI INI ========
                async loadTodayTransactions() {
                    try {
                        const res = await fetch("{{ route('pos.today') }}");
                        const data = await res.json();
                        if (data.success) {
                            this.transactionsToday = data.transactions;
                            this.summary = data.summary || {
                                total_penjualan: 0,
                                jumlah_transaksi: 0,
                                total_produk_terjual: 0,
                            };
                            this.showHistory = true;
                        } else {
                            console.error("⚠️ Gagal memuat riwayat:", data.message);
                            alert(data.message || "Gagal memuat riwayat transaksi hari ini.");
                        }
                    } catch (err) {
                        console.error("❌ Kesalahan jaringan:", err);
                        alert("Terjadi kesalahan saat memuat data transaksi hari ini.");
                    }
                },

                // ======== CHECKOUT DIGITAL ========
                async confirmDigitalTransaction() {
                    try {
                        const payload = {
                            device_id: this.selectedDevice?.id,
                            app_id: this.selectedApp?.id,
                            digital_brand_id: this.selectedBrand?.id,
                            digital_product_id: this.selectedProduct?.id,
                            customer_id: this.selectedCustomer || null,
                            nominal: this.selectedProduct?.base_price || 0,
                            harga_jual: this.selectedProduct?.base_price || 0,
                            subtotal: this.payment.total,
                            dibayar: this.payment.paid,
                            kembalian: this.payment.paid - this.payment.total,
                            total: this.payment.total,
                        };

                        console.log('%c🚀 Sending Digital Checkout Payload:', 'color:#2563eb;font-weight:bold',
                            payload);

                        const res = await fetch("{{ route('pos.digital.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload)
                        });

                        console.log('%c📡 Response Status:', 'color:#10b981;font-weight:bold', res.status, res
                            .statusText);

                        let result;
                        try {
                            result = await res.json();
                        } catch (parseError) {
                            const rawText = await res.text();
                            console.error('❌ Gagal parse JSON dari server:', parseError, '\nRaw response:\n', rawText);
                            throw new Error('Response bukan JSON valid.');
                        }

                        console.log('%c🧭 Server Response:', 'color:#9333ea;font-weight:bold', result);

                        if (result.success) {
                            this.showSuccess = true;
                            this.lastTransaction = {
                                total: payload.total,
                                dibayar: payload.dibayar,
                                kembalian: payload.kembalian,
                            };

                            // 🔁 Reset semua state setelah transaksi sukses
                            this.step = 1;
                            this.payment = {
                                customer: '',
                                total: 0,
                                paid: 0
                            };
                            this.selectedDevice = null;
                            this.selectedApp = null;
                            this.selectedCategory = null;
                            this.selectedBrand = null;
                            this.selectedProduct = null;
                        } else {
                            console.error('%c💥 Server Error:', 'color:#dc2626;font-weight:bold', result.error ||
                                '(no message)');
                            alert(result.message || 'Gagal menyimpan transaksi.');
                        }
                    } catch (err) {
                        console.error('%c🔥 Exception saat mengirim transaksi:', 'color:#ef4444;font-weight:bold', err);
                        alert('Kesalahan jaringan atau error internal. Lihat console untuk detail.');
                    }
                },
                async loadDigitalTransactions() {
                    this.loadingDigitalTransactions = true;
                    try {
                        const res = await fetch(`/digital-transactions?app_id=${this.selectedAppFilter}`);
                        const data = await res.json();
                        this.digitalTransactions = data.transactions;
                        this.digitalSummary = data.summary_total; // 🆕 ambil total subtotal
                    } catch (e) {
                        console.error('Gagal memuat transaksi digital', e);
                    } finally {
                        this.loadingDigitalTransactions = false;
                    }
                },
                async loadAppSummaries() {
                    try {
                        const res = await fetch('/digital-transactions');
                        const data = await res.json();
                        this.digitalAppSummary = data.summary_per_app || {};
                    } catch (err) {
                        console.error('Gagal memuat summary per app:', err);
                    }
                },
                async confirmDelete(trx) {
                    this.transactionToDelete = trx;
                    this.showDeleteConfirm = true;
                },

                async deleteTransaction(trx) {
                    if (!trx) return;

                    try {
                        const res = await fetch(`/pos/transactions/${trx.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        const result = await res.json();
                        console.log("🗑️ Hapus transaksi result:", result);

                        if (result.success) {
                            // Tutup modal konfirmasi
                            this.showDeleteConfirm = false;

                            // ✅ Hapus transaksi dari daftar
                            this.transactionsToday = this.transactionsToday.filter(t => t.id !== trx.id);

                            // ✅ Update summary dasar
                            this.summary.jumlah_transaksi = this.transactionsToday.length;
                            this.summary.total_penjualan = this.transactionsToday.reduce((sum, t) => sum + (t.subtotal || 0), 0);
                            this.summary.total_produk_terjual = this.transactionsToday.reduce(
                                (sum, t) => sum + (t.details?.reduce((a, d) => a + (d.qty || 0), 0) || 0),
                                0
                            );

                            // ✅ Rehitung kategori terjual
                            const categoryMap = {};
                            this.transactionsToday.forEach(t => {
                                (t.details || []).forEach(d => {
                                    const catName = d.category_name || 'Lainnya';
                                    categoryMap[catName] = (categoryMap[catName] || 0) + (d.qty || 0);
                                });
                            });

                            this.summary.categories = Object.entries(categoryMap).map(([name, pcs]) => ({
                                name,
                                pcs,
                            }));

                            // ✅ Toast
                            this.toastMsg = result.message || 'Transaksi berhasil dihapus.';
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 2000);
                        } else {
                            this.toastMsg = result.message || 'Gagal menghapus transaksi.';
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 2000);
                        }

                    } catch (err) {
                        console.error("🔥 Error saat hapus:", err);
                        this.toastMsg = 'Terjadi kesalahan saat menghapus transaksi.';
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2000);
                    }
                },

                confirmDeleteDigital(trx) {
                    this.digitalToDelete = trx;
                    this.showDeleteConfirmDigital = true;
                },

                async deleteDigitalTransaction(trx) {
                    if (!trx) return;

                    try {
                        const res = await fetch(`/digital-transactions/${trx.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        const result = await res.json();
                        console.log("🗑️ Delete digital transaction:", result);

                        if (result.success) {
                            // 1️⃣ Hapus transaksi dari daftar
                            this.digitalTransactions = this.digitalTransactions.filter(t => t.id !== trx.id);

                            // 2️⃣ Kurangi total summary secara dinamis
                            if (this.digitalSummary && trx.subtotal) {
                                this.digitalSummary -= Number(trx.subtotal);
                            }

                            // 3️⃣ Tutup modal
                            this.showDeleteConfirmDigital = false;

                            // 4️⃣ Tampilkan notifikasi
                            this.toastMsg = result.message || "Transaksi digital berhasil dihapus.";
                            this.toastType = "success";
                            this.showToast = true;
                        } else {
                            this.toastMsg = result.message || "Gagal menghapus transaksi digital.";
                            this.toastType = "error";
                            this.showToast = true;
                        }
                    } catch (err) {
                        console.error("🔥 Error hapus digital:", err);
                        this.toastMsg = "Kesalahan saat menghapus transaksi digital.";
                        this.toastType = "error";
                        this.showToast = true;
                    }
                },

                openProductOptions(product) {
                    const attrs = product.attribute_values || [];

                    // 🚀 Kalau tidak ada varian → langsung masuk keranjang + tampilkan toast
                    if (attrs.length === 0) {
                        this.addToCart(product);
                        this.toastMsg = `${product.name} ditambahkan!`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2000);
                        return;
                    }

                    // 🚀 Kalau cuma 1 varian → langsung tambah ke keranjang juga
                    if (attrs.length === 1) {
                        const opt = attrs[0];
                        const itemName = `${product.name} - ${opt.attribute_value}`;
                        const masterStock = opt.stok ?? 0;

                        const existing = this.cart.find(i => i.id === product.id && i.variant === opt.attribute_value);
                        if (existing) {
                            if (existing.qty + 1 > masterStock) {
                                this.toastMsg = `Stok ${itemName} tidak cukup (tersisa ${masterStock}).`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 2500);
                                return;
                            }
                            existing.qty++;
                        } else {
                            if (masterStock <= 0) {
                                this.toastMsg = `⚠️ Stok ${itemName} habis.`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 2500);
                                return;
                            }
                            this.cart.push({
                                id: product.id,
                                name: itemName,
                                price: product.price,
                                qty: 1,
                                variant: opt.attribute_value,
                                variant_id: opt.id, // ✅ tambahkan ini
                            });

                            // 🔄 Kurangi stok di UI agar langsung terlihat tanpa refresh
                            const attrIndex = product.attribute_values.findIndex(a => a.id === opt.id);
                            if (attrIndex !== -1 && product.attribute_values[attrIndex].stok > 0) {
                                product.attribute_values[attrIndex].stok -= 1;
                            }

                            const prodIndex = this.products.findIndex(p => p.id === product.id);
                            if (prodIndex !== -1 && this.products[prodIndex].stock > 0) {
                                this.products[prodIndex].stock -= 1;
                            }
                        }

                        this.saveCart();
                        this.toastMsg = `${itemName} ditambahkan!`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2000);
                        return;
                    }

                    // 🧩 Kalau varian lebih dari 1 → tampilkan modal
                    this.selectedProduct = product;
                    this.selectedProductOptions = attrs;
                    this.showOptionModal = true;
                },

                // Pilih salah satu varian di modal
                chooseOption(opt) {
                    if (!this.selectedProduct) return;

                    const itemName = `${this.selectedProduct.name} - ${opt.attribute_value}`;
                    const masterStock = opt.stok ?? 0;

                    // Cek stok
                    if (masterStock <= 0) {
                        this.toastMsg = `⚠️ Stok ${itemName} habis.`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        return;
                    }

                    // Cek apakah item sudah ada di keranjang
                    const existing = this.cart.find(i =>
                        i.id === this.selectedProduct.id && i.variant_id === opt.id
                    );

                    if (existing) {
                        if (existing.qty + 1 > masterStock) {
                            this.toastMsg = `Stok ${itemName} tidak cukup (tersisa ${masterStock}).`;
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 2500);
                            return;
                        }
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: this.selectedProduct.id,
                            name: itemName,
                            price: this.selectedProduct.price,
                            qty: 1,
                            variant: opt.attribute_value,
                            variant_id: opt.id, // simpan id varian
                        });
                    }

                    // 🔄 Update stok UI sementara (bukan database)
                    const attrIndex = this.selectedProductOptions.findIndex(a => a.id === opt.id);
                    if (attrIndex !== -1 && this.selectedProductOptions[attrIndex].stok > 0) {
                        this.selectedProductOptions[attrIndex].stok -= 1;
                    }

                    const prodIndex = this.products.findIndex(p => p.id === this.selectedProduct.id);
                    if (prodIndex !== -1 && this.products[prodIndex].stock > 0) {
                        this.products[prodIndex].stock -= 1;
                    }

                    this.saveCart();

                    // ✅ Tutup modal langsung setelah pilih
                    this.showOptionModal = false;
                    this.selectedProduct = null;
                    this.selectedProductOptions = [];

                    // ✅ Tampilkan notifikasi
                    this.toastMsg = `${itemName} ditambahkan!`;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 2000);
                },

                // Update scanner agar mendeteksi produk bervarian
                handleBarcodeInput(e) {
                    const code = e.target.value.trim();
                    if (!code) return e.target.value = '';
                    const found = this.products.find(p => String(p.code) === String(code));
                    if (!found) {
                        this.toastMsg = `Produk dengan barcode ${code} tidak ditemukan.`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2000);
                        e.target.value = '';
                        return;
                    }

                    const attrs = found.attribute_values || [];

                    // Jika produk punya varian
                    if (attrs.length > 0) {
                        // Jika cuma 1 varian -> otomatis pilih varian tersebut (sama seperti chooseOption)
                        if (attrs.length === 1) {
                            const opt = attrs[0];

                            // cek stok varian
                            const masterStock = opt.stok ?? 0;
                            if (masterStock <= 0) {
                                this.toastMsg = `⚠️ Stok ${found.name} - ${opt.attribute_value} habis.`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 2500);
                                e.target.value = '';
                                return;
                            }

                            // siapkan selectedProduct agar chooseOption berfungsi konsisten
                            this.selectedProduct = found;
                            this.selectedProductOptions = attrs;

                            // langsung pilih opt (chooseOption akan menutup modal, update cart, dsb)
                            this.chooseOption(opt);

                            e.target.value = '';
                            return;
                        }

                        // Kalau varian lebih dari 1 -> buka modal pilihan
                        this.selectedProduct = found;
                        this.selectedProductOptions = attrs;
                        this.showOptionModal = true;
                        e.target.value = '';
                        return;
                    }

                    // Kalau tidak ada varian, langsung tambahkan ke keranjang
                    const existing = this.cart.find(i => i.id === found.id);
                    const qtyInCart = existing ? existing.qty : 0;
                    if (found.stock <= qtyInCart) {
                        this.toastMsg = `Stok ${found.name} tidak cukup (tersisa ${found.stock}).`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        e.target.value = '';
                        return;
                    }

                    this.addToCart(found);
                    this.toastMsg = `${found.name} ditambahkan!`;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 2000);
                    e.target.value = '';
                },

                handleCloseBook() {
                    console.log("📘 Fetching close-book data...");
                    fetch("{{ route('pos.closebook.data') }}")
                        .then(res => {
                            console.log("🔗 Response status:", res.status);
                            return res.json();
                        })
                        .then(data => {
                            console.log("✅ Data diterima:", data);
                            this.closeBookData = data;
                            this.showCloseBookModal = true;
                        })
                        .catch(err => {
                            console.error("❌ Fetch gagal:", err);
                            alert("Terjadi kesalahan saat mengambil data tutup buku: " + err.message);
                        });
                },
                formatRupiah(angka) {
                    if (angka == null) return 'Rp 0';
                    return 'Rp ' + Number(angka).toLocaleString('id-ID');
                },
                formatLebihInput(event) {
                    // Ambil hanya angka
                    let raw = event.target.value.replace(/[^\d]/g, '');
                    if (raw === '') raw = '0';

                    // Format ke Rupiah
                    const formatted = Number(raw).toLocaleString('id-ID');

                    // Update tampilan input
                    event.target.value = formatted;

                    // Simpan nilai numeriknya ke variable lebih
                    this.lebih = Number(raw);
                },
                async copyCloseBook() {
                    if (!this.closeBookData) return;

                    const d = this.closeBookData;
                    const totalAkhir = (Number(d.grandTotal ?? 0) + Number(this.lebih || 0));
                    const lebihText = this.lebih > 0 ? `Lebih => Rp ${this.lebih.toLocaleString('id-ID')}\n` : '';

                    let text = '';

                    // 🗓️ Tanggal
                    text += `${d.tanggal}\n\n`;

                    // 📦 Barang & Digital
                    text += `Barang : Rp ${Number(d.barangTotal).toLocaleString('id-ID')}\n`;
                    d.digitalPerApp.forEach(app => {
                        text += `${app.name} : Rp ${Number(app.total).toLocaleString('id-ID')}\n`;
                    });

                    // 🔸 Total Penjualan
                    text += `---------------------------\n`;
                    text += `Total : Rp ${Number(d.totalPenjualan).toLocaleString('id-ID')}\n\n`;

                    // 💸 Utang
                    if (d.utangList.length > 0) {
                        text += `Utang :\n`;
                        d.utangList.forEach(u => {
                            text += `- ${u.name}: (Rp ${Number(u.subtotal).toLocaleString('id-ID')})\n`;
                        });
                        text += `---------------------------\n`;
                    }

                    // 🧾 Total dan Lebih
                    text += `Total => Rp ${Number(d.totalSetelahUtang).toLocaleString('id-ID')}\n`;
                    if (this.lebih > 0) text += lebihText;

                    // 💰 Grand Total (pakai total akhir!)
                    text += `Total => Rp ${totalAkhir.toLocaleString('id-ID')}\n`;
                    text += `---------------------------\n`;

                    // 🏦 Transfer & Tarik
                    text += `*Total TF : Rp ${Number(d.totalTransfer).toLocaleString('id-ID')}*\n`;
                    text += `*Total Tarik : Rp ${Number(d.totalTarik).toLocaleString('id-ID')}*`;

                    // 📋 Copy ke clipboard
                    await navigator.clipboard.writeText(text);

                    // ✨ Animasi tombol Copy
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                },
                async handleFinalCloseBook() {
                    try {
                        this.showConfirmCloseBook = false;
                        this.showCloseBookModal = false;

                        // 🧮 Hitung total akhir (cukup GrandTotal + Lebih)
                        const totalFinal = (this.closeBookData?.grandTotal ?? 0) + (this.lebih || 0);

                        console.log('🚀 Mengirim data tutup buku:', {
                            tanggal: this.closeBookData?.tanggal,
                            total_final: totalFinal,
                        });

                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                        if (!csrf) {
                            alert('❌ Token CSRF tidak ditemukan.');
                            return;
                        }

                        const response = await fetch('{{ route("cashbook.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                deskripsi: `Penjualan Tanggal ${this.closeBookData?.tanggal}`,
                                type: 'IN',
                                nominal: totalFinal,
                                outlet_id: {{ Auth::user()->outlet_id ?? 1 }},
                                cashbook_category_id: 3,
                                cashbook_wallet_id: 1,
                            }),
                        });

                        console.log('📬 Response Status:', response.status);
                        const rawText = await response.text();
                        console.log('🧾 Response Body:', rawText);

                        let result = {};
                        try {
                            result = JSON.parse(rawText);
                        } catch {
                            console.warn('⚠️ Respon bukan JSON valid (mungkin redirect / HTML error).');
                        }

                        if (result.success) {
                            const formatted = totalFinal.toLocaleString('id-ID');
                            const toast = document.createElement('div');
                            toast.textContent = `✅ Tutup buku berhasil! Rp ${formatted}`;
                            toast.className = 'fixed bottom-5 right-5 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                            document.body.appendChild(toast);
                            setTimeout(() => toast.remove(), 3000);
                            setTimeout(() => window.location.href = '/pembukuan', 1500);
                        } else {
                            alert(`❌ Gagal menyimpan: ${result.message || 'Server tidak merespons.'}`);
                        }

                    } catch (error) {
                        console.error('💥 Gagal mengirim data ke pembukuan:', error);
                        alert('❌ Terjadi kesalahan jaringan.');
                    }
                }
            }
        }

        // ======== HERO ICONS MAP ========
        window.heroicons = {
            'device-phone-mobile': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 2.25h9a1.5 1.5 0 011.5 1.5v16.5a1.5 1.5 0 01-1.5 1.5h-9a1.5 1.5 0 01-1.5-1.5V3.75a1.5 1.5 0 011.5-1.5zM9 18.75h6" /></svg>`,
            'credit-card': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.5A2.25 2.25 0 014.5 5.25h15a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 16.5v-9zM2.25 9h19.5" /></svg>`,
            'computer-desktop': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18v10.5H3zM7.5 19.5h9" /></svg>`,
            'wifi': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 9.75a15.375 15.375 0 0119.5 0M5.25 12.75a10.5 10.5 0 0113.5 0M8.25 15.75a5.625 5.625 0 017.5 0M12 18.75h.008v.008H12v-.008z" /></svg>`,
            'printer': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 9V4.5h10.5V9m-10.5 0h10.5m-10.5 0v10.5h10.5V9m-10.5 0h-3A2.25 2.25 0 001.5 11.25v5.25A2.25 2.25 0 003.75 18.75H6.75" /></svg>`,
            'server-stack': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h15v3H4.5zm0 6h15v3h-15zm0 6h15v3h-15z" /></svg>`,
        };
    </script>
@endsection

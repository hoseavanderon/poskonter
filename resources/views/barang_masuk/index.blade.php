@extends('layouts.app')

@section('content')

    <div x-data="barangMasuk()" class="w-full mx-auto py-10 px-8 text-gray-100">
        <!-- 🏷️ Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold flex items-center gap-2 text-blue-400">
                <span>📦 Barang Masuk</span>
            </h1>
            <p class="text-gray-400 mt-1">Tambahkan Barang Yang Di Dapat Dari Suppliers</p>
        </div>

        <!-- 🧾 Supplier -->
        <div class="bg-[#1B2332] rounded-2xl p-6 mb-6 shadow-lg border border-[#2A3242] w-full">
            <label class="block font-semibold text-gray-300 mb-2">Supplier</label>
            <div @click="openSupplierModal"
                class="flex items-center gap-2 border border-[#2A3242] rounded-xl px-4 py-3 cursor-pointer hover:border-blue-400 transition bg-[#222B3A]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M5 11a6 6 0 1112 0 6 6 0 01-12 0z" />
                </svg>
                <span x-text="selectedSupplier ? selectedSupplier.name : 'Pilih supplier...'"></span>
            </div>
        </div>

        <!-- 📦 Product Form -->
        <div class="bg-[#1B2332] rounded-2xl p-8 shadow-lg border border-[#2A3242] w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-300">Barang</h2>
                <button @click="addProduct"
                    class="bg-blue-500 text-white text-sm px-4 py-2 rounded-xl hover:bg-blue-400 transition flex items-center gap-1">
                    <span>＋</span> Tambah Barang
                </button>
            </div>

            <template x-for="(item, index) in products" :key="index">
                <div class="flex flex-wrap items-center gap-4 mb-5 border border-[#2A3242] rounded-xl p-5 bg-[#222B3A]">
                    <div class="w-8 text-center font-bold text-gray-400" x-text="index + 1"></div>

                    <!-- Product -->
                    <div class="flex-1 min-w-[220px]">
                        <label class="text-sm text-gray-400">Barang</label>
                        <div @click="openProductModal(index)"
                            class="flex items-center justify-between border border-[#2A3242] rounded-lg px-3 py-2 bg-[#111827] cursor-pointer hover:border-blue-400">
                            <span class="truncate text-sm" x-text="item.product_name || 'Select product...'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M5 11a6 6 0 1112 0 6 6 0 01-12 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Attribute -->
                    <div class="flex-1 min-w-[160px]">
                        <label class="text-sm text-gray-400">Attribute</label>
                        <input type="text" x-model="item.product_attribute"
                            class="w-full border border-[#2A3242] rounded-lg px-3 py-2 bg-[#111827] text-gray-300 text-sm"
                            readonly>
                    </div>

                    <!-- Attribute Value -->
                    <div class="flex-1 min-w-[180px] relative" @click.away="item.showDropdown = false">
                        <label class="text-sm text-gray-400">Isi</label>

                        <!-- Input dinamis -->
                        <template
                            x-if="(item.product_attribute || '').toLowerCase().includes('expired') || (item.product_attribute || '').toLowerCase().includes('tanggal')">
                            <input type="date" x-model="item.attribute_value" @focus="item.showDropdown = true"
                                @input="item.showDropdown = false"
                                class="w-full border border-[#2A3242] rounded-lg px-3 py-2 bg-[#111827] text-gray-100 text-sm">
                        </template>

                        <template
                            x-if="!(item.product_attribute || '').toLowerCase().includes('expired') && !(item.product_attribute || '').toLowerCase().includes('tanggal')">
                            <input type="text" x-model="item.attribute_value" @focus="item.showDropdown = true"
                                @input="item.showDropdown = false" placeholder="Isi attribute"
                                class="w-full border border-[#2A3242] rounded-lg px-3 py-2 bg-[#111827] text-gray-100 text-sm">
                        </template>

                        <!-- Dropdown -->
                        <template x-if="item.showDropdown && availableAttributeValues(item.product_id).length > 0">
                            <ul
                                class="absolute z-20 w-full bg-[#1B2332] border border-[#2A3242] mt-1 rounded-lg shadow-lg max-h-32 overflow-y-auto">
                                <template x-for="val in availableAttributeValues(item.product_id)" :key="val">
                                    <li @click="item.attribute_value = val; item.showDropdown = false"
                                        class="px-3 py-1.5 hover:bg-blue-500 hover:text-white cursor-pointer text-gray-300 text-sm"
                                        x-text="val"></li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <!-- Harga -->
                    <div class="flex-1 min-w-[140px] relative" @click.away="item.showHargaDropdown = false">
                        <label class="text-sm text-gray-400">Harga</label>
                        <input type="text" x-model="item.hargaDisplay" @focus="item.showHargaDropdown = true"
                            @input.debounce.100ms="onHargaInput(item)"
                            class="w-full border border-[#2A3242] rounded-lg px-3 py-2 bg-[#111827] text-gray-100 text-sm"
                            placeholder="Masukkan harga">

                        <!-- Dropdown Harga -->
                        <template x-if="item.showHargaDropdown && availableHargaValues(item.product_id).length > 0">
                            <ul
                                class="absolute z-20 w-full bg-[#1B2332] border border-[#2A3242] mt-1 rounded-lg shadow-lg max-h-32 overflow-y-auto">
                                <template x-for="val in availableHargaValues(item.product_id)" :key="val">
                                    <li @click="item.harga = val; item.hargaDisplay = formatRupiah(val); item.showHargaDropdown = false"
                                        class="px-3 py-1.5 hover:bg-blue-500 hover:text-white cursor-pointer text-gray-300 text-sm"
                                        x-text="formatRupiah(val)"></li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <!-- Quantity -->
                    <div class="flex-1 min-w-[100px]">
                        <label class="text-sm text-gray-400">PCS</label>
                        <input type="number" min="0" x-model="item.pcs"
                            class="w-full border border-[#2A3242] rounded-lg px-3 py-2 bg-[#111827] text-gray-100 text-sm">
                    </div>

                    <!-- Delete -->
                    <button @click="removeProduct(index)" class="text-gray-400 hover:text-red-400 mt-6">🗑️</button>
                </div>
            </template>

            <!-- Subtotal -->
            <div class="flex justify-end mt-6 border-t border-[#2A3242] pt-4">
                <div class="text-right">
                    <p class="text-gray-400 text-sm">Subtotal:</p>
                    <h3 class="text-2xl font-bold text-blue-400" x-text="formatRupiah(totalSubtotal())"></h3>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-5">
                <button @click="showConfirmModal = true"
                    class="w-full bg-blue-500 text-white py-4 rounded-xl text-lg hover:bg-blue-400 transition font-semibold">
                    Tambahkan Barang
                </button>
            </div>
        </div>

        <!-- 🔔 Confirm Modal -->
        <div x-show="showConfirmModal" x-transition
            class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-[#1B2332] w-full max-w-md rounded-2xl p-6 shadow-xl border border-[#2A3242]">
                <h3 class="text-lg font-semibold text-gray-200 mb-3">Konfirmasi Penambahan Barang</h3>
                <p class="text-gray-400 mb-5">Apakah Anda yakin ingin menambahkan barang ini ke dalam sistem?</p>

                <label class="flex items-center gap-2 mb-6 cursor-pointer">
                    <input type="checkbox" x-model="addToBookkeeping"
                        class="form-checkbox h-4 w-4 text-blue-500 rounded border-gray-600 bg-[#222B3A]">
                    <span class="text-gray-300">Tambahkan ke pembukuan?</span>
                </label>

                <div class="flex justify-end gap-3">
                    <button @click="showConfirmModal = false"
                        class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button @click="confirmSubmit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400 transition">
                        Ya, Tambahkan
                    </button>
                </div>
            </div>
        </div>

        <!-- 🔍 Supplier Modal -->
        <div 
            x-show="showSupplierModal" 
            x-transition 
            @keydown.escape.window="showSupplierModal = false"
            @click.self="showSupplierModal = false"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
        >
            <div 
                class="bg-[#1B2332] w-full max-w-lg rounded-2xl p-6 shadow-xl border border-[#2A3242]" 
                x-trap.inert.noscroll="showSupplierModal"
                @click.stop
            >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-200">Select Supplier</h3>
                    <button @click="showSupplierModal = false" class="text-gray-400 hover:text-gray-200">✖</button>
                </div>

                <div class="relative mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M5 11a6 6 0 1112 0 6 6 0 01-12 0z" />
                    </svg>
                    <input type="text" placeholder="Search suppliers..." 
                        x-model="supplierSearch"
                        x-ref="supplierInput"
                        @input.debounce.400ms="searchSuppliers"
                        class="w-full border border-[#2A3242] rounded-lg px-10 py-2 bg-[#222B3A] text-gray-100 focus:border-blue-400 focus:ring-blue-400" />
                </div>

                <div class="max-h-64 overflow-y-auto divide-y divide-[#2A3242]">
                    <template x-for="supplier in filteredSuppliers()" :key="supplier.id">
                        <div @click="selectSupplier(supplier)" class="p-3 hover:bg-[#222B3A] cursor-pointer rounded-lg">
                            <div class="font-semibold text-gray-100" x-text="supplier.name"></div>
                            <div class="text-sm text-gray-400" x-text="supplier.email"></div>
                        </div>
                    </template>
                    <div x-show="filteredSuppliers().length === 0" class="text-center text-gray-500 py-3">
                        No supplier found
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔍 Product Modal -->
        <div 
            x-show="showProductModal" 
            x-transition 
            @keydown.escape.window="showProductModal = false"
            @click.self="showProductModal = false"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
        >
            <div 
                class="bg-[#1B2332] w-full max-w-lg rounded-2xl p-6 shadow-xl border border-[#2A3242]"
                x-trap.inert.noscroll="showProductModal"
                @click.stop
            >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-200">Select Product</h3>
                    <button @click="showProductModal = false" class="text-gray-400 hover:text-gray-200">✖</button>
                </div>

                <div class="relative mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M5 11a6 6 0 1112 0 6 6 0 01-12 0z" />
                    </svg>
                    <input type="text" placeholder="Search products..." 
                        x-model="productSearch"
                        x-ref="productInput"
                        @input.debounce.400ms="searchProducts"
                        class="w-full border border-[#2A3242] rounded-lg px-10 py-2 bg-[#222B3A] text-gray-100 focus:border-blue-400 focus:ring-blue-400" />
                </div>

                <div class="max-h-64 overflow-y-auto divide-y divide-[#2A3242]">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div @click="selectProduct(product)" class="p-3 hover:bg-[#222B3A] cursor-pointer rounded-lg">
                            <div class="font-semibold text-gray-100" x-text="product.name"></div>
                            <div class="text-sm text-gray-400" x-text="product.category"></div>
                        </div>
                    </template>
                    <div x-show="filteredProducts().length === 0" class="text-center text-gray-500 py-3">
                        No product found
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Toast Notification -->
        <div x-show="toast.show" x-transition x-text="toast.message"
            :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
            class="fixed bottom-5 right-5 text-white px-5 py-3 rounded-lg shadow-lg text-sm z-50">
        </div>
    </div>

    <script>
        function barangMasuk() {
            return {
                selectedSupplier: null,
                showSupplierModal: false,
                showProductModal: false,
                showConfirmModal: false,
                addToBookkeeping: false,
                supplierSearch: '',
                productSearch: '',
                activeProductIndex: null,
                suppliers: [],
                allProducts: [],
                attributeValues: {}, // key = product_id : array of values
                products: [{
                    product_id: null,
                    product_name: '',
                    product_attribute: '',
                    attribute_value: '',
                    harga: '',
                    hargaDisplay: '', // 👈 tambahan
                    pcs: 0,
                    is_manual: false,
                    showDropdown: false,
                    showHargaDropdown: false
                }],
                hargaValues: {}, // key = product_id : [harga lama]
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                init() {
                    // 🔹 Ketika modal supplier dibuka → fokus input
                    this.$watch('showSupplierModal', (value) => {
                        if (value) {
                            this.$nextTick(() => this.$refs.supplierInput?.focus());
                        }
                    });

                    // 🔹 Ketika modal product dibuka → fokus input
                    this.$watch('showProductModal', (value) => {
                        if (value) {
                            this.$nextTick(() => this.$refs.productInput?.focus());
                        }
                    });
                },

                formatRupiah(value) {
                    if (!value) return 'Rp 0';
                    return 'Rp ' + parseInt(value).toLocaleString('id-ID');
                },
                availableHargaValues(product_id) {
                    const vals = this.hargaValues[product_id];
                    return Array.isArray(vals) ? vals : [];
                },
                totalSubtotal() {
                    return this.products.reduce((total, item) => {
                        const harga = parseInt(item.harga || 0);
                        const pcs = parseInt(item.pcs || 0);
                        return total + (harga * pcs);
                    }, 0);
                },

                // 🧾 SUPPLIER
                async searchSuppliers() {
                    if (this.supplierSearch.trim().length < 2) {
                        this.suppliers = [];
                        return;
                    }
                    try {
                        const res = await fetch(
                            `/api/suppliers?q=${encodeURIComponent(this.supplierSearch)}&t=${Date.now()}`);
                        this.suppliers = await res.json();
                    } catch (error) {
                        console.error('Supplier fetch failed:', error);
                        this.suppliers = [];
                    }
                },
                openSupplierModal() {
                    this.supplierSearch = '';
                    this.suppliers = [];
                    this.showSupplierModal = true;
                },
                selectSupplier(supplier) {
                    this.selectedSupplier = supplier;
                    this.showSupplierModal = false;
                },

                // 📦 PRODUCT
                async searchProducts() {
                    if (this.productSearch.trim().length < 2) {
                        this.allProducts = [];
                        return;
                    }
                    try {
                        const res = await fetch(
                            `/api/products?q=${encodeURIComponent(this.productSearch)}&t=${Date.now()}`);
                        this.allProducts = await res.json();
                    } catch (error) {
                        console.error('Product fetch failed:', error);
                        this.allProducts = [];
                    }
                },
                openProductModal(index) {
                    this.activeProductIndex = index;
                    this.productSearch = '';
                    this.allProducts = [];
                    this.showProductModal = true;
                },
                async selectProduct(product) {
                    if (this.activeProductIndex !== null) {
                        const target = this.products[this.activeProductIndex];

                        // Reset data sebelumnya
                        target.product_id = product.id;
                        target.product_name = product.name;
                        target.product_attribute = product.attribute_name ?? '-';
                        target.attribute_value = '';
                        target.harga = '';
                        target.pcs = 0;
                        target.showDropdown = false;
                        target.showHargaDropdown = false;
                    }

                    // 🚀 Tutup modal langsung dulu
                    this.showProductModal = false;

                    // Lanjutkan ambil data di background
                    try {
                        const [attrRes, hargaRes] = await Promise.all([
                            fetch(`/api/attribute-values/${product.id}`),
                            fetch(`/api/harga-values/${product.id}`)
                        ]);

                        if (attrRes.ok) {
                            this.attributeValues[product.id] = await attrRes.json();
                        } else {
                            this.attributeValues[product.id] = [];
                        }

                        if (hargaRes.ok) {
                            this.hargaValues[product.id] = await hargaRes.json();
                        } else {
                            this.hargaValues[product.id] = [];
                        }
                    } catch (error) {
                        console.error('Gagal mengambil data product:', error);
                        this.attributeValues[product.id] = [];
                        this.hargaValues[product.id] = [];
                    }
                },

                // 📋 UTILS
                filteredSuppliers() {
                    return this.suppliers;
                },
                filteredProducts() {
                    return this.allProducts;
                },

                async confirmSubmit() {
                    this.showConfirmModal = false;

                    try {
                        const res = await fetch('/barangmasuk/submit', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                supplier: this.selectedSupplier,
                                addToBookkeeping: this.addToBookkeeping,
                                products: this.products,
                                subtotal: this.totalSubtotal()
                            })
                        });

                        const data = await res.json();
                        if (res.ok) {
                            this.showToast(data.message, 'success');

                            this.products = [{
                                product_id: null,
                                product_name: '',
                                product_attribute: '',
                                attribute_value: '',
                                harga: '',
                                hargaDisplay: '',
                                pcs: 0,
                                showDropdown: false,
                                showHargaDropdown: false
                            }];
                            this.selectedSupplier = null;
                            this.addToBookkeeping = false;
                        } else {
                            this.showToast('Gagal menyimpan barang masuk.', 'error');
                        }
                    } catch (e) {
                        console.error(e);
                        this.showToast('Terjadi kesalahan saat mengirim data.', 'error');
                    }
                },

                // ✏️ Dynamic Input
                availableAttributeValues(product_id) {
                    return this.attributeValues[product_id] || [];
                },
                addProduct() {
                    this.products.push({
                        product_id: null,
                        product_name: '',
                        product_attribute: '',
                        attribute_value: '',
                        harga: '', // ← tambahkan ini juga
                        hargaDisplay: '',
                        pcs: 0,
                        is_manual: false,
                        showDropdown: false,
                        showHargaDropdown: false // ← tambahkan ini juga
                    });
                },
                removeProduct(index) {
                    this.products.splice(index, 1);
                },
                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => this.toast.show = false, 3000);
                },
                onHargaInput(item) {
                    // hapus semua karakter non-digit
                    const cleaned = item.hargaDisplay.replace(/[^\d]/g, '');
                    item.harga = cleaned ? parseInt(cleaned) : 0;

                    // tampilkan kembali dalam format Rupiah
                    item.hargaDisplay = item.harga ? 'Rp ' + item.harga.toLocaleString('id-ID') : '';
                },
            }
        }
    </script>
    <style>
        /* Hilangkan spinner pada input type=number di Chrome, Edge, Safari */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hilangkan spinner pada Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
/* 🧩 Cegah overflow dari elemen fixed/modal Alpine */
body {
    position: relative;
    overflow: hidden;
    height: 100vh;
}

/* ✅ Scroll hanya pada konten utama (main) */
main {
    height: calc(100vh - 64px);
    overflow-y: auto;
}

/* 🚀 Fix tambahan: pastikan modal & toast tidak menambah tinggi halaman */
.fixed,
[ x-show ] {
    overscroll-behavior: contain !important;
}

/* 🧭 Pastikan tidak ada padding global dari html/body */
html, body {
    margin: 0 !important;
    padding: 0 !important;
    scrollbar-gutter: stable both-edges;
}

/* 🌙 Scrollbar custom theme */
main::-webkit-scrollbar {
    width: 10px;
}
main::-webkit-scrollbar-track {
    background: #0f172a;
}
main::-webkit-scrollbar-thumb {
    background-color: #334155;
    border-radius: 10px;
    border: 2px solid #0f172a;
}
main::-webkit-scrollbar-thumb:hover {
    background-color: #475569;
}
main {
    scrollbar-width: thin;
    scrollbar-color: #334155 #0f172a;
}
    </style>
@endsection

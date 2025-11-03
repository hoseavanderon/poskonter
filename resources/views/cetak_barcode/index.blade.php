@extends('layouts.app')

@section('content')
    <style>
        /* 🔹 Hilangkan spinner number input */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        /* 🔹 Tampilan modern garis polos (qty input) */
        .qty-input {
            border: none;
            border-bottom: 1px solid #475569;
            padding: 2px 0;
            width: 42px;
            background: transparent;
            color: #e2e8f0;
            text-align: center;
            font-size: 0.875rem;
            transition: all 0.25s ease;
        }

        .qty-input:focus {
            outline: none;
            border-bottom: 1px solid #3b82f6;
            box-shadow: 0 1px 0 0 #3b82f6;
        }

        .qty-input::placeholder {
            color: #64748b;
        }
    </style>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="barcodePage()" x-init="init()">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-slate-800 rounded-lg p-3 shadow-sm border border-slate-700 flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="relative top-[1px]">
                    <rect x="1" y="4" width="2" height="16" fill="#60A5FA" />
                    <rect x="4" y="4" width="1.5" height="16" fill="#60A5FA" />
                    <rect x="6" y="4" width="0.8" height="16" fill="#60A5FA" />
                    <rect x="8" y="4" width="1.2" height="16" fill="#60A5FA" />
                    <rect x="10.5" y="4" width="1.5" height="16" fill="#60A5FA" />
                    <rect x="13" y="4" width="0.9" height="16" fill="#60A5FA" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-slate-100 flex items-center gap-2">
                Cetak Barcode
            </h1>
        </div>

        <!-- Card utama -->
        <div class="bg-slate-800 rounded-2xl shadow border border-slate-700 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs text-slate-400 mb-1">Pilih Produk</label>
                    <button @click="openModal()"
                        class="w-full sm:w-72 flex items-center justify-between gap-3 px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-slate-300 hover:bg-slate-600 transition">
                        <span x-text="cart.length ? cart.length + ' produk dipilih' : 'Klik untuk memilih produk'"></span>
                        <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="flex-none">
                    <label class="block text-xs text-slate-400 mb-1">Aksi</label>
                    <div class="flex gap-2">
                        <button @click="clearCart()"
                            class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-md text-sm">
                            Clear
                        </button>

                        <form method="POST" action="{{ route('cetakbarcode.print') }}" target="_blank" id="printForm"
                            @submit.prevent="submitForPrint">
                            @csrf
                            <input type="hidden" name="items" id="printItems">
                            <button type="submit" :disabled="!cart.length"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow">
                                Cetak Barcode
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Cart -->
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-700/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Brand • Kategori</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-400">Stok</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-400">Cetak (pcs)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <template x-for="item in cart" :key="item.id">
                            <tr class="hover:bg-slate-700/40 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-md bg-slate-700 flex items-center justify-center text-sm text-slate-300 font-medium">
                                            <span x-text="getInitials(item.name)"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-100">
                                                <span x-text="item.name"></span>
                                                <span x-show="item.variant" class="text-slate-400 text-xs ml-1">(<span
                                                        x-text="item.variant"></span>)</span>
                                            </div>
                                            <div class="text-xs text-slate-500"
                                                x-text="'Barcode: ' + (item.barcode ?? '-')"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-slate-300" x-text="item.brand + ' • ' + item.category">
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-slate-400" x-text="item.stock"></td>

                                <td class="px-4 py-3 text-center">
                                    <input type="number" min="1" x-model.number="item.qty"
                                        @blur="updateQty(item, item.qty)" class="qty-input" placeholder="1">
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button @click="removeFromCart(item.id)"
                                        class="px-3 py-1 text-xs rounded-md bg-red-600 hover:bg-red-700 text-white">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="!cart.length">
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Keranjang cetak kosong — klik <span class="font-medium text-slate-100">Pilih Produk</span>
                                untuk menambah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="modalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click.outside="closeModal()"
                class="bg-slate-800 w-full max-w-4xl rounded-xl border border-slate-700 shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-100">Pilih Produk</h3>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-200">✕</button>
                </div>

                <!-- Search -->
                <div class="p-4 border-b border-slate-700">
                    <input type="text" x-model="modalSearch"
                        placeholder="Cari produk berdasarkan nama, merek, atau kategori..."
                        class="w-full bg-slate-700 border border-slate-600 rounded-md px-3 py-2 text-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none" />
                </div>

                <!-- Product List -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="p in availableProducts" :key="p.id">
                        <div @click="selectProduct(p)"
                            class="flex items-center justify-between px-4 py-3 border-b border-slate-700 hover:bg-slate-700/50 cursor-pointer transition">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 bg-slate-700 rounded-md flex items-center justify-center text-xs text-slate-200 font-medium">
                                    <span x-text="getInitials(p.name)"></span>
                                </div>
                                <div>
                                    <div class="text-sm text-slate-100 font-medium">
                                        <span x-text="p.name"></span>
                                        <span x-show="p.variant" class="text-slate-400 text-xs ml-1">(<span
                                                x-text="p.variant"></span>)</span>
                                    </div>
                                    <div class="text-xs text-slate-400" x-text="'Barcode: ' + (p.barcode ?? '-')"></div>
                                </div>
                            </div>
                            <div class="text-xs text-slate-400">Stok: <span x-text="p.stock"></span></div>
                        </div>
                    </template>

                    <div x-show="!availableProducts.length" class="py-8 text-center text-slate-500">
                        Tidak ada produk ditemukan.
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end p-4 border-t border-slate-700 bg-slate-800">
                    <button @click="closeModal()"
                        class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-md text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <div x-show="showWarning" x-transition.opacity.duration.400ms
            class="fixed inset-0 z-[999] flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div x-transition.scale.duration.300ms
                class="bg-slate-800 text-slate-100 border border-slate-600 rounded-xl shadow-2xl px-6 py-5 max-w-sm text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-400 mx-auto mb-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-.01-9a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
                <p class="text-sm leading-relaxed" x-text="warningMessage"></p>
                <button @click="showWarning = false"
                    class="mt-4 px-4 py-1.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-md text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function barcodePage() {
            return {
                products: @json($products ?? []),
                cart: [],
                modalOpen: false,
                modalSearch: '',
                showWarning: false,
                warningMessage: '',
                init() {},

                // 🔹 Filter produk
                get availableProducts() {
                    const q = this.modalSearch.toLowerCase();
                    return this.products
                        .filter(p => !this.cart.find(c => c.id === p.id))
                        .filter(p =>
                            p.name.toLowerCase().includes(q) ||
                            (p.variant || '').toLowerCase().includes(q) ||
                            (p.brand || '').toLowerCase().includes(q) ||
                            (p.category || '').toLowerCase().includes(q)
                        );
                },

                // 🔹 Utilitas
                getInitials(name) {
                    return (name || '').split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();
                },
                openModal() {
                    this.modalSearch = '';
                    this.modalOpen = true;
                },
                closeModal() {
                    this.modalOpen = false;
                },
                selectProduct(p) {
                    this.cart.push({
                        ...p,
                        qty: 1
                    });
                },
                removeFromCart(id) {
                    this.cart = this.cart.filter(i => i.id !== id);
                },
                clearCart() {
                    if (!this.cart.length) return;
                    this.showPopup('Keranjang dikosongkan.');
                    this.cart = [];
                },

                // 🔹 Popup Warning Modern
                showPopup(msg) {
                    this.warningMessage = msg;
                    this.showWarning = true;
                    setTimeout(() => this.showWarning = false, 3500);
                },

                // 🔹 Cetak Barcode dengan validasi stok
                printCart() {
                    if (!this.cart.length) return this.showPopup('Pilih minimal satu produk.');

                    // 🔸 Validasi stok (tetap sama)
                    for (const item of this.cart) {
                        const qty = parseInt(item.qty) || 0;
                        const stock = parseInt(item.stock) || 0;

                        if (qty <= 0) {
                            this.showPopup(`Jumlah cetak untuk "${item.name}" tidak boleh kosong atau 0.`);
                            return;
                        }

                        if (qty > stock) {
                            this.showPopup(
                                `Jumlah cetak melebihi stok tersedia untuk "${item.name}". (Stok: ${stock}, Cetak: ${qty})`
                            );
                            return;
                        }
                    }

                    // 🔹 Kumpulkan semua label yang akan dicetak
                    const labels = [];
                    this.cart.forEach(item => {
                        const copies = parseInt(item.qty);
                        for (let i = 0; i < copies; i++) labels.push(item);
                    });

                    // 🔹 Hitung tinggi halaman dinamis (Konfigurasi 33mm x 15mm pada kertas 70mm)
                    const labelHeight = 15; // mm (Tinggi label)
                    const labelWidth = 33; // mm (Lebar label)
                    const gap = 4; // mm (Jarak antar label: (70 - 2*33) = 4)
                    const cols = 2;
                    const totalLabels = labels.length;
                    const rows = Math.ceil(totalLabels / cols);
                    const pageHeight = rows * labelHeight + (rows - 1) * gap + 10; // buffer kecil bawah

                    // 🔹 Buka jendela print
                    const w = window.open('', '_blank');
                    w.document.write(`
<html>
<head>
    <meta charset="utf-8">
    <title>Print Barcode</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>
    <style>
        @page {
            size: 60mm ${pageHeight}mm; /* ⬅️ otomatis sesuai jumlah label */
            margin: 0;
        }
        html, body {
            width: 70mm;
            height: ${pageHeight}mm;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            overflow: hidden;
        }
        .sheet {
            display: grid;
            grid-template-columns: repeat(${cols}, ${labelWidth}mm); /* 33mm */
            grid-auto-rows: ${labelHeight}mm;
            gap: ${gap}mm ${gap}mm;
            justify-content: center;
            align-content: start; /* Nempel atas */
            width: 100%;
            height: 100%;
            padding: ${gap / 2}mm 0; /* padding atas agar tidak terlalu mepet */
            box-sizing: border-box;
        }
        .label {
            width: ${labelWidth}mm; /* 33mm */
            height: ${labelHeight}mm; /* 15mm */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .barcode { width: 100%; height: 6mm; } /* Tinggi SVG 6mm */
        .name {
            font-weight: 600;
            font-size: 5.5px;
            line-height: 1;
            margin-top: 1px;
        }
        .code {
            font-size: 5.5px;
            line-height: 1;
            margin-top: 1px;
        }
        .price {
            font-weight: 700;
            font-size: 6.5px;
            color: #000;
            margin-top: 1px;
        }
        @media print {
            header, footer { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="sheet" id="barcode-container"></div>
</body>
</html>
    `);
                    w.document.close();

                    // 🔹 Render barcode setelah window siap
                    w.onload = function() {
                        const container = w.document.getElementById('barcode-container');
                        labels.forEach((it, i) => {
                            const label = w.document.createElement('div');
                            label.className = 'label';
                            const productName = it.variant ? `${it.name} (${it.variant})` : it.name;

                            label.innerHTML = `
                <svg id="barcode-${i}" class="barcode"></svg>
                <div class="name">${productName}</div>
                <div class="code">${it.barcode ?? '-'}</div>
                <div class="price">Rp ${parseInt(it.jual || 0).toLocaleString('id-ID')}</div>
            `;
                            container.appendChild(label);

                            // generate barcode
                            w.JsBarcode(`#barcode-${i}`, it.barcode, {
                                format: 'CODE128',
                                lineColor: '#000',
                                width: 1.1,
                                height: 18, // Tinggi barcode diatur agar muat
                                displayValue: false
                            });
                        });

                        // print otomatis setelah render
                        setTimeout(() => w.print(), 600);
                    };
                },

                updateQty(item, value) {
                    let val = parseInt(value) || 0;

                    // Batas minimal dan maksimal
                    if (val < 1) val = 1;
                    if (item.stock && val > item.stock) val = item.stock;

                    // Update nilai qty
                    item.qty = val;

                    // Paksa Alpine refresh reactive
                    this.cart = [...this.cart];
                },
                submitForPrint() {
                    if (confirm('Lihat preview dulu sebelum cetak?')) {
                        document.getElementById('printForm').action =
                            "{{ route('cetakbarcode.print', ['preview' => true]) }}";
                    } else {
                        document.getElementById('printForm').action = "{{ route('cetakbarcode.print') }}";
                    }
                    document.getElementById('printItems').value = JSON.stringify(this.cart);
                    document.getElementById('printForm').submit();
                }
            }
        }
    </script>
@endsection

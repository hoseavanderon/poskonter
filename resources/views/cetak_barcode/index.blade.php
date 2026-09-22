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

        .bc-page {
            color: var(--text-primary);
        }

        .bc-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            color: #007AFF;
        }

        html.dark .bc-icon {
            background: #1C1C1E;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
            color: #0A84FF;
        }

        .bc-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: #1D1D1F;
        }

        html.dark .bc-title {
            color: #F5F5F7;
        }

        .bc-card {
            background: #FFFFFF;
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            padding: 20px;
        }

        html.dark .bc-card {
            background: #1C1C1E;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45);
        }

        .bc-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #8E8E93;
            margin-bottom: 6px;
        }

        .bc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #F2F2F7;
            border: 1px solid rgba(0, 0, 0, 0.06);
            color: #1D1D1F;
            font-size: 14px;
            transition: background-color 180ms ease, border-color 180ms ease;
        }

        .bc-btn:hover {
            background: #E5E5EA;
            border-color: rgba(0, 0, 0, 0.10);
        }

        html.dark .bc-btn {
            background: #2C2C2E;
            border-color: rgba(255, 255, 255, 0.10);
            color: #F5F5F7;
        }

        html.dark .bc-btn:hover {
            background: #3A3A3C;
        }

        .bc-btn-muted {
            padding: 10px 12px;
            border-radius: 12px;
            background: #F2F2F7;
            border: 0;
            color: #1D1D1F;
            font-size: 14px;
            font-weight: 600;
        }

        .bc-btn-muted:hover {
            background: #E5E5EA;
        }

        html.dark .bc-btn-muted {
            background: #2C2C2E;
            color: #F5F5F7;
        }

        html.dark .bc-btn-muted:hover {
            background: #3A3A3C;
        }

        .bc-btn-primary {
            padding: 10px 16px;
            border-radius: 12px;
            background: #007AFF;
            border: 0;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.25);
        }

        .bc-btn-primary:hover {
            background: #0066D6;
        }

        .bc-btn-primary:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            box-shadow: none;
        }

        .bc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bc-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #8E8E93;
            background: #F2F2F7;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .bc-table th.text-right,
        .bc-table td.text-right {
            text-align: right;
        }

        .bc-table th.text-center,
        .bc-table td.text-center {
            text-align: center;
        }

        html.dark .bc-table th {
            background: #2C2C2E;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .bc-table td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            color: #1D1D1F;
            font-size: 14px;
            vertical-align: middle;
        }

        html.dark .bc-table td {
            border-bottom-color: rgba(255, 255, 255, 0.08);
            color: #F5F5F7;
        }

        .bc-table tbody tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        html.dark .bc-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.04);
        }

        .bc-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F2F2F7;
            color: #1D1D1F;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
        }

        html.dark .bc-avatar {
            background: #2C2C2E;
            color: #F5F5F7;
        }

        .bc-muted {
            color: #8E8E93;
            font-size: 12px;
        }

        .bc-name {
            font-size: 14px;
            font-weight: 600;
            color: #1D1D1F;
        }

        html.dark .bc-name {
            color: #F5F5F7;
        }

        .bc-empty {
            padding: 32px 24px;
            text-align: center;
            color: #8E8E93;
            font-size: 14px;
        }

        .bc-empty strong {
            color: #1D1D1F;
            font-weight: 600;
        }

        html.dark .bc-empty strong {
            color: #F5F5F7;
        }

        .qty-input {
            border: none;
            border-bottom: 1.5px solid #D1D1D6;
            padding: 2px 0;
            width: 42px;
            background: transparent !important;
            color: #1D1D1F !important;
            text-align: center;
            font-size: 0.875rem;
            border-radius: 0 !important;
            box-shadow: none !important;
            transition: border-color 0.2s ease;
        }

        .qty-input:focus {
            outline: none;
            border-bottom-color: #007AFF;
            box-shadow: none !important;
        }

        html.dark .qty-input {
            border-bottom-color: #48484A;
            color: #F5F5F7 !important;
        }

        html.dark .qty-input:focus {
            border-bottom-color: #0A84FF;
        }

        .qty-input::placeholder {
            color: #8E8E93;
        }

        .bc-btn-danger {
            padding: 6px 12px;
            border-radius: 10px;
            background: #FF3B30;
            border: 0;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 600;
        }

        .bc-btn-danger:hover {
            background: #E0352B;
        }

        .bc-modal-shell {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.16);
            overflow: hidden;
        }

        html.dark .bc-modal-shell {
            background: #1C1C1E;
            border-color: rgba(255, 255, 255, 0.10);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        .bc-modal-head,
        .bc-modal-search,
        .bc-modal-foot {
            border-color: rgba(0, 0, 0, 0.06);
        }

        html.dark .bc-modal-head,
        html.dark .bc-modal-search,
        html.dark .bc-modal-foot {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .bc-modal-title {
            font-size: 17px;
            font-weight: 700;
            color: #1D1D1F;
        }

        html.dark .bc-modal-title {
            color: #F5F5F7;
        }

        .bc-page .bc-modal-search input,
        .bc-page .bc-modal-search input:focus {
            background: #F2F2F7 !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: 12px !important;
            color: #1D1D1F !important;
            box-shadow: none !important;
        }

        html.dark .bc-page .bc-modal-search input,
        html.dark .bc-page .bc-modal-search input:focus {
            background: #2C2C2E !important;
            border-color: rgba(255, 255, 255, 0.10) !important;
            color: #F5F5F7 !important;
        }

        .bc-row {
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: background-color 160ms ease;
        }

        .bc-row:hover {
            background: #F2F2F7;
        }

        html.dark .bc-row {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        html.dark .bc-row:hover {
            background: #2C2C2E;
        }

        .bc-warn {
            background: #FFFFFF;
            color: #1D1D1F;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.16);
        }

        html.dark .bc-warn {
            background: #1C1C1E;
            color: #F5F5F7;
            border-color: rgba(255, 255, 255, 0.10);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }
    </style>

    <div class="bc-page max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="barcodePage()" x-init="init()">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-8 page-enter" style="--d:0">
            <div class="bc-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="4" width="2" height="16" fill="currentColor" />
                    <rect x="4" y="4" width="1.5" height="16" fill="currentColor" />
                    <rect x="6" y="4" width="0.8" height="16" fill="currentColor" />
                    <rect x="8" y="4" width="1.2" height="16" fill="currentColor" />
                    <rect x="10.5" y="4" width="1.5" height="16" fill="currentColor" />
                    <rect x="13" y="4" width="0.9" height="16" fill="currentColor" />
                    <rect x="15" y="4" width="2" height="16" fill="currentColor" />
                    <rect x="18" y="4" width="1" height="16" fill="currentColor" />
                    <rect x="20.5" y="4" width="2" height="16" fill="currentColor" />
                </svg>
            </div>
            <h1 class="bc-title">Cetak Barcode</h1>
        </div>

        <!-- Card utama -->
        <div class="bc-card page-enter" style="--d:100">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <label class="bc-label">Pilih Produk</label>
                    <button type="button" @click="openModal()" class="bc-btn w-full sm:w-72">
                        <span x-text="cart.length ? cart.length + ' produk dipilih' : 'Klik untuk memilih produk'"></span>
                        <svg class="w-4 h-4 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="flex-none">
                    <label class="bc-label">Aksi</label>
                    <div class="flex gap-2">
                        <button type="button" @click="clearCart()" class="bc-btn-muted">
                            Clear
                        </button>

                        <form method="POST" action="{{ route('cetakbarcode.print') }}" target="_blank" id="printForm"
                            @submit.prevent="submitForPrint">
                            @csrf
                            <input type="hidden" name="items" id="printItems">
                            <button type="submit" :disabled="!cart.length" class="bc-btn-primary">
                                Cetak Barcode
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Cart -->
            <div class="mt-6 overflow-x-auto rounded-16" style="border-radius: 14px; overflow: hidden;">
                <table class="bc-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Brand • Kategori</th>
                            <th class="text-right">Stok</th>
                            <th class="text-center">Cetak (pcs)</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in cart" :key="item.id">
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="bc-avatar">
                                            <span x-text="getInitials(item.name)"></span>
                                        </div>
                                        <div>
                                            <div class="bc-name">
                                                <span x-text="item.name"></span>
                                                <span x-show="item.variant" class="bc-muted ml-1">(<span
                                                        x-text="item.variant"></span>)</span>
                                            </div>
                                            <div class="bc-muted"
                                                x-text="'Barcode: ' + (item.barcode ?? '-')"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="bc-muted" style="font-size: 14px;"
                                    x-text="item.brand + ' • ' + item.category"></td>
                                <td class="text-right bc-muted" style="font-size: 14px;" x-text="item.stock"></td>

                                <td class="text-center">
                                    <input type="number" min="1" x-model.number="item.qty"
                                        @blur="updateQty(item, item.qty)" class="qty-input" placeholder="1">
                                </td>

                                <td class="text-right">
                                    <button type="button" @click="removeFromCart(item.id)" class="bc-btn-danger">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="!cart.length">
                            <td colspan="5" class="bc-empty">
                                Keranjang cetak kosong — klik <strong>Pilih Produk</strong>
                                untuk menambah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="modalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
            <div @click.outside="closeModal()" class="bc-modal-shell w-full max-w-4xl">

                <!-- Header -->
                <div class="bc-modal-head flex items-center justify-between p-4 border-b">
                    <h3 class="bc-modal-title">Pilih Produk</h3>
                    <button type="button" @click="closeModal()" class="bc-muted hover:opacity-80 text-lg">✕</button>
                </div>

                <!-- Search -->
                <div class="bc-modal-search p-4 border-b">
                    <input type="text" x-model="modalSearch"
                        placeholder="Cari produk berdasarkan nama, merek, atau kategori..."
                        class="w-full px-3 py-2 text-sm outline-none" />
                </div>

                <!-- Product List -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="p in availableProducts" :key="p.id">
                        <div @click="selectProduct(p)"
                            class="bc-row flex items-center justify-between px-4 py-3 cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="bc-avatar" style="width:36px;height:36px;font-size:12px;">
                                    <span x-text="getInitials(p.name)"></span>
                                </div>
                                <div>
                                    <div class="bc-name">
                                        <span x-text="p.name"></span>
                                        <span x-show="p.variant" class="bc-muted ml-1">(<span
                                                x-text="p.variant"></span>)</span>
                                    </div>
                                    <div class="bc-muted" x-text="'Barcode: ' + (p.barcode ?? '-')"></div>
                                </div>
                            </div>
                            <div class="bc-muted">Stok: <span x-text="p.stock"></span></div>
                        </div>
                    </template>

                    <div x-show="!availableProducts.length" class="bc-empty">
                        Tidak ada produk ditemukan.
                    </div>
                </div>

                <!-- Footer -->
                <div class="bc-modal-foot flex items-center justify-end p-4 border-t">
                    <button type="button" @click="closeModal()" class="bc-btn-muted">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <div x-show="showWarning" x-transition.opacity.duration.400ms
            class="fixed inset-0 z-[999] flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div x-transition.scale.duration.300ms class="bc-warn px-6 py-5 max-w-sm text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-500 mx-auto mb-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-.01-9a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
                <p class="text-sm leading-relaxed" x-text="warningMessage"></p>
                <div class="mt-4 flex justify-center">
                    <button type="button" @click="showWarning = false" class="bc-btn-muted text-xs px-4 py-1.5">
                        Tutup
                    </button>
                </div>
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
                hasInitialized: false,
                draftKey: 'barcodePrintDraft',

                init() {
                    // Restore keranjang dari localStorage perangkat
                    this.loadDraft();

                    // Autosave setiap perubahan cart (tetap di perangkat)
                    this.$watch('cart', () => {
                        if (!this.hasInitialized) return;
                        this.saveDraft();
                    }, { deep: true });

                    this.$nextTick(() => {
                        this.hasInitialized = true;
                    });
                },

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
                    this.cart = [];
                    this.clearDraft();
                    this.showPopup('Keranjang dikosongkan.');
                },

                showPopup(msg) {
                    this.warningMessage = msg;
                    this.showWarning = true;
                    setTimeout(() => this.showWarning = false, 3500);
                },

                saveDraft() {
                    try {
                        localStorage.setItem(this.draftKey, JSON.stringify({
                            cart: this.cart
                        }));
                    } catch (e) {
                        console.error('Gagal menyimpan draft barcode', e);
                    }
                },

                loadDraft() {
                    try {
                        const raw = localStorage.getItem(this.draftKey);
                        if (!raw) return;
                        const data = JSON.parse(raw);
                        if (Array.isArray(data.cart)) {
                            this.cart = data.cart;
                        }
                    } catch (e) {
                        console.error('Draft barcode rusak, diabaikan');
                        this.clearDraft();
                    }
                },

                clearDraft() {
                    try {
                        localStorage.removeItem(this.draftKey);
                    } catch (e) {}
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
        }
        .price {
            font-size: 5.5px;
            font-weight: 600;
            line-height: 1;
            margin-top: 1px;
        }
    <\/style>
<\/head>
<body>
    <div class="sheet" id="barcode-container"><\/div>
<\/body>
<\/html>
                    `);
                    w.document.close();

                    w.onload = () => {
                        // 🔹 Render barcode setelah window siap
                        setTimeout(() => {
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
                                    height: 18,
                                    displayValue: false
                                });
                            });

                            // print otomatis setelah render
                            setTimeout(() => w.print(), 600);
                        }, 50);
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
                    // pastikan draft tersimpan sebelum buka print
                    this.saveDraft();

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

@extends('layouts.app')

@section('content')
    <style>
        .cust-hero {
            background: #FFFFFF;
            color: #1D1D1F;
            padding: 20px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        html.dark .cust-hero {
            background: #1C1C1E;
            color: #F5F5F7;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45);
        }

        .cust-hero-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #1D1D1F;
        }

        html.dark .cust-hero-title {
            color: #F5F5F7;
        }

        .cust-hero-icon {
            color: #007AFF;
        }

        html.dark .cust-hero-icon {
            color: #0A84FF;
        }

        .cust-hero-sub {
            margin-top: 4px;
            font-size: 0.875rem;
            color: #6E6E73;
        }

        html.dark .cust-hero-sub {
            color: #AEAEB2;
        }

        .copy-chip {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            width: 32px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: rgba(120, 120, 128, 0.12);
            color: #8E8E93;
            flex-shrink: 0;
            z-index: 2;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.28s cubic-bezier(0.2, 0.9, 0.3, 1);
        }

        html.dark .copy-chip {
            background: rgba(120, 120, 128, 0.28);
            color: #AEAEB2;
        }

        .copy-chip--card {
            position: absolute;
            top: 14px;
            right: 14px;
        }

        .copy-chip:hover {
            background: rgba(120, 120, 128, 0.2);
        }

        html.dark .copy-chip:hover {
            background: rgba(120, 120, 128, 0.38);
        }

        .copy-chip:active {
            transform: scale(0.86);
        }

        .copy-chip.is-copied {
            background: rgba(0, 122, 255, 0.12);
            color: #007AFF;
            animation: iosCopyPop 0.46s cubic-bezier(0.2, 0.9, 0.3, 1);
        }

        html.dark .copy-chip.is-copied {
            background: rgba(10, 132, 255, 0.22);
            color: #0A84FF;
        }

        .copy-chip-inner,
        .copy-icons {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
        }

        .copy-icons svg {
            position: absolute;
            inset: 0;
            width: 16px;
            height: 16px;
        }

        .icon-copy {
            transition: opacity 0.16s ease, transform 0.16s ease;
        }

        .icon-check {
            opacity: 0;
            transform: scale(0.6);
        }

        .copy-chip.is-copied .icon-copy {
            opacity: 0;
            transform: scale(0.6);
        }

        .copy-chip.is-copied .icon-check {
            opacity: 1;
            animation: iosCheckIn 0.42s cubic-bezier(0.2, 0.9, 0.2, 1.05) forwards;
        }

        @keyframes iosCopyPop {
            0% { transform: scale(0.86); }
            55% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        @keyframes iosCheckIn {
            0% { opacity: 0; transform: scale(0.55); }
            70% { opacity: 1; transform: scale(1.08); }
            100% { opacity: 1; transform: scale(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            .copy-chip,
            .copy-chip.is-copied,
            .copy-chip.is-copied .icon-check {
                animation: none;
                transition: none;
            }
        }
    </style>

    <div x-data="customerPage()" class="p-6 space-y-6 relative">

        <!-- HEADER -->
        <div class="cust-hero page-enter" style="--d:0">
            <div>
                <h1 class="cust-hero-title">
                    <x-heroicon-o-users class="w-6 h-6 cust-hero-icon" />
                    Pelanggan
                </h1>
                <p class="cust-hero-sub">Total: <span x-text="customers.length"></span> Pelanggan</p>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <div
            class="page-enter flex items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition duration-300"
            style="--d:80">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            <input type="text" x-model="search" placeholder="Cari customer berdasarkan nama..."
                class="w-full bg-transparent focus:outline-none text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 transition-all duration-300 focus:ring-0">
        </div>

        <!-- CUSTOMER GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
            <template x-for="(item, index) in filteredCustomers" :key="item.id">
                <div @click="openCustomer(item)"
                    class="page-enter-item group relative bg-gray-800 border border-gray-700 rounded-xl shadow-md hover:shadow-blue-900/30 hover:border-blue-500 hover:-translate-y-1 transition-all duration-300 cursor-pointer p-5 flex flex-col items-start justify-between"
                    :style="'--i:' + index">
                    <button type="button" @click.stop="copyCustomer(item)"
                        class="copy-chip copy-chip--card"
                        :class="{ 'is-copied': copiedId === item.id }"
                        :aria-label="'Salin utang ' + item.name + ' untuk WhatsApp'"
                        title="Salin untuk WhatsApp">
                        <span class="copy-chip-inner">
                            <span class="copy-icons" aria-hidden="true">
                                <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="11" height="11" rx="2"></rect>
                                    <path d="M5 15V5a2 2 0 0 1 2-2h10"></path>
                                </svg>
                                <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                                    <path class="check-path" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                        </span>
                    </button>
                    <div class="flex items-center gap-3 mb-3 w-full pr-10">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold shadow-md">
                            <span x-text="item.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-base font-semibold text-gray-100 group-hover:text-blue-400 transition truncate"
                                x-text="item.name"></h2>
                            <p class="text-xs text-gray-400 italic" x-text="item.attributes.length + ' nomor'"></p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center w-full mt-auto">
                        <p class="text-xs text-gray-400">Utang:</p>
                        <p class="text-sm font-semibold"
                            :class="item.debts.length > 0 ? 'text-yellow-400' : 'text-gray-400'"
                            x-text="item.debts.length > 0 
                            ? 'Rp ' + Number(item.debts.reduce((sum, d) => sum + Number(d.subtotal || 0), 0)).toLocaleString('id-ID') 
                            : 'Lunas'">
                        </p>
                    </div>
                </div>
            </template>
        </div>

        <!-- ❗ Pesan jika hasil pencarian kosong -->
        <template x-if="search && filteredCustomers.length === 0">
            <div class="text-center text-gray-500 dark:text-gray-400 py-10 italic">
                Pelanggan dengan nama "<span class="font-semibold" x-text="search"></span>" tidak ditemukan.
            </div>
        </template>

        <!-- 🟢 Modal Detail Customer -->
        <div x-show="selectedCustomer" x-transition.opacity.duration.300ms
            class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-3">

            <div @click.outside="if (!showPayConfirm) selectedCustomer = null" x-transition.scale.duration.300ms
                class="bg-gray-900 text-gray-100 rounded-2xl shadow-2xl p-6 w-full max-w-3xl border border-gray-700 relative
                max-h-[80vh] overflow-y-auto">

                <button @click="selectedCustomer = null"
                    class="absolute top-3 right-3 text-gray-400 hover:text-white transition">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold shadow-md">
                        <span x-text="selectedCustomer.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-blue-400" x-text="selectedCustomer.name"></h2>
                        <p class="text-gray-400 text-sm" x-text="selectedCustomer.attributes.length + ' nomor terdaftar'">
                        </p>
                    </div>
                    <button type="button" @click.stop="copyCustomer(selectedCustomer)"
                        class="copy-chip ml-auto mr-8"
                        :class="{ 'is-copied': selectedCustomer && copiedId === selectedCustomer.id }"
                        aria-label="Salin utang untuk WhatsApp"
                        title="Salin untuk WhatsApp">
                        <span class="copy-chip-inner">
                            <span class="copy-icons" aria-hidden="true">
                                <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="11" height="11" rx="2"></rect>
                                    <path d="M5 15V5a2 2 0 0 1 2-2h10"></path>
                                </svg>
                                <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                                    <path class="check-path" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                        </span>
                    </button>
                </div>

                <!-- Detail Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Nomor Pelanggan -->
                    <div>
                        <h3 class="text-sm font-semibold mb-2 text-gray-300 uppercase tracking-wide">Nomor Pelanggan</h3>
                        <template x-if="selectedCustomer.attributes.length > 0">
                            <div class="space-y-2">
                                <template x-for="attr in selectedCustomer.attributes" :key="attr.id">
                                    <div
                                        class="bg-gray-800 border border-gray-700 rounded-lg p-3 hover:border-blue-500 transition">
                                        <p class="text-sm font-semibold text-blue-400" x-text="attr.attribute_value"></p>
                                        <p class="text-xs text-gray-400 italic mt-1" x-text="attr.attribute_notes || '—'">
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="selectedCustomer.attributes.length === 0">
                            <p class="text-gray-500 italic text-sm">Tidak ada nomor pelanggan.</p>
                        </template>
                    </div>

                    <!-- Utang Pelanggan -->
                    <div>
                        <h3 class="text-sm font-semibold mb-2 text-gray-300 uppercase tracking-wide">Utang Pelanggan</h3>
                        <template x-if="selectedCustomer.debts.length > 0">
                            <div class="space-y-2">
                                <template x-for="debt in selectedCustomer.debts" :key="debt.type + '-' + debt.id">
                                    <div
                                        class="bg-gray-800 border border-gray-700 rounded-lg p-3 hover:border-yellow-500 transition">
                                        <div class="flex justify-between text-sm">
                                            <p class="font-semibold text-yellow-400">Nota: <span
                                                    x-text="debt.nomor_nota"></span></p>
                                            <p class="text-gray-400 text-xs" x-text="formatLongDate(debt.created_at)"></p>
                                        </div>
                                        <p class="text-sm text-gray-300 mt-1">
                                            Produk: <span class="font-semibold text-gray-100"
                                                x-text="debtItemName(debt)"></span>
                                        </p>
                                        <p class="text-sm font-bold text-yellow-400 mt-1">Rp <span
                                                x-text="Number(debt.subtotal).toLocaleString('id-ID')"></span></p>

                                        <!-- Tombol Lunaskan -->
                                        <div class="flex justify-end mt-3">
                                            <button @click.stop="openPayConfirm(debt, selectedCustomer)"
                                                class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md shadow-sm transition-all duration-300 hover:shadow-lg active:scale-95 flex items-center gap-1">
                                                <x-heroicon-o-check class="w-4 h-4" /> Lunaskan
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="selectedCustomer.debts.length === 0">
                            <p class="text-gray-500 italic text-sm">Tidak ada utang pelanggan.</p>
                        </template>
                    </div>

                </div>
            </div>
        </div>

        <!-- 🔵 Modal Konfirmasi Pelunasan -->
        <div x-show="showPayConfirm" x-transition.opacity.duration.300ms
            class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div x-show="showPayConfirm" x-transition.scale.duration.300ms
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-[90%] max-w-sm text-center">
                <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">Lunaskan Utang?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">
                    Apakah Anda yakin ingin menandai transaksi
                    <br> dengan nomor nota:
                    <span class="font-semibold text-green-600 dark:text-green-400"
                        x-text="debtToPay?.nomor_nota || '-'"></span>
                    <br> sebagai <strong>lunas</strong>?
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="showPayConfirm = false"
                        class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button @click="confirmPayDebt()" :disabled="isPaying"
                        :class="isPaying ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 active:scale-[0.97] transition shadow-md">

                        <span x-text="isPaying ? 'Memproses...' : 'Lunaskan'"></span>

                    </button>
                </div>
            </div>
        </div>

        <!-- ✅ Toast Notification -->
        <div x-show="toast.show" x-transition.opacity.duration.500ms class="fixed bottom-5 right-5 z-[1000]">
            <div class="bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
                <x-heroicon-o-check-circle class="w-5 h-5 text-white" />
                <span x-text="toast.message"></span>
            </div>
        </div>

    </div>

    <script>
        function customerPage() {
            return {
                customers: @json($customers ?? []),
                search: '',
                selectedCustomer: null,
                showPayConfirm: false,
                debtToPay: null,
                customerOfDebt: null,
                toast: {
                    show: false,
                    message: ''
                },
                isPaying: false,
                copiedId: null,
                copyTimer: null,

                get filteredCustomers() {
                    if (!this.search) return this.customers;
                    return this.customers.filter(c =>
                        c.name.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                openCustomer(cust) {
                    this.selectedCustomer = cust;
                },

                formatRupiah(amount) {
                    return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
                },

                shortName(name) {
                    const clean = String(name || '').replace(/\s+/g, ' ').trim();
                    if (clean.length <= 24) return clean;
                    const cut = clean.slice(0, 24);
                    const lastSpace = cut.lastIndexOf(' ');
                    const base = lastSpace > 12 ? cut.slice(0, lastSpace) : cut;
                    return base.trim() + '…';
                },

                debtItemName(debt) {
                    const names = (debt.details || [])
                        .map(detail => detail.product?.name)
                        .filter(Boolean);
                    if (names.length) return names.join(', ');

                    const digital = [debt.brand?.name, debt.product?.name].filter(Boolean).join(' ');
                    if (digital) return digital;

                    return '—';
                },

                buildDebtMessage(item) {
                    const debts = item.debts || [];
                    if (!debts.length) return `*${item.name}*\nLunas`;

                    const lines = [`*${item.name}*`];
                    debts.forEach((debt) => {
                        lines.push(`${this.shortName(this.debtItemName(debt))} — ${this.formatRupiah(debt.subtotal)}`);
                    });

                    const total = debts.reduce((sum, debt) => sum + Number(debt.subtotal || 0), 0);
                    lines.push(`*Total ${this.formatRupiah(total)}*`);
                    return lines.join('\n');
                },

                copyText(text) {
                    const fallback = () => {
                        const area = document.createElement('textarea');
                        area.value = text;
                        area.setAttribute('readonly', '');
                        area.style.position = 'fixed';
                        area.style.left = '-9999px';
                        document.body.appendChild(area);
                        area.select();
                        const ok = document.execCommand('copy');
                        document.body.removeChild(area);
                        if (!ok) throw new Error('copy failed');
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        return navigator.clipboard.writeText(text).catch(() => fallback());
                    }

                    fallback();
                    return Promise.resolve();
                },

                copyCustomer(item) {
                    this.copyText(this.buildDebtMessage(item))
                        .then(() => {
                            this.copiedId = item.id;
                            clearTimeout(this.copyTimer);
                            this.copyTimer = setTimeout(() => {
                                if (this.copiedId === item.id) this.copiedId = null;
                            }, 1600);
                        })
                        .catch(() => {
                            this.showToast('Gagal menyalin teks');
                        });
                },

                openPayConfirm(debt, customer) {
                    this.debtToPay = debt;
                    this.customerOfDebt = customer;
                    this.showPayConfirm = true;
                },

                confirmPayDebt() {
                    if (!this.debtToPay) return;

                    this.isPaying = true;

                    const debtId = this.debtToPay.id;
                    const customer = this.customerOfDebt;

                    fetch(`/customers/pay-debt/${debtId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                type: this.debtToPay.type
                            })
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal memperbarui status');

                            customer.debts = customer.debts.filter(d =>
                                !(d.id === debtId && d.type === this.debtToPay.type)
                            );

                            this.showToast('Utang berhasil dilunaskan ✅');

                            // ⏳ delay dikit biar user lihat feedback
                            setTimeout(() => {
                                this.showPayConfirm = false;
                                this.debtToPay = null;
                                this.customerOfDebt = null;
                            }, 800);
                        })
                        .catch(err => {
                            alert('Terjadi kesalahan: ' + err.message);
                        })
                        .finally(() => {
                            this.isPaying = false;
                        });
                },

                formatLongDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => this.toast.show = false, 3000);
                }
            }
        }
    </script>
@endsection

@extends('layouts.app')

@section('content')
<div x-data="customerPage()" class="p-6 space-y-6 relative">

    <!-- HEADER -->
    <div x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-gradient-to-r from-gray-800 via-slate-800 to-gray-900 
                text-white p-5 rounded-lg flex items-center justify-between 
                shadow shadow-blue-900/30 border border-gray-700/60">
        <div>
            <h1 class="text-2xl font-semibold flex items-center gap-2">
                <x-heroicon-o-users class="w-6 h-6 text-gray-300" />
                Pelanggan
            </h1>
            <p class="text-sm text-gray-300">Total: <span x-text="customers.length"></span> Pelanggan</p>
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="flex items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition duration-300">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        <input
            type="text"
            x-model="search"
            placeholder="Cari customer berdasarkan nama..."
            class="w-full bg-transparent focus:outline-none text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 transition-all duration-300 focus:ring-0 focus:translate-x-1"
        >
    </div>

    <!-- CUSTOMER LIST -->
    <div class="space-y-3">
        <template x-for="(item, index) in filteredCustomers" :key="item.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4"
                 x-data="{ open: false }">

                <!-- HEADER CARD -->
                <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 transition-colors duration-300"
                            :class="open ? 'text-blue-400' : ''"
                            x-text="item.name"></h2>
                    </div>
                    <button class="text-gray-500 hover:text-blue-500 transition-transform duration-300"
                            :class="open ? 'rotate-180' : ''">
                        <x-heroicon-o-chevron-down class="w-5 h-5" />
                    </button>
                </div>

                <!-- DROPDOWN CONTENT -->
                <div x-show="open" x-collapse
                     x-transition:enter="transition ease-out duration-400"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- LEFT: Attributes -->
                    <div>
                        <h3 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Nomor Pelanggan</h3>
                        <template x-if="item.attributes.length > 0">
                            <div class="space-y-2">
                                <template x-for="attr in item.attributes" :key="attr.id">
                                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg p-3 transition duration-300 hover:border-blue-400">
                                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400" x-text="attr.attribute_value"></p>
                                        <p class="text-xs text-gray-500 italic mt-1" x-text="attr.attribute_notes || '—'"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="item.attributes.length === 0">
                            <p class="text-gray-400 italic text-sm">Tidak ada atribut.</p>
                        </template>
                    </div>

                    <!-- RIGHT: Debts -->
                    <div>
                        <h3 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Utang Pelanggan</h3>

                        <template x-if="item.debts.length > 0">
                            <div class="space-y-2">
                                <template x-for="debt in item.debts" :key="debt.id">
                                    <div class="bg-red-50 dark:bg-gray-900 border border-red-200 dark:border-gray-700 rounded-lg p-3 hover:scale-[1.02] transition-all duration-300 relative">
                                        <div class="flex justify-between items-center text-sm">
                                            <p class="font-semibold text-red-600 dark:text-red-400">
                                                Nota: <span x-text="debt.nomor_nota"></span>
                                            </p>
                                            <p class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                                <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                                                <span x-text="formatLongDate(debt.created_at)"></span>
                                            </p>
                                        </div>

                                        <!-- Produk + Total -->
                                        <div class="flex justify-between items-center mt-2 text-xs sm:text-sm">
                                            <div class="text-gray-700 dark:text-gray-300">
                                                <template x-if="debt.details && debt.details.length > 0">
                                                    <template x-for="d in debt.details" :key="d.id">
                                                        <p>Produk: <span class="font-semibold" x-text="d.product?.name || 'Produk tidak diketahui'"></span></p>
                                                    </template>
                                                </template>
                                                <template x-if="debt.product">
                                                    <p>
                                                        Produk:
                                                        <span class="font-semibold" x-text="debt.product?.category?.name || '—'"></span>
                                                        — <span x-text="debt.product?.name || ''"></span>
                                                    </p>
                                                </template>
                                            </div>
                                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                                Rp <span x-text="Number(debt.subtotal).toLocaleString('id-ID')"></span>
                                            </p>
                                        </div>

                                        <!-- Tombol Lunaskan -->
                                        <div class="flex justify-end mt-3">
                                            <button
                                                @click="openPayConfirm(debt, item)"
                                                class="text-xs sm:text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md shadow-sm 
                                                    transition-all duration-300 hover:shadow-lg active:scale-95 flex items-center gap-1">
                                                <x-heroicon-o-check class="w-4 h-4" /> Lunaskan
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- 💰 TOTAL CARD -->
                                <div class="bg-white dark:bg-gray-900 border border-yellow-300 dark:border-yellow-700 rounded-lg p-4 mt-3 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                                    <div class="flex items-center gap-2 text-yellow-700 dark:text-yellow-300">
                                        <x-heroicon-o-currency-dollar class="w-5 h-5" />
                                        <div>
                                            <p class="text-xs font-semibold uppercase">Total Utang :</p>
                                            <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">
                                                Rp <span 
                                                    x-text="Number(item.debts.reduce((sum, d) => sum + Number(d.subtotal || 0), 0)).toLocaleString('id-ID')">
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="item.debts.length === 0">
                            <p class="text-gray-400 italic text-sm">Tidak ada utang.</p>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- ❗ Pesan jika hasil pencarian kosong -->
        <template x-if="search && filteredCustomers.length === 0">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="text-center text-gray-500 dark:text-gray-400 py-10 italic">
                Pelanggan dengan nama "<span class="font-semibold" x-text="search"></span>" tidak ditemukan.
            </div>
        </template>
    </div>

    <!-- 🟢 Modal Konfirmasi Pelunasan -->
    <div x-show="showPayConfirm" x-transition.opacity.duration.300ms
        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div x-show="showPayConfirm" x-transition.scale.duration.300ms
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-[90%] max-w-sm text-center">
            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">Lunaskan Utang?</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">
                Apakah Anda yakin ingin menandai transaksi
                <br> dengan nomor nota:
                <span class="font-semibold text-green-600 dark:text-green-400" x-text="debtToPay?.nomor_nota || '-'"></span>
                <br> sebagai <strong>lunas</strong>?
            </p>
            <div class="flex justify-center gap-3">
                <button @click="showPayConfirm = false"
                    class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100
                        hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Batal
                </button>
                <button @click="confirmPayDebt()"
                    class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 active:scale-[0.97]
                        transition shadow-md">
                    Lunaskan
                </button>
            </div>
        </div>
    </div>

    <!-- ✅ Toast Notification -->
    <div x-show="toast.show"
         x-transition.opacity.duration.500ms
         class="fixed bottom-5 right-5 z-[1000]">
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
        showPayConfirm: false,
        debtToPay: null,
        customerOfDebt: null,
        toast: { show: false, message: '' },

        async fetchData() {
            try {
                const response = await fetch('{{ route('customer') }}', { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                this.customers = data.customers;
            } catch (err) {
                console.error('Failed to load customers:', err);
            }
        },

        get filteredCustomers() {
            if (!this.search) return this.customers;
            return this.customers.filter(c =>
                c.name.toLowerCase().includes(this.search.toLowerCase())
            );
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

        openPayConfirm(debt, customer) {
            this.debtToPay = debt;
            this.customerOfDebt = customer;
            this.showPayConfirm = true;
        },

        confirmPayDebt() {
            if (!this.debtToPay) return;
            const debtId = this.debtToPay.id;
            const customer = this.customerOfDebt;

            fetch(`/customers/pay-debt/${debtId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal memperbarui status');
                customer.debts = customer.debts.filter(d => d.id !== debtId);
                this.showPayConfirm = false;
                this.debtToPay = null;
                this.customerOfDebt = null;
                // ✅ Toast muncul
                this.showToast('Utang berhasil dilunaskan ✅');
            })
            .catch(err => alert('Terjadi kesalahan: ' + err.message));
        },

        showToast(msg) {
            this.toast.message = msg;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },
    }
}
</script>
@endsection

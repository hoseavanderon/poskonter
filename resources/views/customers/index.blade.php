@extends('layouts.app')

@section('content')
    <div x-data="customerPage()" class="p-6 space-y-6 relative">

        <!-- HEADER -->
        <div
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
        <div
            class="flex items-center gap-2 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition duration-300">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            <input type="text" x-model="search" placeholder="Cari customer berdasarkan nama..."
                class="w-full bg-transparent focus:outline-none text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 transition-all duration-300 focus:ring-0">
        </div>

        <!-- CUSTOMER GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-5">
            <template x-for="(item, index) in filteredCustomers" :key="item.id">
                <div @click="openCustomer(item)"
                    class="group bg-gray-800 border border-gray-700 rounded-xl shadow-md hover:shadow-blue-900/30 hover:border-blue-500 hover:-translate-y-1 transition-all duration-300 cursor-pointer p-5 flex flex-col items-start justify-between">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold shadow-md">
                            <span x-text="item.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-100 group-hover:text-blue-400 transition"
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

            <div @click.outside="selectedCustomer = null" x-transition.scale.duration.300ms
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
                    <div>
                        <h2 class="text-2xl font-bold text-blue-400" x-text="selectedCustomer.name"></h2>
                        <p class="text-gray-400 text-sm" x-text="selectedCustomer.attributes.length + ' nomor terdaftar'">
                        </p>
                    </div>
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
                                <template x-for="debt in selectedCustomer.debts" :key="debt.id">
                                    <div
                                        class="bg-gray-800 border border-gray-700 rounded-lg p-3 hover:border-yellow-500 transition">
                                        <div class="flex justify-between text-sm">
                                            <p class="font-semibold text-yellow-400">Nota: <span
                                                    x-text="debt.nomor_nota"></span></p>
                                            <p class="text-gray-400 text-xs" x-text="formatLongDate(debt.created_at)"></p>
                                        </div>
                                        <p class="text-sm text-gray-300 mt-1">
                                            Produk: <span class="font-semibold text-gray-100"
                                                x-text="debt.details?.[0]?.product?.name || '—'"></span>
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
                    <button @click="confirmPayDebt()"
                        class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 active:scale-[0.97] transition shadow-md">
                        Lunaskan
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

                get filteredCustomers() {
                    if (!this.search) return this.customers;
                    return this.customers.filter(c =>
                        c.name.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                openCustomer(cust) {
                    this.selectedCustomer = cust;
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
                            this.showToast('Utang berhasil dilunaskan ✅');
                        })
                        .catch(err => alert('Terjadi kesalahan: ' + err.message));
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

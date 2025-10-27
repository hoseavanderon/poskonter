@extends('layouts.app')

@section('content')
<div x-data="inventoryHistory()" x-init="init()" class="p-6 max-w-4xl mx-auto">

    <!-- 🔹 Judul di Tengah -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-1">
            📦 Cek History Stok Barang
        </h2>
        <p class="text-sm text-gray-500">Scan atau ketik barcode barang untuk melihat riwayat keluar dan masuknya.</p>
    </div>

    <!-- 🔍 Form Input -->
    <div
        class="flex flex-col sm:flex-row justify-center items-center gap-3 mb-10 bg-white/50 dark:bg-gray-800/60 p-4 rounded-2xl shadow-sm backdrop-blur-md">
        
        <input type="text" x-model="barcode" placeholder="Scan / Ketik Kode / Barcode Barang"
            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-72 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-900 text-center"
            x-ref="barcodeInput" @keyup.enter="fetchData()">

        <input type="text" x-ref="rangeInput" x-model="dateRange"
            placeholder="Pilih rentang tanggal (opsional)"
            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-80 sm:w-96 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-900 text-center"
            readonly>
    </div>

    <!-- 📄 Loading -->
    <template x-if="loading">
        <div class="text-center py-10 text-gray-500 animate-pulse">Memuat data...</div>
    </template>

    <!-- 📦 Card Summary per Tanggal (1 grid) -->
    <div x-show="!loading && summaries.length > 0" class="space-y-4">
        <template x-for="(item, index) in summaries" :key="index">
            <div @click="openModal(item.date)" 
                 class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-md cursor-pointer hover:shadow-lg hover:scale-[1.01] transition border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="formatDate(item.date)"></h3>
                        <p class="text-sm text-gray-500" x-text="item.total + ' transaksi'"></p>
                    </div>
                    <div class="text-sm flex flex-col items-end">
                        <span class="text-green-600 font-medium">Masuk: <span x-text="item.in_qty"></span></span>
                        <span class="text-red-500 font-medium">Keluar: <span x-text="item.out_qty"></span></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- 🪟 Modal Detail -->
    <div x-show="showModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
         x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-3xl p-6 shadow-lg relative">
            <button @click="showModal=false"
                class="absolute top-3 right-4 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>

            <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-100">
                Detail Transaksi — <span x-text="formatDate(selectedDate)"></span>
            </h3>

            <template x-if="filteredDetails.length === 0">
                <div class="text-center text-gray-500 py-10">Tidak ada transaksi pada tanggal ini.</div>
            </template>

            <div class="space-y-3 overflow-y-auto max-h-[70vh] pr-2">
                <template x-for="(d, i) in filteredDetails" :key="i">
                    <div class="p-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/80 hover:bg-gray-100 dark:hover:bg-gray-700/80 shadow-sm transition-all">
                        <!-- Header: waktu -->
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                <x-heroicon-o-clock class="w-4 h-4 text-blue-500 dark:text-blue-400" />
                                <span class="text-xs font-medium" x-text="formatTime(d.date_time)"></span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="'#' + (i + 1)"></span>
                        </div>

                        <!-- Atribut produk -->
                        <div class="text-sm text-gray-900 dark:text-gray-100 space-y-1">
                            <template x-if="d.attribute_name && d.batch_code">
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-50" x-text="d.attribute_name + ' : '"></span>
                                    <span class="text-gray-700 dark:text-gray-200" x-text="d.batch_code"></span>
                                </div>
                            </template>
                            <template x-if="!d.attribute_name">
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-50">Batch :</span>
                                    <span class="text-gray-700 dark:text-gray-200" x-text="d.batch_code || '-'"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Catatan -->
                        <div class="text-xs italic text-gray-600 dark:text-gray-400 mt-1" x-text="d.note"></div>

                        <!-- Jumlah in/out -->
                        <div class="flex justify-between mt-3 text-xs font-semibold">
                            <span class="text-green-600 dark:text-green-400">Masuk: <span x-text="d.in_qty || '-'"></span></span>
                            <span class="text-red-600 dark:text-red-400">Keluar: <span x-text="d.out_qty || '-'"></span></span>
                        </div>
                    </div>
                </template>
            </div>


        </div>
    </div>

    <!-- 📭 Jika belum ada data -->
    <template x-if="!loading && !barcode">
        <div class="text-center py-10 text-gray-400">
            Silakan <b>scan</b> atau <b>ketik kode barang</b> untuk melihat history stok.
        </div>
    </template>

    <template x-if="!loading && barcode && summaries.length === 0">
        <div class="text-center py-10 text-gray-400">Tidak ada data dalam rentang tanggal ini.</div>
    </template>
</div>

<!-- ⚙️ Script -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function inventoryHistory() {
    return {
        barcode: '',
        dateRange: '',
        rangeText: 'Hari ini',
        loading: false,
        details: [],
        summaries: [],
        showModal: false,
        selectedDate: '',
        filteredDetails: [],

        init() {
            this.$nextTick(() => this.$refs.barcodeInput.focus());
            flatpickr(this.$refs.rangeInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'id',
                altInput: true,
                altFormat: 'j F Y',
                onChange: (dates, str) => {
                    this.dateRange = str;
                    if (this.barcode) this.fetchData();
                }
            });
        },

        async fetchData() {
            if (!this.barcode) return;
            this.loading = true;
            try {
                const res = await fetch(`/history-inventory/data?barcode=${this.barcode}&range=${this.dateRange}`);
                const data = await res.json();
                this.details = data.details ?? [];

                // 🔹 Group by tanggal
                const grouped = {};
                this.details.forEach(d => {
                    if (!grouped[d.date]) grouped[d.date] = { in_qty: 0, out_qty: 0, total: 0, date: d.date };
                    grouped[d.date].in_qty += d.in_qty ? parseInt(d.in_qty) : 0;
                    grouped[d.date].out_qty += d.out_qty ? parseInt(d.out_qty) : 0;
                    grouped[d.date].total++;
                });
                this.summaries = Object.values(grouped).sort((a, b) => b.date.localeCompare(a.date));

            } catch (err) {
                console.error(err);
                alert('Gagal mengambil data.');
            }
            this.loading = false;
        },

        openModal(date) {
            this.selectedDate = date;
            this.filteredDetails = this.details.filter(d => d.date === date);
            this.showModal = true;
        },

        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        },

        formatTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            });
        },
    }
}
</script>
@endsection

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
@endpush

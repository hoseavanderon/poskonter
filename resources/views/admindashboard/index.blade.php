@extends('layouts.admin')

@section('content')
    <!-- 🏠 HOME -->
    <div x-show="page === 'home'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Home</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Overview All Outlet + Grafik + Live Activity
        </div>
    </div>

    <!-- 🛒 PRODUK -->
    <div x-show="page === 'product'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Produk</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Produk Terlaris + Stok Hampir Habis
        </div>
    </div>

    <!-- 📊 INSIGHT -->
    <div x-show="page === 'insight'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Insight</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Jam Rame + Hari Terlaris + Bulan Terbaik
        </div>
    </div>

    <!-- ⚙️ SETTINGS -->
    <div x-show="page === 'settings'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Menu</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Settings / Config / Master Data
        </div>
    </div>
@endsection

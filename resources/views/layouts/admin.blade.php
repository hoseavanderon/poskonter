<div class="p-6">
    <h1 class="text-2xl font-bold mb-4 text-white">Halaman Admin</h1>

    <!-- Tombol Logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
            🚪 Logout
        </button>
    </form>
</div>
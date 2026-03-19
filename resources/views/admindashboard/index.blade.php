@extends('layouts.admin')

@section('content')
    <!-- DASHBOARD -->
    <div x-show="page === 'dashboard'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Dashboard</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Dashboard content
        </div>
    </div>

    <!-- ANALYTICS -->
    <div x-show="page === 'analytics'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Analytics</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Analytics content
        </div>
    </div>

    <!-- OUTLET -->
    <div x-show="page === 'outlet'" x-transition.opacity.duration.300ms>
        <h1 class="text-xl font-bold mb-4">Outlet Detail</h1>

        <div class="bg-gray-800 p-4 rounded-xl">
            Outlet content
        </div>
    </div>
@endsection

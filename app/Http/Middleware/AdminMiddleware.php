<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil user dari guard Jetstream (sanctum)
        $user = Auth::guard(config('jetstream.guard', 'sanctum'))->user();

        // Jika belum login
        if (! $user) {
            return redirect()->route('login');
        }

        // Jika bukan admin
        if ($user->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}

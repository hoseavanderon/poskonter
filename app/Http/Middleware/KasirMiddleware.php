<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard(config('jetstream.guard', 'sanctum'))->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'kasir') {
            abort(403);
        }

        return $next($request);
    }
}

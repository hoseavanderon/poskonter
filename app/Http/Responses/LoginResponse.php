<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $target = ($user && $user->isOwner())
            ? route('admin-pos')
            : route('pos');

        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 200);
        }

        if ($user && $user->isOwner()) {
            return redirect()->route('admin-pos');
        }

        return redirect()->intended($target);
    }
}

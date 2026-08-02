<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->status) {
                auth()->logout();
                $request->session()->invalidate();
                return redirect()->route('login')
                    ->withErrors(['status' => 'Akun Anda telah dinonaktifkan.']);
            }

            if ($user->isSiswa() && $user->needsPasswordChange()) {
                $routeName = $request->route()?->getName();
                $allowed = ['profile', 'profile.update', 'profile.password', 'logout'];
                if (!in_array($routeName, $allowed, true)) {
                    return redirect()->route('profile')
                        ->with('warning', 'Demi keamanan, Anda wajib mengubah password bawaan (Tanggal Lahir) sebelum mengakses menu LMS.');
                }
            }
        }
        return $next($request);

    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->status) {
            auth()->logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->withErrors(['status' => 'Akun Anda telah dinonaktifkan.']);
        }
        return $next($request);
    }
}
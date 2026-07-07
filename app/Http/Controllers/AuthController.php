<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $result = $this->authService->attemptLogin($request);

        return redirect()->intended($result['redirect']);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('login')->with('success', 'Berhasil keluar dari sistem.');
    }
}

<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function redirectToCbt()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $now = time();
        $payload = [
            'iat' => $now,
            'exp' => $now + 60, // Token valid for 60 seconds
            'uid' => $user->username,
            'nama' => $user->name,
            'role' => match ($user->role?->value ?? (string) $user->role) {
                'superadmin' => 'admin',
                'admin', 'kepala_sekolah' => 'admin',
                'guru' => 'guru',
                'siswa' => 'siswa',
                default => 'pengawas',
            },
        ];

        // If the user is a student, we load the class information
        if ($user->isSiswa()) {
            $user->loadMissing('student.class');
            $payload['kelas'] = $user->student?->class?->name;
        }

        $secretKey = config('services.cbt.sync_secret_key');
        abort_unless($secretKey, 503, 'Integrasi SSO CBT belum dikonfigurasi.');
        $token = JWT::encode($payload, $secretKey, 'HS256');

        $cbtUrl = rtrim(config('services.cbt.url'), '/');
        
        // Keep the signed credential out of browser history, proxy logs, and
        // Referer headers by posting it directly to the CBT verifier.
        return response()->view('auth.sso-post-redirect', [
            'targetUrl' => $cbtUrl . '/sso/verify',
            'token' => $token,
        ])->header('Referrer-Policy', 'no-referrer');
    }
}

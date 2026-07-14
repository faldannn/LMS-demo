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
                'kepala_sekolah' => 'admin',
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

        $secretKey = env('SYNC_SECRET_KEY', 'isi_kunci_rahasia_sso_123');
        $token = JWT::encode($payload, $secretKey, 'HS256');

        $cbtUrl = rtrim(env('CBT_URL', 'http://cbt.local'), '/');
        
        return redirect($cbtUrl . '/sso/verify?token=' . $token);
    }
}

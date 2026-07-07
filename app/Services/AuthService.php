<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * @return array{user: User, redirect: string}
     */
    public function attemptLogin(Request $request): array
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string',
        ]);

        $throttleKey = $this->throttleKey($credentials['username'], $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->recordLoginAttempt(
                username: $credentials['username'],
                status: 'failed',
                failureReason: 'invalid_credentials',
                user: $user,
                request: $request,
            );

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        if (!$user->status) {
            $this->recordLoginAttempt(
                username: $credentials['username'],
                status: 'failed',
                failureReason: 'inactive_account',
                user: $user,
                request: $request,
            );

            throw ValidationException::withMessages([
                'username' => 'Akun Anda telah dinonaktifkan.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        Auth::login($user, $request->boolean('remember'));

        $user->update(['last_login' => now()]);

        $this->recordLoginAttempt(
            username: $user->username,
            status: 'success',
            user: $user,
            request: $request,
        );

        $this->activityLogService->logLogin($user, $request);

        return [
            'user' => $user,
            'redirect' => $this->resolveRedirectPath($user),
        ];
    }

    public function logout(Request $request): void
    {
        $user = Auth::user();

        $this->activityLogService->logLogout($user, $request);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function resolveRedirectPath(User $user): string
    {
        return match ($user->role) {
            UserRole::Siswa => route('dashboard'),
            UserRole::Guru => route('dashboard'),
            UserRole::KepalaSekolah => route('dashboard'),
            default => route('dashboard'),
        };
    }

    public function dashboardViewFor(User $user): string
    {
        return match ($user->role) {
            UserRole::Siswa => 'dashboard.siswa',
            UserRole::Guru => 'dashboard.guru',
            UserRole::KepalaSekolah => 'dashboard.kepala-sekolah',
            default => 'dashboard.kepala-sekolah',
        };
    }

    private function recordLoginAttempt(
        string $username,
        string $status,
        Request $request,
        ?User $user = null,
        ?string $failureReason = null,
    ): void {
        LoginLog::create([
            'user_id' => $user?->id,
            'username' => $username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $status,
            'failure_reason' => $failureReason,
            'logged_at' => now(),
        ]);
    }

    private function throttleKey(string $username, ?string $ip): string
    {
        return Str::lower($username) . '|' . ($ip ?? 'unknown');
    }
}

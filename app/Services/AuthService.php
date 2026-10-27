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

        $loginInput = $credentials['username'];
        $user = User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->orWhereHas('teacher', function($query) use ($loginInput) {
                $query->where('nip', $loginInput);
            })
            ->orWhereHas('student', function($query) use ($loginInput) {
                $query->where('nisn', $loginInput)->orWhere('nis', $loginInput);
            })
            ->first();

        $passwordAttempts = [$credentials['password']];
        $rawPass = trim($credentials['password']);
        if (preg_match('/^(\d{2})[\-\/](\d{2})[\-\/](\d{4})$/', $rawPass, $m)) {
            $passwordAttempts[] = "{$m[3]}-{$m[2]}-{$m[1]}";
        } elseif (preg_match('/^(\d{4})[\-\/](\d{2})[\-\/](\d{2})$/', $rawPass, $m)) {
            $passwordAttempts[] = "{$m[1]}-{$m[2]}-{$m[3]}";
        } elseif (preg_match('/^(\d{2})(\d{2})(\d{4})$/', $rawPass, $m)) {
            $passwordAttempts[] = "{$m[3]}-{$m[2]}-{$m[1]}";
        } elseif (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $rawPass, $m)) {
            $passwordAttempts[] = "{$m[1]}-{$m[2]}-{$m[3]}";
        }
        $passwordAttempts = array_unique($passwordAttempts);

        $passwordCorrect = false;
        if ($user) {
            foreach ($passwordAttempts as $attempt) {
                if (Hash::check($attempt, $user->password)) {
                    $passwordCorrect = true;
                    break;
                }
            }
        }

        if (!$user || !$passwordCorrect) {
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

        if ($user) {
            $this->activityLogService->logLogout($user, $request);
        }

        Auth::logout();

        try {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $e) {
            // Ignore session invalidation errors
        }
    }

    public function resolveRedirectPath(User $user): string
    {
        return match ($user->role) {
            UserRole::Siswa => route('dashboard'),
            UserRole::Guru => route('dashboard'),
            UserRole::Admin, UserRole::SuperAdmin => route('dashboard'),
            default => route('dashboard'),
        };
    }

    public function dashboardViewFor(User $user): string
    {
        return match ($user->role) {
            UserRole::Siswa => 'dashboard.siswa',
            UserRole::Guru => 'dashboard.guru',
            UserRole::Admin, UserRole::SuperAdmin => 'dashboard.kepala-sekolah',
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

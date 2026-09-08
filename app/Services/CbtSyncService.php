<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CbtSyncService
{
    /**
     * Synchronize all master data (subjects, teachers, students, admins) to CBT system.
     *
     * @return array
     */
    public function syncAll(): array
    {
        $apiUrl = config('services.cbt.api_url');
        $secretKey = config('services.cbt.sync_secret_key');

        if (!$apiUrl || !$secretKey) {
            return [
                'success' => false,
                'message' => 'CBT_API_URL atau SYNC_SECRET_KEY belum dikonfigurasi di file .env LMS.',
                'stats' => ['subjects' => 0, 'teachers' => 0, 'students' => 0, 'users' => 0, 'errors' => []]
            ];
        }

        $apiUrl = rtrim($apiUrl, '/');

        $batchPayload = [
            'subjects' => Subject::query()->get()->map(fn ($subject) => [
                'id' => $subject->id, 'name' => $subject->name, 'code' => $subject->code, 'level' => $subject->grade,
            ])->all(),
            'teachers' => Teacher::with('user')->get()->map(function ($teacher) {
                $user = $teacher->user;
                return [
                    'username' => $user?->username ?? $teacher->email, 'name' => $teacher->name, 'role' => 'guru',
                    'tanggal_lahir' => $teacher->dob?->format('Y-m-d'),
                    'subjects' => $teacher->subject ? array_values(array_filter(array_map('trim', explode(',', $teacher->subject)))) : [],
                    'email' => $teacher->email ?? $user?->email, 'password' => $user?->password,
                ];
            })->filter(fn ($teacher) => !empty($teacher['username']))->values()->all(),
            'students' => Student::with(['class', 'user'])->get()->map(fn ($student) => [
                'nis' => $student->nis, 'nama' => $student->name, 'kelas' => $student->class?->name ?? 'Umum',
                'ruangan' => $student->class?->room, 'tanggal_lahir' => $student->dob?->format('Y-m-d'), 'password' => $student->user?->password,
            ])->all(),
            'admins' => User::whereNotIn('role', ['siswa', 'guru'])->get()->map(function ($user) {
                $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;
                return ['username' => $user->username, 'name' => $user->name, 'role' => $role === 'superadmin' ? 'admin' : 'admin', 'email' => $user->email, 'subjects' => [], 'password' => $user->password];
            })->all(),
        ];

        try {
            $response = Http::connectTimeout(5)->timeout(45)->retry(3, 1500, throw: false)
                ->withHeaders(['X-Sync-Secret' => $secretKey])
                ->post("{$apiUrl}/sync/batch", $batchPayload);
            if ($response->successful()) {
                $counts = $response->json('counts', []);
                return ['success' => true, 'message' => "Sinkronisasi batch CBT berhasil: {$counts['subjects']} mapel, {$counts['teachers']} guru, {$counts['students']} siswa, {$counts['admins']} admin/staf.", 'stats' => $counts];
            }
            return ['success' => false, 'message' => 'Sinkronisasi batch CBT gagal: HTTP ' . $response->status(), 'stats' => []];
        } catch (\Throwable $e) {
            Log::error('CBT batch sync failed', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Sinkronisasi batch CBT gagal: ' . $e->getMessage(), 'stats' => []];
        }

        $stats = [
            'subjects' => 0,
            'teachers' => 0,
            'students' => 0,
            'users' => 0,
            'errors' => []
        ];

        // 1. Sync Subjects
        $subjects = Subject::all();
        foreach ($subjects as $subject) {
            $payload = [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'level' => $subject->grade,
            ];
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post("{$apiUrl}/sync/subject", $payload);

                if ($response->successful()) {
                    $stats['subjects']++;
                } else {
                    $stats['errors'][] = "Matapelajaran '{$subject->name}': " . $response->status() . " " . $response->body();
                }
            } catch (\Exception $e) {
                $stats['errors'][] = "Matapelajaran '{$subject->name}': " . $e->getMessage();
            }
        }

        // 2. Sync Teachers
        $teachers = Teacher::with('user')->get();
        foreach ($teachers as $teacher) {
            $user = $teacher->user;
            $username = $user ? $user->username : ($teacher->nip ?? $teacher->email);
            if (!$username) {
                continue;
            }

            $subjectsList = $teacher->subject 
                ? array_map('trim', explode(',', $teacher->subject))
                : [];

            $payload = [
                'username' => $username,
                'name' => $teacher->name,
                'role' => 'guru',
                'tanggal_lahir' => $teacher->dob ? $teacher->dob->format('Y-m-d') : null,
                'subjects' => $subjectsList,
                'email' => $teacher->email ?? $user?->email,
                'password' => $user?->password,
            ];
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post("{$apiUrl}/sync/user", $payload);

                if ($response->successful()) {
                    $stats['teachers']++;
                } else {
                    $stats['errors'][] = "Guru '{$teacher->name}': " . $response->status() . " " . $response->body();
                }
            } catch (\Exception $e) {
                $stats['errors'][] = "Guru '{$teacher->name}': " . $e->getMessage();
            }
        }

        // 3. Sync Students
        $students = Student::with(['class', 'user'])->get();
        foreach ($students as $student) {
            $payload = [
                'nis' => $student->nis,
                'nama' => $student->name,
                'kelas' => $student->class?->name ?? 'Umum',
                'ruangan' => $student->class?->room,
                'tanggal_lahir' => $student->dob ? $student->dob->format('Y-m-d') : null,
                'password' => $student->user?->password,
            ];
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post("{$apiUrl}/sync/student", $payload);

                if ($response->successful()) {
                    $stats['students']++;
                } else {
                    $stats['errors'][] = "Siswa '{$student->name}': " . $response->status() . " " . $response->body();
                }
            } catch (\Exception $e) {
                $stats['errors'][] = "Siswa '{$student->name}': " . $e->getMessage();
            }
        }

        // 4. Sync Admins & Staff
        $nonAcademicUsers = User::whereNotIn('role', ['siswa', 'guru'], 'and')->get();
        foreach ($nonAcademicUsers as $u) {
            $roleValue = $u->role instanceof UserRole ? $u->role->value : (string) $u->role;
            $cbtRole = match ($roleValue) {
                'superadmin' => 'admin',
                'admin', 'kepala_sekolah' => 'admin',
                default => 'pengawas',
            };

            $payload = [
                'username' => $u->username,
                'name' => $u->name,
                'role' => $cbtRole,
                'email' => $u->email,
                'password' => $u->password,
            ];
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post("{$apiUrl}/sync/user", $payload);

                if ($response->successful()) {
                    $stats['users']++;
                } else {
                    $stats['errors'][] = "User '{$u->username}': " . $response->status() . " " . $response->body();
                }
            } catch (\Exception $e) {
                $stats['errors'][] = "User '{$u->username}': " . $e->getMessage();
            }
        }

        $totalSuccess = $stats['subjects'] + $stats['teachers'] + $stats['students'] + $stats['users'];
        $hasErrors = !empty($stats['errors']);

        if (!$hasErrors && $totalSuccess > 0) {
            $message = "Sinkronisasi ke CBT Berhasil! Terkirim: {$stats['subjects']} Matapelajaran, {$stats['teachers']} Guru, {$stats['students']} Siswa, {$stats['users']} Staff/Admin.";
            return ['success' => true, 'message' => $message, 'stats' => $stats];
        } elseif ($totalSuccess > 0) {
            $message = "Sinkronisasi selesai dengan beberapa kendala. Berhasil: {$totalSuccess} data ({$stats['subjects']} Matapelajaran, {$stats['teachers']} Guru, {$stats['students']} Siswa, {$stats['users']} Admin/Staff). Gagal: " . count($stats['errors']) . " item.";
            return ['success' => true, 'message' => $message, 'stats' => $stats];
        } else {
            $firstError = $stats['errors'][0] ?? 'Tidak ada data yang dapat disinkronkan atau server CBT tidak merespon.';
            return ['success' => false, 'message' => "Gagal menyinkronkan data ke CBT. Details: {$firstError}", 'stats' => $stats];
        }
    }
}

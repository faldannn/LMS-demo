<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{Http, Log};

class SyncCbtAfterImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param string $type Either 'teacher' or 'student'
     * @param array $ids Array of model IDs to sync
     */
    public function __construct(
        public readonly string $type,
        public readonly array $ids,
    ) {
    }

    public function handle(): void
    {
        $apiUrl = config('services.cbt.api_url');
        $secretKey = config('services.cbt.sync_secret_key');

        if (!$apiUrl || !$secretKey) {
            Log::warning('CBT sync skipped during import: CBT_API_URL or SYNC_SECRET_KEY not set in .env');
            return;
        }

        if (empty($this->ids)) {
            return;
        }

        if ($this->type === 'teacher') {
            $teachers = Teacher::with('user')->whereIn('id', $this->ids)->get();
            foreach ($teachers as $teacher) {
                $user = $teacher->user;
                $username = $user ? $user->username : ($teacher->nip ?? $teacher->email);
                if (!$username) {
                    continue;
                }

                $subjects = $teacher->subject 
                    ? array_map('trim', explode(',', $teacher->subject))
                    : [];

                $payload = [
                    'username' => $username,
                    'name' => $teacher->name,
                    'role' => 'guru',
                    'tanggal_lahir' => $teacher->dob ? $teacher->dob->format('Y-m-d') : null,
                    'subjects' => $subjects,
                    'email' => $teacher->email ?? $user?->email,
                    'password' => $user?->password,
                ];

                try {
                    Http::timeout(5)
                        ->withHeaders(['X-Sync-Secret' => $secretKey])
                        ->post(rtrim($apiUrl, '/') . '/sync/user', $payload);
                } catch (\Exception $e) {
                    Log::error("CBT Teacher Sync failed during import sync for teacher ID {$teacher->id}: " . $e->getMessage());
                }
            }
        } elseif ($this->type === 'student') {
            $students = Student::with(['class', 'user'])->whereIn('id', $this->ids)->get();
            foreach ($students as $student) {
                $user = $student->user;
                $payload = [
                    'nis' => $student->nis,
                    'nama' => $student->name,
                    'kelas' => $student->class?->name,
                    'ruangan' => $student->class?->room,
                    'tanggal_lahir' => $student->dob ? $student->dob->format('Y-m-d') : null,
                    'password' => $user?->password,
                ];

                try {
                    Http::timeout(5)
                        ->withHeaders(['X-Sync-Secret' => $secretKey])
                        ->post(rtrim($apiUrl, '/') . '/sync/student', $payload);
                } catch (\Exception $e) {
                    Log::error("CBT Student Sync failed during import sync for student ID {$student->id}: " . $e->getMessage());
                }
            }
        }
    }
}

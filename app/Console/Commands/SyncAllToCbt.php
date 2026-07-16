<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SyncAllToCbt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cbt:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all subjects, teachers, and students from LMS to CBT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = env('CBT_API_URL');
        $secretKey = env('SYNC_SECRET_KEY');

        if (!$apiUrl || !$secretKey) {
            $this->error('CBT_API_URL or SYNC_SECRET_KEY is not configured in .env');
            return Command::FAILURE;
        }

        $this->info('Starting sync to CBT...');

        // 1. Sync Subjects
        $subjects = Subject::all();
        $this->info("Syncing {$subjects->count()} subjects...");
        foreach ($subjects as $subject) {
            $payload = [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'level' => $subject->grade,
            ];
            try {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/subject', $payload);
            } catch (\Exception $e) {
                $this->warn("Failed to sync subject {$subject->name}: " . $e->getMessage());
            }
        }

        // 2. Sync Teachers
        $teachers = Teacher::all();
        $this->info("Syncing {$teachers->count()} teachers...");
        foreach ($teachers as $teacher) {
            $user = $teacher->user_id ? User::find($teacher->user_id) : null;
            $username = $user ? $user->username : ($teacher->nip ?? $teacher->email);
            if (!$username) continue;

            $subjectsList = $teacher->subject 
                ? array_map('trim', explode(',', $teacher->subject))
                : [];

            $payload = [
                'username' => $username,
                'name' => $teacher->name,
                'role' => 'guru',
                'tanggal_lahir' => $teacher->dob ? $teacher->dob->format('Y-m-d') : null,
                'subjects' => $subjectsList,
            ];
            try {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/user', $payload);
            } catch (\Exception $e) {
                $this->warn("Failed to sync teacher {$teacher->name}: " . $e->getMessage());
            }
        }

        // 3. Sync Students
        $students = Student::all();
        $this->info("Syncing {$students->count()} students...");
        foreach ($students as $student) {
            if (!$student->relationLoaded('class')) {
                $student->load('class');
            }
            $payload = [
                'nis' => $student->nis,
                'nama' => $student->name,
                'kelas' => $student->class?->name,
                'ruangan' => $student->class?->room,
                'tanggal_lahir' => $student->dob ? $student->dob->format('Y-m-d') : null,
            ];
            try {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/student', $payload);
            } catch (\Exception $e) {
                $this->warn("Failed to sync student {$student->name}: " . $e->getMessage());
            }
        }

        $this->info('Sync completed successfully!');
        return Command::SUCCESS;
    }
}

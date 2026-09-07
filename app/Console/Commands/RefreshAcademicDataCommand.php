<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RefreshAcademicDataCommand extends Command
{
    protected $signature = 'academic:refresh
        {students : File Excel siswa 2026/2027}
        {teachers : File Excel guru}
        {--force : Konfirmasi penggantian data akademik}';

    protected $description = 'Hapus data akademik lama, pertahankan admin/superadmin, lalu impor siswa dan guru baru.';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->error('Tambahkan --force setelah backup database diverifikasi.');
            return self::FAILURE;
        }

        $studentsFile = realpath($this->argument('students'));
        $teachersFile = realpath($this->argument('teachers'));
        if (!$studentsFile || !$teachersFile) {
            $this->error('File sumber siswa atau guru tidak ditemukan.');
            return self::FAILURE;
        }

        $adminCount = User::query()->whereIn('role', [UserRole::Admin->value, UserRole::SuperAdmin->value])->count();
        if ($adminCount === 0) {
            $this->error('Dibatalkan: tidak ada akun admin/superadmin yang dapat dipertahankan.');
            return self::FAILURE;
        }

        try {
            $summary = DB::transaction(function () use ($studentsFile, $teachersFile, $adminCount) {
                $this->deleteAcademicData();

                AcademicYear::query()->update(['is_active' => false]);
                AcademicYear::updateOrCreate(['year' => '2026/2027'], ['is_active' => true]);

                $studentImport = new StudentsImport();
                Excel::import($studentImport, $studentsFile);

                $teacherImport = new TeachersImport();
                Excel::import($teacherImport, $teachersFile);

                $summary = [
                    'admin' => $adminCount,
                    'students' => Student::query()->count(),
                    'teachers' => Teacher::query()->count(),
                    'classes' => Classes::query()->count(),
                    'subjects' => Subject::query()->count(),
                    'student_accounts' => User::query()->where('role', UserRole::Siswa->value)->count(),
                    'teacher_accounts' => User::query()->where('role', UserRole::Guru->value)->count(),
                ];

                if ($summary['students'] === 0 || $summary['teachers'] === 0 || $summary['classes'] === 0) {
                    throw new \RuntimeException('Impor tidak menghasilkan data minimum yang diperlukan; transaksi dibatalkan.');
                }

                return $summary;
            });
        } catch (\Throwable $e) {
            report($e);
            $this->error('Penggantian data dibatalkan: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->table(array_keys($summary), [array_values($summary)]);
        $this->info('Data akademik 2026/2027 berhasil diganti. Admin dan superadmin dipertahankan.');

        return self::SUCCESS;
    }

    private function deleteAcademicData(): void
    {
        // Tabel-tabel ini seluruhnya merupakan data operasional/akademik, bukan konfigurasi atau akun admin.
        $tables = [
            'comments', 'discussion_threads', 'student_answers', 'quiz_attempt_questions',
            'quiz_attempts', 'question_options', 'quiz_questions', 'questions', 'question_banks',
            'quizzes', 'assignment_grades', 'assignment_submissions', 'assignments', 'materials',
            'self_attendance_logs', 'student_face_profiles', 'attendance_details', 'attendance',
            'teacher_attendances', 'report_card_details', 'report_cards', 'cbt_cheat_logs',
            'schedules', 'class_subject', 'teacher_subject', 'student_parents', 'students',
            'teachers', 'subjects', 'classes', 'semesters', 'academic_calendar_events',
            'announcements', 'majors', 'academic_years',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach ($tables as $table) {
                DB::table($table)->delete();
            }

            // Sesi lama dapat tetap mengautentikasi akun yang sudah dihapus, maka seluruh sesi dicabut.
            DB::table('sessions')->delete();
            User::query()
                ->whereNotIn('role', [UserRole::Admin->value, UserRole::SuperAdmin->value])
                ->delete();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}

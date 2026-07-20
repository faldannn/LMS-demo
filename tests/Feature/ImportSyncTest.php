<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\Student;
use App\Jobs\SyncCbtAfterImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_teachers_dispatches_sync_cbt_job(): void
    {
        Queue::fake();

        $kepalaSekolah = User::factory()->kepalaSekolah()->create(['status' => true]);

        // Create a temporary CSV file content simulating teacher import
        $csvContent = "Email Address,Nama Lengkap,Tempat Tanggal Lahir (Tempat, Tanggal, Bulan, Tahun),Email,Nomer Telepon Whatsapp,Guru Mata Pelajaran,Wali kelas (jika bukan walas maka tidak perlu di isi)\n"
            . "guru.baru@example.com,Guru Baru,Bekasi 5 Maret 1985,guru.baru@example.com,08123456789,Matematika,\n";

        $tempFile = UploadedFile::fake()->createWithContent('Format_Import_Guru.csv', $csvContent);

        $this->actingAs($kepalaSekolah)
            ->post(route('teachers.import'), [
                'file' => $tempFile,
            ])
            ->assertRedirect(route('teachers.index'));

        Queue::assertPushed(SyncCbtAfterImport::class, function ($job) {
            return $job->type === 'teacher' && count($job->ids) === 1;
        });
    }

    public function test_import_students_dispatches_sync_cbt_job(): void
    {
        Queue::fake();

        $kepalaSekolah = User::factory()->kepalaSekolah()->create(['status' => true]);

        $year = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
        $class = Classes::create([
            'name' => 'X IPA 1',
            'grade' => 10,
            'academic_year_id' => $year->id,
        ]);

        // Create a temporary CSV file content simulating student import
        // Headers parsed in StudentsImport: kelas, tanggal_lahir, nis, nama, nisn, jk, nama_ortu, no_hp_ortu
        $csvContent = "nis,nama,kelas,nisn,jk,nama_ortu,no_hp_ortu,tanggal_lahir\n"
            . "2025001,Siswa Baru,X IPA 1,12345678,L,Budi,08123456780,15-04-2008\n";

        $tempFile = UploadedFile::fake()->createWithContent('Format_Import_Siswa.csv', $csvContent);

        $this->actingAs($kepalaSekolah)
            ->post(route('students.import'), [
                'file' => $tempFile,
            ])
            ->assertRedirect(route('students.index'));

        Queue::assertPushed(SyncCbtAfterImport::class, function ($job) {
            return $job->type === 'student' && count($job->ids) === 1;
        });
    }

    public function test_sync_cbt_after_import_job_sends_http_requests(): void
    {
        Http::fake();

        // 1. Create a teacher
        $teacherUser = User::factory()->guru()->create(['username' => 'guru1', 'password' => 'pass1']);
        $teacher = Teacher::create([
            'nip' => 'guru1',
            'name' => 'Guru Test',
            'email' => 'guru@test.com',
            'user_id' => $teacherUser->id,
            'status' => true,
        ]);

        // 2. Create a student
        $studentUser = User::factory()->siswa()->create(['username' => 'siswa1', 'password' => 'pass2']);
        $year = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
        $class = Classes::create([
            'name' => 'X IPA 1',
            'grade' => 10,
            'academic_year_id' => $year->id,
        ]);
        $student = Student::create([
            'nis' => 'siswa1',
            'name' => 'Siswa Test',
            'gender' => 'L',
            'class_id' => $class->id,
            'user_id' => $studentUser->id,
            'status' => true,
        ]);

        // Run the job for teacher
        $jobTeacher = new SyncCbtAfterImport('teacher', [$teacher->id]);
        $jobTeacher->handle();

        // Run the job for student
        $jobStudent = new SyncCbtAfterImport('student', [$student->id]);
        $jobStudent->handle();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/sync/user') && $request['username'] === 'guru1';
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/sync/student') && $request['nis'] === 'siswa1';
        });
    }
}

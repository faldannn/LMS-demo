<?php

namespace Tests\Feature;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\DiscussionThread;
use App\Models\Material;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LmsModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $guruUser;
    private User $siswaUser;
    private Teacher $teacher;
    private Classes $class;
    private ClassSubject $classSubject;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $year = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
        $this->guruUser = User::factory()->guru()->create(['status' => true]);
        $this->teacher = Teacher::create([
            'name' => 'Guru LMS',
            'user_id' => $this->guruUser->id,
            'status' => true,
        ]);
        $this->class = Classes::create([
            'name' => 'X IPA 1',
            'grade' => 10,
            'academic_year_id' => $year->id,
        ]);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK', 'status' => true]);
        $this->classSubject = ClassSubject::create([
            'class_id' => $this->class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $this->siswaUser = User::factory()->siswa()->create(['status' => true]);
        $this->student = Student::create([
            'nis' => '2025099',
            'name' => 'Siswa LMS',
            'gender' => 'L',
            'class_id' => $this->class->id,
            'user_id' => $this->siswaUser->id,
            'status' => true,
        ]);
    }

    public function test_guru_can_upload_pdf_material_via_storage(): void
    {
        $file = UploadedFile::fake()->create('materi.pdf', 100, 'application/pdf');

        $this->actingAs($this->guruUser)
            ->post(route('materials.store', $this->classSubject), [
                'title' => 'Bab 1 Aljabar',
                'type' => 'pdf',
                'topic' => 'Bab 1',
                'file' => $file,
                'is_published' => '1',
            ])
            ->assertRedirect(route('materials.index', $this->classSubject));

        $material = Material::first();
        $this->assertNotNull($material);
        $this->assertSame('pdf', $material->type);
        Storage::disk('public')->assertExists($material->file_path);
    }

    public function test_student_can_view_and_download_published_material(): void
    {
        $path = 'materials/test.pdf';
        Storage::disk('public')->put($path, 'pdf-content');

        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'created_by' => $this->guruUser->id,
            'title' => 'Materi PDF',
            'type' => 'pdf',
            'file_path' => $path,
            'file_name' => 'test.pdf',
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->actingAs($this->siswaUser)
            ->get(route('materials.show', $material))
            ->assertOk()
            ->assertSee('Materi PDF');

        $this->actingAs($this->siswaUser)
            ->get(route('materials.download', $material))
            ->assertOk();
    }

    public function test_guru_can_create_youtube_material(): void
    {
        $this->actingAs($this->guruUser)
            ->post(route('materials.store', $this->classSubject), [
                'title' => 'Video YouTube',
                'type' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => '1',
            ])
            ->assertRedirect();

        $material = Material::first();
        $this->assertSame('youtube', $material->type);
        $this->assertSame('dQw4w9WgXcQ', $material->youtube_id);
    }

    public function test_student_can_comment_on_material(): void
    {
        $material = Material::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Materi',
            'type' => 'link',
            'url' => 'https://example.com',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->actingAs($this->siswaUser)
            ->post(route('comments.store', ['material', $material->id]), [
                'body' => 'Pertanyaan tentang materi ini',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'body' => 'Pertanyaan tentang materi ini',
            'user_id' => $this->siswaUser->id,
        ]);
    }

    public function test_discussion_thread_can_be_created_and_replied(): void
    {
        $this->actingAs($this->siswaUser)
            ->post(route('discussions.store', $this->classSubject), [
                'title' => 'Diskusi Bab 1',
                'body' => 'Ada yang bingung?',
            ])
            ->assertRedirect();

        $thread = DiscussionThread::first();
        $this->assertNotNull($thread);

        $this->actingAs($this->guruUser)
            ->post(route('discussions.reply', $thread), [
                'body' => 'Silakan tanyakan di sini',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'commentable_type' => DiscussionThread::class,
            'commentable_id' => $thread->id,
            'body' => 'Silakan tanyakan di sini',
        ]);
    }

    public function test_staff_can_create_academic_calendar_event(): void
    {
        $this->actingAs($this->guruUser)
            ->post(route('academic-calendar.store'), [
                'title' => 'Ujian Tengah Semester',
                'category' => 'ujian',
                'start_date' => now()->addWeek()->toDateString(),
                'is_public' => '1',
            ])
            ->assertRedirect(route('academic-calendar.index'));

        $this->assertDatabaseHas('academic_calendar_events', [
            'title' => 'Ujian Tengah Semester',
            'category' => 'ujian',
        ]);
    }

    public function test_student_can_view_academic_calendar(): void
    {
        AcademicCalendarEvent::create([
            'title' => 'Libur Nasional',
            'category' => 'libur',
            'start_date' => now()->startOfMonth(),
            'created_by' => $this->guruUser->id,
            'is_public' => true,
        ]);

        $this->actingAs($this->siswaUser)
            ->get(route('academic-calendar.index'))
            ->assertOk()
            ->assertSee('Libur Nasional');
    }

    public function test_assignment_deadline_is_visible_to_student(): void
    {
        $due = now()->addDays(3);
        Assignment::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Tugas 1',
            'description' => 'Kerjakan soal',
            'type' => 'individu',
            'due_date' => $due,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->actingAs($this->siswaUser)
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('Tugas 1');
    }
}

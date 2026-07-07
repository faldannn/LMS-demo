<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CbtQuizTest extends TestCase
{
    use RefreshDatabase;

    private User $guruUser;
    private User $siswaUser;
    private ClassSubject $classSubject;
    private QuestionBank $bank;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $year = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
        $this->guruUser = User::factory()->guru()->create(['status' => true]);
        $teacher = Teacher::create(['name' => 'Guru CBT', 'user_id' => $this->guruUser->id, 'status' => true]);
        $class = Classes::create(['name' => 'X IPA', 'grade' => 10, 'academic_year_id' => $year->id]);
        $subject = Subject::create(['name' => 'IPA', 'code' => 'IPA', 'status' => true]);
        $this->classSubject = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $year->id,
        ]);

        $this->bank = QuestionBank::create([
            'class_subject_id' => $this->classSubject->id,
            'title' => 'Bank UTS',
            'created_by' => $this->guruUser->id,
        ]);

        $this->siswaUser = User::factory()->siswa()->create(['status' => true]);
        $this->student = Student::create([
            'nis' => '2025101',
            'name' => 'Siswa CBT',
            'gender' => 'P',
            'class_id' => $class->id,
            'user_id' => $this->siswaUser->id,
            'status' => true,
        ]);
    }

    private function createMcQuestion(int $points = 10, bool $correctFirst = true): Question
    {
        $q = Question::create([
            'question_bank_id' => $this->bank->id,
            'question_text' => 'Soal PG ' . uniqid(),
            'type' => 'multiple_choice',
            'points' => $points,
            'is_active' => true,
        ]);
        $opt1 = QuestionOption::create(['question_id' => $q->id, 'option_text' => 'A', 'is_correct' => $correctFirst, 'order_number' => 1]);
        QuestionOption::create(['question_id' => $q->id, 'option_text' => 'B', 'is_correct' => !$correctFirst, 'order_number' => 2]);

        return $q->fresh('options');
    }

    public function test_guru_can_create_question_bank_and_mc_question(): void
    {
        $this->actingAs($this->guruUser)
            ->post(route('questions.store', $this->bank), [
                'question_text' => 'Berapa 2+2?',
                'type' => 'multiple_choice',
                'difficulty' => 'easy',
                'points' => 5,
                'options' => ['3', '4', '5', '6'],
                'correct_option' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('questions', [
            'question_bank_id' => $this->bank->id,
            'type' => 'multiple_choice',
        ]);
    }

    public function test_quiz_random_mode_generates_attempt_questions(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->createMcQuestion();
        }

        $quiz = Quiz::create([
            'class_subject_id' => $this->classSubject->id,
            'question_bank_id' => $this->bank->id,
            'title' => 'UTS Acak',
            'duration' => 30,
            'is_published' => true,
            'selection_mode' => 'random',
            'random_question_count' => 3,
            'shuffle_questions' => true,
            'shuffle_answers' => true,
        ]);

        $this->actingAs($this->siswaUser)
            ->post(route('student.quizzes.start', $quiz))
            ->assertRedirect(route('student.quizzes.attempt', $quiz));

        $attempt = QuizAttempt::first();
        $this->assertCount(3, $attempt->attemptQuestions);
    }

    public function test_auto_scoring_for_multiple_choice(): void
    {
        $q = $this->createMcQuestion(10, true);
        $correctId = $q->options->firstWhere('is_correct', true)->id;

        $quiz = Quiz::create([
            'class_subject_id' => $this->classSubject->id,
            'question_bank_id' => $this->bank->id,
            'title' => 'Kuis PG',
            'duration' => 15,
            'is_published' => true,
            'selection_mode' => 'fixed',
            'show_results' => true,
        ]);
        $quiz->bankQuestions()->attach($q->id, ['order_number' => 1]);

        $this->actingAs($this->siswaUser)->post(route('student.quizzes.start', $quiz));
        $attempt = QuizAttempt::first();

        $this->actingAs($this->siswaUser)->postJson(route('student.quizzes.save-answer', $quiz), [
            'question_id' => $q->id,
            'option_id' => $correctId,
        ])->assertOk();

        $this->actingAs($this->siswaUser)->post(route('student.quizzes.finish', $quiz));

        $attempt->refresh();
        $this->assertSame('100.00', number_format((float) $attempt->score, 2));
    }

    public function test_guru_can_export_quiz_results(): void
    {
        $q = $this->createMcQuestion();
        $quiz = Quiz::create([
            'class_subject_id' => $this->classSubject->id,
            'question_bank_id' => $this->bank->id,
            'title' => 'Export Test',
            'duration' => 10,
            'is_published' => true,
            'selection_mode' => 'fixed',
        ]);
        $quiz->bankQuestions()->attach($q->id, ['order_number' => 1]);

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'start_time' => now()->subMinutes(5),
            'end_time' => now(),
            'expires_at' => now(),
            'score' => 80,
            'status' => 'finished',
            'total_points' => 10,
            'earned_points' => 8,
        ]);

        $this->actingAs($this->guruUser)
            ->get(route('quizzes.results.export', $quiz))
            ->assertOk();
    }

    public function test_guru_can_open_quiz_detail_page(): void
    {
        $q = $this->createMcQuestion();
        $quiz = Quiz::create([
            'class_subject_id' => $this->classSubject->id,
            'question_bank_id' => $this->bank->id,
            'title' => 'Detail CBT',
            'duration' => 20,
            'is_published' => true,
            'selection_mode' => 'fixed',
        ]);
        $quiz->bankQuestions()->attach($q->id, ['order_number' => 1]);

        $this->actingAs($this->guruUser)
            ->get(route('quizzes.show', $quiz))
            ->assertOk()
            ->assertSee('Soal Ujian (1)');
    }

    public function test_student_can_see_loaded_question_count_on_quiz_detail(): void
    {
        $q = $this->createMcQuestion();
        $quiz = Quiz::create([
            'class_subject_id' => $this->classSubject->id,
            'question_bank_id' => $this->bank->id,
            'title' => 'Detail Siswa',
            'duration' => 15,
            'is_published' => true,
            'selection_mode' => 'fixed',
        ]);
        $quiz->bankQuestions()->attach($q->id, ['order_number' => 1]);

        $this->actingAs($this->siswaUser)
            ->get(route('student.quizzes.show', $quiz))
            ->assertOk()
            ->assertSee('1 Soal');
    }

    public function test_save_answer_requires_question_id(): void
    {
        $q = $this->createMcQuestion();
        $quiz = Quiz::create([
            'class_subject_id' => $this->classSubject->id,
            'question_bank_id' => $this->bank->id,
            'title' => 'Validasi Jawaban',
            'duration' => 15,
            'is_published' => true,
            'selection_mode' => 'fixed',
        ]);
        $quiz->bankQuestions()->attach($q->id, ['order_number' => 1]);

        $this->actingAs($this->siswaUser)->post(route('student.quizzes.start', $quiz));

        $this->actingAs($this->siswaUser)
            ->postJson(route('student.quizzes.save-answer', $quiz), [
                'option_id' => $q->options->first()->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['question_id']);
    }

}
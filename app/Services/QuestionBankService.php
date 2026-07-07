<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class QuestionBankService
{
    public function __construct(private readonly FileStorageService $fileStorage)
    {
    }

    public function listForClassSubject(int $classSubjectId, array $filters = []): LengthAwarePaginator
    {
        return QuestionBank::where('class_subject_id', $classSubjectId)
            ->withCount('questions')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();
    }

    public function listQuestions(QuestionBank $bank, array $filters = []): LengthAwarePaginator
    {
        return $bank->questions()
            ->withCount('options')
            ->when($filters['type'] ?? null, fn ($q, $t) => $q->where('type', $t))
            ->when($filters['difficulty'] ?? null, fn ($q, $d) => $q->where('difficulty', $d))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('question_text', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();
    }

    public function createBank(array $data): QuestionBank
    {
        return QuestionBank::create($data);
    }

    public function createQuestion(QuestionBank $bank, array $data, ?UploadedFile $image = null): Question
    {
        return DB::transaction(function () use ($bank, $data, $image) {
            $questionData = [
                'question_bank_id' => $bank->id,
                'created_by' => $data['created_by'] ?? null,
                'question_text' => $data['question_text'],
                'type' => $data['type'],
                'difficulty' => $data['difficulty'] ?? 'medium',
                'points' => $data['points'],
                'order_number' => ($bank->questions()->max('order_number') ?? 0) + 1,
                'is_active' => true,
            ];

            if ($image) {
                $uploaded = $this->fileStorage->upload($image, "question-banks/{$bank->id}");
                $questionData['image_path'] = $uploaded['file_path'];
            }

            $question = Question::create($questionData);

            if ($data['type'] === 'multiple_choice' && !empty($data['options'])) {
                foreach ($data['options'] as $index => $optionText) {
                    if (trim((string) $optionText) === '') {
                        continue;
                    }
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => (int) ($data['correct_option'] ?? -1) === (int) $index,
                        'order_number' => $index + 1,
                    ]);
                }
            }

            return $question;
        });
    }

    public function deleteQuestion(Question $question): void
    {
        if ($question->image_path) {
            $this->fileStorage->delete($question->image_path);
        }
        $question->delete();
    }
}

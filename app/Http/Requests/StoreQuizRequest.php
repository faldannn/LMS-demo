<?php

namespace App\Http\Requests;

use App\Models\Quiz;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'question_bank_id' => 'nullable|exists:question_banks,id',
            'duration' => 'required|integer|min:1|max:300',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_published' => 'boolean',
            'show_results' => 'boolean',
            'selection_mode' => ['required', Rule::in(Quiz::SELECTION_MODES)],
            'random_question_count' => 'nullable|integer|min:1|required_if:selection_mode,random',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'auto_submit_on_timeout' => 'boolean',
            'pass_score' => 'nullable|numeric|min:0|max:100',
            'questions_per_page' => 'nullable|integer|min:1|max:10',
        ];
    }
}

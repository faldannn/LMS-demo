<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveQuizAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isSiswa() ?? false;
    }

    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:question_options,id',
            'essay_answer' => 'nullable|string',
        ];
    }
}


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isStaff();
    }

    public function rules(): array
    {
        $rules = [
            'question_text' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'points' => 'required|integer|min:1',
        ];

        if ($this->input('type') === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';
            $rules['correct_option'] = 'required|integer|min:0';
        }

        return $rules;
    }
}

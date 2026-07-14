<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('subjects', 'code')->ignore($subjectId)->whereNull('deleted_at'),
            ],
            'name' => 'required|string|max:100',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'grade' => 'nullable|integer|in:10,11,12',
            'status' => 'boolean',
        ];
    }
}

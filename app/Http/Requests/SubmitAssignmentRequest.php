<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('siswa');
    }

    public function rules(): array
    {
        return [
            'content' => 'required_without:file|nullable|string',
            'file' => 'required_without:content|nullable|' . \App\Support\UploadRules::ruleString('assignment'),
        ];
    }

    public function messages(): array
    {
        return [
            'content.required_without' => 'Anda harus mengisi teks atau mengunggah file.',
            'file.required_without' => 'Anda harus mengunggah file atau mengisi teks.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nip' => 'nullable|string|max:30|unique:teachers,nip',
            'name' => 'required|string|max:100',
            'dob' => 'nullable|date',
            'subject' => 'nullable|string|max:100',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:teachers,email',
            'status' => 'nullable|boolean',
        ];
    }
}

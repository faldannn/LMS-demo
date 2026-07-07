<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'grade' => 'required|integer|in:10,11,12',
            'major_id' => 'nullable|exists:majors,id',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'room' => 'nullable|string|max:20',
            'max_students' => 'integer|min:1|max:50',
        ];
    }
}

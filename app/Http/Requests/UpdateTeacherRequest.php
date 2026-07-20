<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        // access the route parameter 'teacher'
        $teacher = $this->route('teacher');
        $id = $teacher ? $teacher->id : null;

        return [
            'nip' => "nullable|string|max:30|unique:teachers,nip,{$id}",
            'name' => 'required|string|max:100',
            'dob' => 'required|date',
            'subject' => 'nullable|string|max:100',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'phone' => 'nullable|string|max:20',
            'email' => "nullable|email|max:100|unique:teachers,email,{$id}",
            'status' => 'boolean',
            'password' => 'nullable|string|min:6',
        ];
    }
}

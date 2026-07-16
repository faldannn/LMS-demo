<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $id = $user ? $user->id : null;

        $rules = [
            'name' => 'required|string|max:100',
            'email' => "nullable|email|unique:users,email,{$id}",
            'role' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\UserRole::class)],
            'password' => [
                'nullable',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers(),
            ],
            'username' => "sometimes|required|string|max:50|unique:users,username,{$id}|alpha_dash",
        ];

        if ($this->input('role') === 'siswa') {
            $studentId = $user && $user->student ? $user->student->id : null;
            $rules['nis'] = "required|string|unique:students,nis,{$studentId}";
            $rules['nisn'] = "nullable|string|unique:students,nisn,{$studentId}";
            $rules['gender'] = 'required|in:L,P';
            $rules['class_id'] = 'required|exists:classes,id';
            $rules['dob'] = 'nullable|date';
            $rules['pob'] = 'nullable|string';
            $rules['parent_name'] = 'nullable|string';
            $rules['parent_phone'] = 'nullable|string';
            $rules['parent_email'] = 'nullable|email';
            $rules['address'] = 'nullable|string';
        } elseif ($this->input('role') === 'guru') {
            $teacherId = $user && $user->teacher ? $user->teacher->id : null;
            $rules['nip'] = "nullable|string|unique:teachers,nip,{$teacherId}";
            $rules['dob'] = 'required|date';
            $rules['phone'] = 'nullable|string';
            $rules['subject_ids'] = 'required|array';
            $rules['subject_ids.*'] = 'exists:subjects,id';
        }

        return $rules;
    }
}

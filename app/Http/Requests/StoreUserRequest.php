<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        $rules = [
            'username' => 'required|string|max:50|unique:users|alpha_dash',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users',
            'role' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\UserRole::class)],
        ];

        if ($this->input('role') === 'siswa') {
            $rules['nis'] = 'required|string|unique:students,nis';
            $rules['nisn'] = 'nullable|string|unique:students,nisn';
            $rules['gender'] = 'required|in:L,P';
            $rules['class_id'] = 'required|exists:classes,id';
            $rules['dob'] = 'nullable|date';
            $rules['pob'] = 'nullable|string';
            $rules['parent_name'] = 'nullable|string';
            $rules['parent_phone'] = 'nullable|string';
            $rules['parent_email'] = 'nullable|email';
            $rules['address'] = 'nullable|string';
        } elseif ($this->input('role') === 'guru') {
            $rules['nip'] = 'nullable|string|unique:teachers,nip';
            $rules['dob'] = 'required|date';
            $rules['phone'] = 'nullable|string';
            $rules['subject_ids'] = 'required|array';
            $rules['subject_ids.*'] = 'exists:subjects,id';
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('role') === 'superadmin' && (!auth()->check() || !auth()->user()->isSuperAdmin())) {
                $validator->errors()->add('role', 'Hanya Superadmin yang dapat membuat user dengan role Superadmin.');
            }
        });
    }
}

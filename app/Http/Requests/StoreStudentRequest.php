<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student?->id;
        $userId = $student?->user_id;

        return [
            'nis' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'nis')->whereNull('deleted_at')->ignore($studentId),
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'nisn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('students', 'nisn')->whereNull('deleted_at')->ignore($studentId),
            ],
            'name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'dob' => 'nullable|date|before:today',
            'pob' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:100',
            'photo' => \App\Support\UploadRules::ruleString('image') . '|nullable',
            'status' => 'boolean',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.unique' => 'NIS ini sudah terdaftar pada data siswa atau akun pengguna.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
        ];
    }
}
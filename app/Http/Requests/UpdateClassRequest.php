<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        $classId = $this->route('class')?->id ?? $this->route('class');

        return [
            'name'               => 'required|string|max:50',
            'grade'              => 'required|integer|in:10,11,12',
            'major_id'           => 'nullable|exists:majors,id',
            'homeroom_teacher_id'=> [
                'nullable',
                'exists:teachers,id',
                // A teacher can only be homeroom for 1 class; ignore current class row
                Rule::unique('classes', 'homeroom_teacher_id')->ignore($classId),
            ],
            'room'               => 'nullable|string|max:20',
            'max_students'       => 'integer|min:1|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'homeroom_teacher_id.unique' => 'Guru ini sudah menjadi wali kelas di kelas lain. Satu guru hanya dapat menjadi wali kelas untuk 1 kelas saja.',
        ];
    }
}

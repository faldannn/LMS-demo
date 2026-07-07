<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isKepalaSekolah() ?? false;
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'string', 'max:9', Rule::unique('academic_years', 'year')->ignore($this->route('academic_year'))],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }
}

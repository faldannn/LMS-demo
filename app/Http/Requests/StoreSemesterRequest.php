<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSemesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isKepalaSekolah() ?? false;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'number' => ['required', 'integer', Rule::in([1, 2]), Rule::unique('semesters')->where(fn ($q) => $q->where('academic_year_id', $this->academic_year_id))],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ];
    }
}

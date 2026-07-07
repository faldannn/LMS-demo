<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicCalendarEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category' => 'required|in:akademik,ujian,libur,kegiatan,lainnya',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'class_id' => 'nullable|exists:classes,id',
            'is_public' => 'boolean',
        ];
    }
}

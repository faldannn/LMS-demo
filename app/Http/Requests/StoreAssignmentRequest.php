<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'type' => 'required|in:individu,kelompok',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:1|max:100',
            'file' => 'nullable|file|max:51200', // max 50MB
            'is_published' => 'boolean',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:100',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'grade' => 'nullable|integer|in:10,11,12',
            'status' => 'boolean',
        ];
    }
}

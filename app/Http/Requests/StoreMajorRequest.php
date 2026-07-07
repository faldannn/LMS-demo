<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMajorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isKepalaSekolah() ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:20|unique:majors,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'boolean',
        ];
    }
}

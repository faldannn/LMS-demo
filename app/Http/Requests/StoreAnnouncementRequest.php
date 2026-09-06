<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:umum,kelas',
            'class_id' => 'required_if:type,kelas|nullable|exists:classes,id',
            'attachment' => 'nullable|' . \App\Support\UploadRules::ruleString('announcement'),
            'is_published' => 'boolean',
        ];
    }
}

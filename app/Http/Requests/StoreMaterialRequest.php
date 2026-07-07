<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
            'type' => 'required|in:pdf,ppt,video,youtube,image,link',
            'file' => 'required_if:type,pdf,ppt,video,image|nullable|' . \App\Services\FileStorageService::validationRule(
                match ($this->type) {
                    'pdf' => 'document',
                    'ppt' => 'presentation',
                    'video' => 'video',
                    'image' => 'image',
                    default => 'document',
                }
            ),
            'url' => 'required_if:type,youtube,link|nullable|url|max:500',
            'topic' => 'nullable|string|max:100',
            'is_published' => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->type === 'youtube' && $this->url) {
                if (!\App\Services\FileStorageService::extractYoutubeId($this->url)) {
                    $validator->errors()->add('url', 'URL YouTube tidak valid.');
                }
            }
            if ($this->type === 'pdf' && $this->hasFile('file')) {
                $ext = strtolower($this->file('file')->getClientOriginalExtension());
                if (!in_array($ext, ['pdf'], true)) {
                    $validator->errors()->add('file', 'File harus berformat PDF.');
                }
            }
            if ($this->type === 'ppt' && $this->hasFile('file')) {
                $ext = strtolower($this->file('file')->getClientOriginalExtension());
                if (!in_array($ext, ['ppt', 'pptx'], true)) {
                    $validator->errors()->add('file', 'File harus berformat PPT/PPTX.');
                }
            }
        });
    }
}

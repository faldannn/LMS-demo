<?php

namespace App\Services;

use App\Support\UploadRules;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    public function __construct(private readonly string $disk = 'public')
    {
    }

    public function upload(UploadedFile $file, string $directory, ?string $uploadType = null): array
    {
        if ($uploadType) {
            $this->assertSafeUpload($file, $uploadType);
        }

        $path = $file->store($directory, $this->disk);

        return [
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    public function assertSafeUpload(UploadedFile $file, string $type): void
    {
        $config = config("security.uploads.{$type}");
        if (!$config) {
            return;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = $config['mimes'] ?? [];

        if (!in_array($extension, $allowed, true)) {
            throw ValidationException::withMessages([
                'file' => 'Format file tidak diizinkan.',
            ]);
        }

        $maxBytes = ($config['max_kb'] ?? 5120) * 1024;
        if ($file->getSize() > $maxBytes) {
            throw ValidationException::withMessages([
                'file' => 'Ukuran file melebihi batas yang diizinkan.',
            ]);
        }
    }

    public static function validationRule(string $type, bool $required = false): string
    {
        return $required ? 'required|' . UploadRules::ruleString($type) : 'nullable|' . UploadRules::ruleString($type);
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    public function download(?string $path, ?string $name = null): StreamedResponse
    {
        abort_unless($path && Storage::disk($this->disk)->exists($path), 404, 'File tidak ditemukan.');

        return Storage::disk($this->disk)->download($path, $name ?? basename($path));
    }

    public function url(?string $path): ?string
    {
        return $path ? Storage::disk($this->disk)->url($path) : null;
    }

    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }

        return null;
    }
}

<?php

namespace App\Services;

use App\Models\Material;
use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;

class MaterialService
{
    public function __construct(private readonly FileStorageService $storage)
    {
    }

    public function getByClassSubject(int $classSubjectId)
    {
        return Material::where('class_subject_id', $classSubjectId)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('topic');
    }

    public function prepareData(array $data, ?UploadedFile $file = null): array
    {
        if ($file && in_array($data['type'], ['pdf', 'ppt', 'video', 'image'], true)) {
            $subdir = match ($data['type']) {
                'pdf' => 'materials/pdf',
                'ppt' => 'materials/ppt',
                'video' => 'materials/video',
                default => 'materials/files',
            };
            $data = array_merge($data, $this->storage->upload($file, $subdir));
        }

        if ($data['type'] === 'youtube' && !empty($data['url'])) {
            $data['youtube_id'] = FileStorageService::extractYoutubeId($data['url']);
        }

        return $data;
    }

    public function uploadFile(UploadedFile $file, string $directory = 'materials'): array
    {
        return $this->storage->upload($file, $directory);
    }

    public function deleteFile(?string $path): void
    {
        $this->storage->delete($path);
    }

    public function download(Material $material)
    {
        return $this->storage->download($material->file_path, $material->file_name);
    }
}

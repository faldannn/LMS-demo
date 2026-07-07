<?php

namespace App\Services;

use App\Models\Assignment;
use Illuminate\Http\UploadedFile;

class AssignmentService
{
    public function __construct(private readonly FileStorageService $storage)
    {
    }

    public function getByClassSubject(int $classSubjectId)
    {
        return Assignment::where('class_subject_id', $classSubjectId)
            ->withCount('submissions')
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function uploadFile(UploadedFile $file, string $directory = 'assignments'): array
    {
        return $this->storage->upload($file, $directory);
    }

    public function deleteFile(?string $path): void
    {
        $this->storage->delete($path);
    }

    public function downloadSubmission(?string $path, ?string $name)
    {
        return $this->storage->download($path, $name);
    }

    public function checkIsLate(Assignment $assignment): bool
    {
        if (!$assignment->due_date) {
            return false;
        }

        return now()->isAfter($assignment->due_date);
    }

    public function deadlineStatus(Assignment $assignment): string
    {
        if (!$assignment->due_date) {
            return 'open';
        }
        if ($assignment->due_date->isPast()) {
            return 'closed';
        }
        if ($assignment->due_date->diffInHours(now()) <= 48) {
            return 'urgent';
        }

        return 'open';
    }
}

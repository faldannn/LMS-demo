<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class AnnouncementService
{
    public function __construct(private readonly FileStorageService $storage)
    {
    }

    public function listForUser(User $user)
    {
        $query = Announcement::with(['creator', 'class'])
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        if ($user->isGuru()) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)->orWhere('type', 'umum');
            });
        }

        if ($user->isSiswa()) {
            $classId = $user->student?->class_id;
            $query->where(function ($q) use ($classId) {
                $q->where('type', 'umum');
                if ($classId) {
                    $q->orWhere('class_id', $classId);
                }
            });
        }

        return $query->paginate(10);
    }

    public function create(array $data, ?UploadedFile $file = null): Announcement
    {
        if ($file) {
            $data = array_merge($data, $this->storage->upload($file, 'announcements'));
            $data['attachment_path'] = $data['file_path'];
            $data['attachment_name'] = $data['file_name'];
            $data['attachment_size'] = $data['file_size'];
            $data['attachment_mime'] = $data['mime_type'];
            unset($data['file_path'], $data['file_name'], $data['file_size'], $data['mime_type']);
        }

        $data['published_at'] = !empty($data['is_published']) ? now() : null;

        return Announcement::create($data);
    }

    public function delete(Announcement $announcement): void
    {
        $this->storage->delete($announcement->attachment_path);
        $announcement->delete();
    }

    public function download(Announcement $announcement)
    {
        return $this->storage->download($announcement->attachment_path, $announcement->attachment_name);
    }
}

<?php

namespace App\Services;

use App\Models\DiscussionThread;

class DiscussionService
{
    public function listForClassSubject(int $classSubjectId)
    {
        return DiscussionThread::with(['user:id,name', 'classSubject.subject'])
            ->withCount('allComments')
            ->where('class_subject_id', $classSubjectId)
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(15);
    }

    public function create(array $data): DiscussionThread
    {
        return DiscussionThread::create($data);
    }
}

<?php

namespace App\Repositories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;

class TeacherRepository extends BaseRepository
{
    protected function model(): string
    {
        return Teacher::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('subjects', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
        });
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with(['user', 'homeroomClasses', 'subjects'])
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }
}

<?php

namespace App\Repositories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;

class SubjectRepository extends BaseRepository
{
    protected function model(): string
    {
        return Subject::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('group', 'like', "%{$search}%");
        });
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['group'] ?? null, fn ($q, $v) => $q->where('group', $v))
            ->when($filters['grade'] ?? null, fn ($q, $v) => $q->where('grade', $v))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }
}

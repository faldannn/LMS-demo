<?php

namespace App\Repositories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;

class SemesterRepository extends BaseRepository
{
    protected function model(): string
    {
        return Semester::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->whereHas('academicYear', fn ($q) => $q->where('year', 'like', "%{$search}%"));
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with('academicYear')
            ->when($filters['academic_year_id'] ?? null, fn ($q, $v) => $q->where('academic_year_id', $v))
            ->when($filters['number'] ?? null, fn ($q, $v) => $q->where('number', $v))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }
}

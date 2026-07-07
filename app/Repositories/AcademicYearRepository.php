<?php

namespace App\Repositories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;

class AcademicYearRepository extends BaseRepository
{
    protected function model(): string
    {
        return AcademicYear::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->where('year', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->withCount(['classes', 'semesters'])
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }
}

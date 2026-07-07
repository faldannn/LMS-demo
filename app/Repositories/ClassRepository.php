<?php

namespace App\Repositories;

use App\Models\Classes;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ClassRepository extends BaseRepository
{
    protected function model(): string
    {
        return Classes::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('room', 'like', "%{$search}%");
        });
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with(['homeroomTeacher', 'academicYear', 'major'])
            ->withCount('students')
            ->when($filters['grade'] ?? null, fn ($q, $v) => $q->where('grade', $v))
            ->when($filters['academic_year_id'] ?? null, fn ($q, $v) => $q->where('academic_year_id', $v))
            ->when($filters['major_id'] ?? null, fn ($q, $v) => $q->where('major_id', $v))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['sort_by'] = $filters['sort_by'] ?? 'grade';
        $filters['sort_dir'] = $filters['sort_dir'] ?? 'asc';

        return parent::paginate($filters, $perPage);
    }
}

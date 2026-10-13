<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class StudentRepository extends BaseRepository
{
    protected function model(): string
    {
        return Student::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%")
                ->orWhere('rfid', 'like', "%{$search}%");
        });
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with(['class.academicYear', 'class.major'])
            ->withCount(['attendanceDetails as attendance_details_count'])
            ->withCount(['attendanceDetails as present_count' => fn ($q) => $q->whereIn('status', ['hadir', 'terlambat'])])
            ->when($filters['class_id'] ?? null, fn ($q, $v) => $q->where('class_id', $v))
            ->when($filters['gender'] ?? null, fn ($q, $v) => $q->where('gender', $v))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['sort_by'] = $filters['sort_by'] ?? 'name';
        $filters['sort_dir'] = $filters['sort_dir'] ?? 'asc';

        return parent::paginate($filters, $perPage);
    }
}

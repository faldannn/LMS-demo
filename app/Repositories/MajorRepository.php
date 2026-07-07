<?php

namespace App\Repositories;

use App\Models\Major;
use Illuminate\Database\Eloquent\Builder;

class MajorRepository extends BaseRepository
{
    protected function model(): string
    {
        return Major::class;
    }

    protected function applySearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        });
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->withCount('classes')
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['trashed'] ?? false, fn ($q) => $q->onlyTrashed());
    }
}

<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    abstract protected function model(): string;

    abstract protected function applySearch(Builder $query, ?string $search): Builder;

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    public function newQuery(bool $withTrashed = false): Builder
    {
        $query = $this->model()::query();

        if ($withTrashed && method_exists($this->model(), 'bootSoftDeletes')) {
            $query->withTrashed();
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->newQuery((bool) ($filters['trashed'] ?? false));

        if (!empty($filters['search'])) {
            $query = $this->applySearch($query, $filters['search']);
        }

        $query = $this->applyFilters($query, $filters);

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
    }

    public function all(array $filters = [])
    {
        $query = $this->newQuery();

        if (!empty($filters['search'])) {
            $query = $this->applySearch($query, $filters['search']);
        }

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('name')->get();
    }

    public function find(int $id, bool $withTrashed = false): Model
    {
        $query = $this->newQuery($withTrashed);

        return $query->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model()::create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->fresh();
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    public function restore(int $id): Model
    {
        $model = $this->find($id, true);
        $model->restore();

        return $model->fresh();
    }

    public function forceDelete(int $id): bool
    {
        $model = $this->find($id, true);

        return (bool) $model->forceDelete();
    }
}

<?php

namespace App\Services\MasterData;

use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class BaseMasterDataService
{
    public function __construct(protected BaseRepository $repository)
    {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function exportCollection(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    public function find(int $id, bool $withTrashed = false): Model
    {
        return $this->repository->find($id, $withTrashed);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        return $this->repository->update($model, $data);
    }

    public function delete(Model $model): bool
    {
        return $this->repository->delete($model);
    }

    public function restore(int $id): Model
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(int $id): bool
    {
        return $this->repository->forceDelete($id);
    }
}

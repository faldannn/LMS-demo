<?php

namespace App\Services\MasterData;

use App\Models\Semester;
use App\Repositories\SemesterRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SemesterService extends BaseMasterDataService
{
    public function __construct(SemesterRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_active'])) {
                Semester::query()->update(['is_active' => false]);
            }

            return parent::create($data);
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            if (!empty($data['is_active'])) {
                Semester::query()->where('id', '!=', $model->id)->update(['is_active' => false]);
            }

            return parent::update($model, $data);
        });
    }
}

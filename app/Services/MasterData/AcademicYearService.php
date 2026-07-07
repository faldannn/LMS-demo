<?php

namespace App\Services\MasterData;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Repositories\AcademicYearRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicYearService extends BaseMasterDataService
{
    public function __construct(AcademicYearRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_active'])) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            return parent::create($data);
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            if (!empty($data['is_active'])) {
                AcademicYear::query()->where('id', '!=', $model->id)->update(['is_active' => false]);
            }

            return parent::update($model, $data);
        });
    }

    public function delete(Model $model): bool
    {
        /** @var AcademicYear $model */
        if ($model->classes()->count() > 0) {
            throw new \RuntimeException('Tidak dapat menghapus tahun ajaran yang masih memiliki kelas.');
        }

        return parent::delete($model);
    }
}

<?php

namespace App\Services\MasterData;

use App\Models\Classes;
use App\Repositories\ClassRepository;
use Illuminate\Database\Eloquent\Model;

class ClassService extends BaseMasterDataService
{
    public function __construct(ClassRepository $repository)
    {
        parent::__construct($repository);
    }

    public function delete(Model $model): bool
    {
        /** @var Classes $model */
        if ($model->students()->count() > 0) {
            throw new \RuntimeException('Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }

        return parent::delete($model);
    }
}

<?php

namespace App\Services\MasterData;

use App\Models\Major;
use App\Repositories\MajorRepository;
use Illuminate\Database\Eloquent\Model;

class MajorService extends BaseMasterDataService
{
    public function __construct(MajorRepository $repository)
    {
        parent::__construct($repository);
    }

    public function delete(Model $model): bool
    {
        /** @var Major $model */
        if ($model->classes()->count() > 0) {
            throw new \RuntimeException('Tidak dapat menghapus jurusan yang masih digunakan kelas.');
        }

        return parent::delete($model);
    }
}

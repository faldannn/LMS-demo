<?php

namespace App\Services\MasterData;

use App\Repositories\SubjectRepository;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class SubjectService extends BaseMasterDataService
{
    public function __construct(SubjectRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        // Check if there is a soft-deleted subject with the same code
        $deletedSubject = Subject::onlyTrashed()
            ->where('code', $data['code'])
            ->first();

        if ($deletedSubject) {
            // Restore the deleted subject and update its properties
            $deletedSubject->restore();
            $deletedSubject->update($data);
            return $deletedSubject->fresh();
        }

        return parent::create($data);
    }
}

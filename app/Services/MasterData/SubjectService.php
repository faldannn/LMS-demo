<?php

namespace App\Services\MasterData;

use App\Repositories\SubjectRepository;

class SubjectService extends BaseMasterDataService
{
    public function __construct(SubjectRepository $repository)
    {
        parent::__construct($repository);
    }
}

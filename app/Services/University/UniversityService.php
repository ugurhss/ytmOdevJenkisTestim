<?php

namespace App\Services\University;

use App\Repositories\University\UniversityRepository;
use App\Services\BaseService;

class UniversityService extends BaseService
{

    public function __construct(UniversityRepository $repository)
    {
        parent::__construct($repository);
    }

}

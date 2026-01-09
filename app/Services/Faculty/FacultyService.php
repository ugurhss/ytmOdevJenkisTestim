<?php

namespace App\Services\Faculty;

use App\Repositories\Faculty\FacultyRepository;
use App\Services\BaseService;

class FacultyService extends BaseService
{

    public function __construct(FacultyRepository $repository)
    {
        parent::__construct($repository);
    }

}

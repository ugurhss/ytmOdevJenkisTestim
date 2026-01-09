<?php

namespace App\Services\Department;

use App\Repositories\Department\DepartmentRepository;
use App\Services\BaseService;

class DepartmentService extends BaseService
{

    public function __construct(DepartmentRepository $repository)
    {
        parent::__construct($repository);
    }

}

<?php

namespace App\Repositories\Department;

use App\Models\Department;
use App\Repositories\BaseRepository;

class DepartmentRepository extends BaseRepository
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }


}

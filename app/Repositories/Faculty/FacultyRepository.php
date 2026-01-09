<?php

namespace App\Repositories\Faculty;

use App\Models\Faculty;
use App\Repositories\BaseRepository;

class FacultyRepository extends BaseRepository
{
    public function __construct(Faculty $model)
    {
        parent::__construct($model);
    }


}

<?php

namespace App\Repositories\University;

use App\Models\University;
use App\Repositories\BaseRepository;

class UniversityRepository extends BaseRepository
{
    public function __construct(University $model)
    {
        parent::__construct($model);
    }


}

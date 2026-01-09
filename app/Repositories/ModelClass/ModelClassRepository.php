<?php

namespace App\Repositories\ModelClass;

use App\Models\ClassModel;
use App\Models\GroupAnnouncement;
use App\Repositories\BaseRepository;

class ModelClassRepository extends BaseRepository
{
    public function __construct(ClassModel $model)
    {
        parent::__construct($model);
    }


}

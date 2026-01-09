<?php

namespace App\Services\ModelClass;

use App\Repositories\ModelClass\ModelClassRepository;
use App\Services\BaseService;

class ModelClassService extends BaseService
{

    public function __construct(ModelClassRepository $repository)
    {
        parent::__construct($repository);
    }

}

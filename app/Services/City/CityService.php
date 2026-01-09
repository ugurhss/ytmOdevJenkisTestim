<?php

namespace App\Services\City;

use App\Repositories\City\CityRepository;
use App\Services\BaseService;

class CityService extends BaseService
{

    public function __construct(CityRepository $repository)
    {
        parent::__construct($repository);
    }

}

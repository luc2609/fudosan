<?php

namespace App\Repositories\MasterPropertyType;

use App\Models\MasterPropertyType;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPropertyTypeEloquentRepository extends BaseEloquentRepository implements MasterPropertyTypeRepositoryInterface
{
    public function getModel()
    {
        return MasterPropertyType::class;
    }
}

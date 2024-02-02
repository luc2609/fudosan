<?php

namespace App\Repositories\PropertyStation;

use App\Models\PropertyStation;
use App\Repositories\Base\BaseEloquentRepository;

class PropertyStationEloquentRepository extends BaseEloquentRepository implements PropertyStationRepositoryInterface
{
    public function getModel()
    {
        return PropertyStation::class;
    }
}

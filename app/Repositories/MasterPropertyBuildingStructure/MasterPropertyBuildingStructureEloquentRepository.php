<?php

namespace App\Repositories\MasterPropertyBuildingStructure;

use App\Models\MasterPropertyBuildingStructure;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPropertyBuildingStructureEloquentRepository extends BaseEloquentRepository implements MasterPropertyBuildingStructureRepositoryInterface
{
    public function getModel()
    {
        return MasterPropertyBuildingStructure::class;
    }
}

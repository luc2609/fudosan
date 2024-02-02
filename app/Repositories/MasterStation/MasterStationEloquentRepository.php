<?php

namespace App\Repositories\MasterStation;

use App\Models\MasterStation;
use App\Repositories\Base\BaseEloquentRepository;

class MasterStationEloquentRepository extends BaseEloquentRepository implements MasterStationRepositoryInterface
{
    public function getModel()
    {
        return MasterStation::class;
    }
}

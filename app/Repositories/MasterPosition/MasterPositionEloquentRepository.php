<?php

namespace App\Repositories\MasterPosition;

use App\Models\MasterPosition;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPositionEloquentRepository extends BaseEloquentRepository implements MasterPositionRepositoryInterface
{
    public function getModel()
    {
        return MasterPosition::class;
    }
}

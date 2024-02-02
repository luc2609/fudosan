<?php

namespace App\Repositories\MasterBrokerageFee;

use App\Models\MasterBrokerageFee;
use App\Repositories\Base\BaseEloquentRepository;

class MasterBrokerageFeeEloquentRepository extends BaseEloquentRepository implements MasterBrokerageFeeRepositoryInterface
{
    public function getModel()
    {
        return MasterBrokerageFee::class;
    }

}

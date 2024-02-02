<?php

namespace App\Repositories\MasterPrice;

use App\Models\MasterPrice;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPriceEloquentRepository extends BaseEloquentRepository implements MasterPriceRepositoryInterface
{
    public function getModel()
    {
        return MasterPrice::class;
    }

}

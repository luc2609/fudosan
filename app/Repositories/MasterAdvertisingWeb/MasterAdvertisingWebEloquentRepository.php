<?php

namespace App\Repositories\MasterAdvertisingWeb;

use App\Models\MasterAdvertisingWeb;
use App\Repositories\Base\BaseEloquentRepository;

class MasterAdvertisingWebEloquentRepository extends BaseEloquentRepository implements MasterAdvertisingWebRepositoryInterface
{
    public function getModel()
    {
        return MasterAdvertisingWeb::class;
    }
}

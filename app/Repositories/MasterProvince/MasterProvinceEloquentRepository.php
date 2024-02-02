<?php

namespace App\Repositories\MasterProvince;

use App\Models\MasterProvince;
use App\Repositories\Base\BaseEloquentRepository;

class MasterProvinceEloquentRepository extends BaseEloquentRepository implements MasterProvinceRepositoryInterface
{
    public function getModel()
    {
        return MasterProvince::class;
    }

    public function findByName($name)
    {
        return $this->_model->where('name', $name)->first();
    }
}

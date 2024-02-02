<?php

namespace App\Repositories\MasterField;

use App\Models\MasterField;
use App\Repositories\Base\BaseEloquentRepository;

class MasterFieldEloquentRepository extends BaseEloquentRepository implements MasterFieldRepositoryInterface
{
    public function getModel()
    {
        return MasterField::class;
    }
}

<?php

namespace App\Repositories\MasterAdvertisingForm;

use App\Models\MasterAdvertisingForm;
use App\Repositories\Base\BaseEloquentRepository;

class MasterAdvertisingFormEloquentRepository extends BaseEloquentRepository implements MasterAdvertisingFormRepositoryInterface
{
    public function getModel()
    {
        return MasterAdvertisingForm::class;
    }

}

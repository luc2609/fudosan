<?php

namespace App\Repositories\MasterScheduleRepeat;

use App\Models\MasterScheduleRepeat;
use App\Repositories\Base\BaseEloquentRepository;

class MasterScheduleRepeatEloquentRepository extends BaseEloquentRepository implements MasterScheduleRepeatRepositoryInterface
{
    public function getModel()
    {
        return MasterScheduleRepeat::class;
    }

}

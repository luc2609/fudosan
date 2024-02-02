<?php

namespace App\Repositories\MasterContactReason;

use App\Models\MasterContactReason;
use App\Repositories\Base\BaseEloquentRepository;

class MasterContactReasonEloquentRepository extends BaseEloquentRepository implements MasterContactReasonRepositoryInterface
{
    public function getModel()
    {
        return MasterContactReason::class;
    }
}

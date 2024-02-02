<?php

namespace App\Repositories\MasterPropertyCurrentSituation;

use App\Models\MasterPropertyCurrentSituation;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPropertyCurrentSituationEloquentRepository extends BaseEloquentRepository implements MasterPropertyCurrentSituationRepositoryInterface
{
    public function getModel()
    {
        return MasterPropertyCurrentSituation::class;
    }
}

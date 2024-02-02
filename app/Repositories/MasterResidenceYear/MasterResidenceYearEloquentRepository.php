<?php

namespace App\Repositories\MasterResidenceYear;

use App\Models\MasterResidenceYear;
use App\Repositories\Base\BaseEloquentRepository;

class MasterResidenceYearEloquentRepository extends BaseEloquentRepository implements MasterResidenceYearRepositoryInterface
{
    public function getModel()
    {
        return MasterResidenceYear::class;
    }

}

<?php

namespace App\Repositories\MasterPropertyContractType;

use App\Models\MasterPropertyContractType;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPropertyContractTypeEloquentRepository extends BaseEloquentRepository implements MasterPropertyContractTypeRepositoryInterface
{
    public function getModel()
    {
        return MasterPropertyContractType::class;
    }
}

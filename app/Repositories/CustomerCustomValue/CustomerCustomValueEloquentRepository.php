<?php

namespace App\Repositories\CustomerCustomValue;

use App\Models\CustomerCustomValue;
use App\Repositories\Base\BaseEloquentRepository;

class CustomerCustomValueEloquentRepository extends BaseEloquentRepository implements CustomerCustomValueRepositoryInterface
{
    public function getModel()
    {
        return CustomerCustomValue::class;
    }
}

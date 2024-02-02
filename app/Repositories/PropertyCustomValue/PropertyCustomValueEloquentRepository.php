<?php

namespace App\Repositories\PropertyCustomValue;

use App\Models\PropertyCustomValue;
use App\Repositories\Base\BaseEloquentRepository;

class PropertyCustomValueEloquentRepository extends BaseEloquentRepository implements PropertyCustomValueRepositoryInterface
{
    public function getModel()
    {
        return PropertyCustomValue::class;
    }
}

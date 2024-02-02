<?php

namespace App\Repositories\PropertyFile;

use App\Models\PropertyFile;
use App\Repositories\Base\BaseEloquentRepository;

class PropertyFileEloquentRepository extends BaseEloquentRepository implements PropertyFileRepositoryInterface
{
    public function getModel()
    {
        return PropertyFile::class;
    }
}

<?php

namespace App\Repositories\ProjectProperty;

use App\Models\ProjectProperty;
use App\Repositories\Base\BaseEloquentRepository;

class ProjectPropertyEloquentRepository extends BaseEloquentRepository implements ProjectPropertyRepositoryInterface
{
    public function getModel()
    {
        return ProjectProperty::class;
    }
}

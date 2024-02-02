<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\Base\BaseEloquentRepository;

class RoleEloquentRepository extends BaseEloquentRepository implements RoleRepositoryInterface
{
    public function getModel()
    {
        return Role::class;
    }
}

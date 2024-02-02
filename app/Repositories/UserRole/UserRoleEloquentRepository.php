<?php

namespace App\Repositories\UserRole;

use App\Models\UserRole;
use App\Repositories\Base\BaseEloquentRepository;

class UserRoleEloquentRepository extends BaseEloquentRepository implements UserRoleRepositoryInterface
{
    public function getModel()
    {
        return UserRole::class;
    }

    public function findId($userId)
    {
        return $this->_model->where('user_id', $userId)->first();
    }
}

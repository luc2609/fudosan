<?php

namespace App\Repositories\PasswordSecurity;

use App\Models\PasswordSecurity;
use App\Repositories\Base\BaseEloquentRepository;

class PasswordSecurityEloquentRepository extends BaseEloquentRepository implements PasswordSecurityRepositoryInterface
{
    public function getModel()
    {
        return PasswordSecurity::class;
    }

    public function findOneByUserId($userId, $type)
    {
        return $this->_model->where('user_id', $userId)->where('type', $type)->first();
    }
}

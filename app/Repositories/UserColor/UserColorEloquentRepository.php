<?php

namespace App\Repositories\UserColor;

use App\Models\UserColor;
use App\Repositories\Base\BaseEloquentRepository;

class UserColorEloquentRepository extends BaseEloquentRepository implements UserColorRepositoryInterface
{
    public function getModel()
    {
        return UserColor::class;
    }

    public function getColorByUserID($userId)
    {
        return $this->_model->where('user_id', $userId)->first();
    }
}

<?php

namespace App\Repositories\UserDeviceToken;

use App\Models\UserDeviceToken;
use App\Repositories\Base\BaseEloquentRepository;

class UserDeviceTokenEloquentRepository extends BaseEloquentRepository implements UserDeviceTokenRepositoryInterface
{
    public function getModel()
    {
        return UserDeviceToken::class;
    }

    public function checkDeviceToken($id, $deviceToken)
    {
        $userDeviceToken = $this->_model->where('user_id', $id)->where('device_token', $deviceToken)->first();
        if ($userDeviceToken) {
            return $userDeviceToken;
        }
        return false;
    }

    public function destroyDeviceToken($id, $deviceToken)
    {
        $userDeviceToken = $this->_model->where('user_id', $id)->where('device_token', $deviceToken)->first();
        if ($userDeviceToken) {
            $userDeviceToken->delete();
        }
    }
}

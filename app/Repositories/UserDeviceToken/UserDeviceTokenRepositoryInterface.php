<?php

namespace App\Repositories\UserDeviceToken;

use App\Repositories\Base\BaseRepositoryInterface;

interface UserDeviceTokenRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function checkDeviceToken($id, $deviceToken);

    public function destroyDeviceToken($id, $deviceToken);
}

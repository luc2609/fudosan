<?php

namespace App\Repositories\UserColor;

use App\Repositories\Base\BaseRepositoryInterface;

interface UserColorRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function getColorByUserID($userId);
}

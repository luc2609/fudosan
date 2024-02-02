<?php

namespace App\Repositories\UserRole;

use App\Repositories\Base\BaseRepositoryInterface;

interface UserRoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function findId($userId);
}

<?php

namespace App\Repositories\PasswordSecurity;

use App\Repositories\Base\BaseRepositoryInterface;

interface PasswordSecurityRepositoryInterface extends BaseRepositoryInterface
{
    public function findOneByUserId($userId, $type);
}

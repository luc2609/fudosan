<?php

namespace App\Repositories\PasswordReset;

use App\Repositories\Base\BaseRepositoryInterface;

interface PasswordResetRepositoryInterface extends BaseRepositoryInterface
{
    public function updateOrCreate($email);

    public function findOneByToken($token);

    public function deleteByEmail($email);
}

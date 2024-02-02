<?php

namespace App\Repositories\MasterSalePurpose;

use App\Repositories\Base\BaseRepositoryInterface;

interface MasterSalePurposeRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function checkExistMasterData($name, $id);
}

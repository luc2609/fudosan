<?php

namespace App\Repositories\MasterContactMethod;

use App\Repositories\Base\BaseRepositoryInterface;

interface  MasterContactMethodRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();
    
    public function checkExistMasterData($name, $id);
}

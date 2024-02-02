<?php

namespace App\Repositories\MasterPurchasePurpose;

use App\Repositories\Base\BaseRepositoryInterface;

interface MasterPurchasePurposeRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();
    
    public function checkExistMasterData($name, $id);
}

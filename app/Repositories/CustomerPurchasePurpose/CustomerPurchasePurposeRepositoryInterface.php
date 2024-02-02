<?php

namespace App\Repositories\CustomerPurchasePurpose;

use App\Repositories\Base\BaseRepositoryInterface;

interface CustomerPurchasePurposeRepositoryInterface extends BaseRepositoryInterface
{
    public function findCustomerPurchasePurposeId($customerId, $purchasePurposeId);
}

<?php

namespace App\Repositories\CustomerAdvertisingForm;

use App\Repositories\Base\BaseRepositoryInterface;

interface CustomerAdvertisingFormRepositoryInterface extends BaseRepositoryInterface
{
    public function findCustomerAdvertisingForm($customerId, $advertisingFormId);
}

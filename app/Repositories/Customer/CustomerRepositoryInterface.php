<?php

namespace App\Repositories\Customer;

use App\Repositories\Base\BaseRepositoryInterface;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    public function listInCompany($companyId, $params);

    public function getByAttributes($attributes);

    public function showCustomer($params, $companyId, $id);

    public function getSessionCustomer($token);

    public function findCustomerExist($companyId, $phone = null, $mail = null);
}

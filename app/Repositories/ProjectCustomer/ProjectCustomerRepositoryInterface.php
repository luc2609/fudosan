<?php

namespace App\Repositories\ProjectCustomer;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProjectCustomerRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function getList();

    public function delete($id);
}

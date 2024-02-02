<?php

namespace App\Repositories\Property;

use App\Repositories\Base\BaseRepositoryInterface;

interface PropertyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param $companyId
     * @param $params
     * @return mixed
     */
    public function listInCompany($companyId, $params);

    // Get data by attributes
    public function getByAttributes($attributes);

    // Get one property
    public function get($params, $id, $companyId);

    public function nextBackProperty($params, $id, $companyId);
}

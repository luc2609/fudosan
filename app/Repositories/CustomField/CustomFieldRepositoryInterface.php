<?php

namespace App\Repositories\CustomField;

use App\Repositories\Base\BaseRepositoryInterface;

interface CustomFieldRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function paramsCustomField($patternType, $customField, $companyId);

    public function getListCustomField($companyId, $type);

    public function checkExists($customField, $companyId, $patternType);
}

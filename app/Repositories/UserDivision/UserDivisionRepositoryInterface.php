<?php

namespace App\Repositories\UserDivision;

use App\Repositories\Base\BaseRepositoryInterface;

interface UserDivisionRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function getManagerListOfDivision($divisionId, $params);

    public function deleteDivision($userId, $divisionId);
}

<?php

namespace App\Repositories\Division;

use App\Repositories\Base\BaseRepositoryInterface;

interface DivisionRepositoryInterface extends BaseRepositoryInterface
{
    public function listInCompany($companyId, $params);

    public function findByParams($companyId, $params);

    public function addAvailableManagers($id, $managerID);

    public function destroyManger($id, $managerID);

    public function getAvailableDivisionListOfUser($params, $auth);

    public function getAvailableDivisionListOfManager($id, $params, $auth);

    public function listDivisionVieRole($user, $params);

    public function listDivisionCreatedCalendar($user, $params);

    public function addAvailableUsers($id, $userId);
}

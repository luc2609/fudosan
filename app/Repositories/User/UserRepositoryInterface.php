<?php

namespace App\Repositories\User;

use App\Repositories\Base\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findOneByEmail($email);

    public function getUserListOfDivision($divisionId, $params);

    public function getAvailableManagerListOfDivision($divisionId, $params);

    public function getAvailableUserListOfDivision($divisionId, $params);

    public function listInCompany($companyId, $params);

    public function createUser($request, $auth);

    public function updateDivisionUser($request, $id);

    public function show($userId);

    public function indexCompanyUser($companyId, $params);

    public function findUserExist($companyId, $phone = null, $mail = null);
}

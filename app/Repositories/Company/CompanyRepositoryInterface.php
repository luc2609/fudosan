<?php

namespace App\Repositories\Company;

use App\Repositories\Base\BaseRepositoryInterface;

interface CompanyRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function getList();

    public function getListCompany($request);

    public function getUserCompany($userId);

    //Create Account Admin CMS Company
    public function addAccountAdminCmsCompany($request);

    public function updateAccountCmsCompany($request, $id);

    public function deleteAccountCmsCompany($id);

    public function getListAccountCmsCompany($companyId);

    public function getCompanyDetail();

    public function getCompanyInfo($companyId);

    public function detailCompanySystem($id);

    public function getAccountAdminCompany($id);

    public function getDivisionCompany($id);
}

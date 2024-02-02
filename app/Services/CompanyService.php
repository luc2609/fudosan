<?php

namespace App\Services;

use App\Exports\CompanyExport;
use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\CustomField\CustomFieldRepositoryInterface;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Property\PropertyRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class CompanyService
{
    protected $companyInterface;
    protected $userInterface;
    protected $customFieldInterface;
    protected $projectInterface;
    protected $propertyInterface;
    protected $customerInterface;
    protected $fileService;
    protected $divisionInterface;
    protected $calendarService;
    protected $projectService;

    public function __construct(
        CompanyRepositoryInterface $companyInterface,
        UserRepositoryInterface $userInterface,
        CustomFieldRepositoryInterface $customFieldInterface,
        ProjectRepositoryInterface $projectInterface,
        PropertyRepositoryInterface $propertyInterface,
        CustomerRepositoryInterface $customerInterface,
        FileService $fileService,
        DivisionRepositoryInterface $divisionInterface,
        CalendarService $calendarService,
        ProjectService $projectService
    ) {
        $this->companyInterface = $companyInterface;
        $this->userInterface = $userInterface;
        $this->customFieldInterface = $customFieldInterface;
        $this->projectInterface = $projectInterface;
        $this->propertyInterface = $propertyInterface;
        $this->customerInterface = $customerInterface;
        $this->fileService = $fileService;
        $this->divisionInterface = $divisionInterface;
        $this->calendarService = $calendarService;
        $this->projectService = $projectService;
    }

    public function create($request)
    {
        $fileUrl = null;
        if (isset($request->logo)) {
            $file = $request->logo;
            $filePath = 'public/company/' . $request->name . '/logo';
            $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);
        }
        $params = [
            'name' => $request->name,
            'province' => $request->province,
            'district' => $request->district,
            'street' => $request->street,
            'phone' => $request->phone,
            'website' => $request->website,
            'address' => $request->address,
            'commission_rate' => $request->commission_rate ?? COMPANY_COMMISSION_RATE,
            'status' => true,
            'note' => $request->note,
            'logo_image' => $fileUrl == null ? null : $fileUrl
        ];
        $this->companyInterface->create($params);
        return _success(null, __('message.created_success'), HTTP_SUCCESS);
    }

    public function update($request, $id)
    {
        $params = [
            'name' => $request->name,
            'province' => $request->province,
            'district' => $request->district,
            'street' => $request->street,
            'phone' => $request->phone,
            'website' => $request->website,
            'address' => $request->address,
            'status' => $request->status,
            'commission_rate' => $request->commission_rate,
            'note' => $request->note
        ];
        $company = $this->companyInterface->find($id);
        if (isset($request->logo)) {
            if ($request->logo == "default") {
                $params['logo_image'] = null;
                if ($company->logo_image) {
                    $this->fileService->deleteFileS3($company->logo_image);
                }
            } else {
                if ($company->logo_image) {
                    $this->fileService->deleteFileS3($company->logo_image);
                }
                $file = $request->logo;
                $filePath = 'public/company/' . $request->name . '/logo';
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);
                $params['logo_image'] = $fileUrl;
            }
        }
        $this->companyInterface->update($id, $params);
        return _success(null, __('message.update_success'), HTTP_SUCCESS);
    }

    public function destroy($id)
    {
        try {
            $company = $this->companyInterface->find($id);
            if (!$company) {
                return _error(null, __('message.not_found'), HTTP_SUCCESS);
            }
            // delete calendar relate to company
            $this->calendarService->deleteCalendarCompany($id);

            // delete project relate to company
            $this->projectService->deleteProjectCompany($id);

            // delete property
            $company->property()->delete();

            // delete customer
            $company->customer()->delete();

            // delete user
            $company->users()->delete();

            // delete company
            $this->companyInterface->delete($id);
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem($e);
        }
    }

    public function index($request)
    {
        $pageSize = $request->page_size;
        $listCompanies =  $this->companyInterface->getListCompany($request)->paginate($pageSize);
        $data = [
            'companies' => $listCompanies->items(),
            'items_total' => $listCompanies->total()
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function show($id)
    {
        $data = $this->companyInterface->getCompanyInfo($id);
        return _success($data, __('message.get_success'), HTTP_SUCCESS);
    }

    public function getUserCompany($userId)
    {
        $data = $this->companyInterface->getUserCompany($userId);
        if ($data) {
            return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
        } else {
            return _success(null, __('message.not_found'), HTTP_SUCCESS);
        }
    }

    public function addAccountAdmin($request)
    {
        return $this->companyInterface->addAccountAdminCmsCompany($request);
    }

    public function updateAccountAdmin($request, $id)
    {
        $updateAccountAdmin = $this->companyInterface->updateAccountCmsCompany($request, $id);
        if (!$updateAccountAdmin) {
            return _error(null, __('message.updated_fail'), HTTP_BAD_REQUEST);
        }
        return _success($updateAccountAdmin, __('message.updated_success'), HTTP_CREATED);
    }

    public function deleteAccountAdmin($id)
    {
        $deleteAccount = $this->companyInterface->deleteAccountCmsCompany($id);
        if (!$deleteAccount) {
            return _error(null, __('message.deleted_fail'), HTTP_SUCCESS);
        }
        return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
    }

    public function getListAccountAdmin($request)
    {
        $companyId = auth()->user()->company;
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $users =  $this->companyInterface->getListAccountCmsCompany($companyId)->paginate($pageSize);
        $data = [
            'users' => $users->items(),
            'items_total' =>  $users->total()
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function showAccountAdmin($id)
    {
        $user = $this->userInterface->find($id);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        return _success($user, __('message.get_success'), HTTP_SUCCESS);
    }

    public function curdCustomField($request)
    {
        $auth = auth()->user();
        $companyId = $auth->company;
        $customFieldParams = [];
        if (isset($request->custom_fields)) {
            $customFieldParams = $request->custom_fields;
        }
        $patternType = $request->pattern_type;
        $currentCustomFieldIds = $this->customFieldInterface->getListCustomField($companyId, $patternType)->pluck('id')->toArray();
        $updateCustomFieldIds = [];
        $nameCustomField = array_column($customFieldParams, 'name');
        $isExists = false;
        for ($i = 0; $i < count($nameCustomField); ++$i) {
            for ($j = $i + 1; $j < count($nameCustomField); ++$j) {
                if ($nameCustomField[$i] == $nameCustomField[$j]) {
                    $isExists = true;
                    break;
                }
            }
        }
        if ($isExists) {
            return _error(null, __('message.field_already_exists'), HTTP_SUCCESS);
        }

        foreach ($customFieldParams  as $customField) {
            if (is_string($customField)) {
                $customField = json_decode($customField, true);
            }
            $attributes = $this->customFieldInterface->paramsCustomField($patternType, $customField, $companyId);
            if (isset($customField['id'])) {
                if (!($this->customFieldInterface->find($customField['id']))) {
                    return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
                }
                $this->customFieldInterface->update($customField['id'], $attributes);
                array_push($updateCustomFieldIds, $customField['id']);
            } else {
                $checkExists = $this->customFieldInterface->checkExists($customField, $companyId, $patternType);
                if ($checkExists) {
                    return _error(null, __('message.field_already_exists'), HTTP_SUCCESS);
                }
                $this->customFieldInterface->create($attributes);
            }
        }
        $deleteCustomFieldIds = array_diff($currentCustomFieldIds, $updateCustomFieldIds);
        foreach ($deleteCustomFieldIds as  $deleteCustomFieldId) {
            $this->customFieldInterface->delete($deleteCustomFieldId);
        }
        return _success(null, __('message.create_custom_field_success'), HTTP_CREATED);
    }

    // Get dashboard total
    public function dashboardTotal()
    {
        $totalCompanies = $this->companyInterface->all()->count();
        $totalProjects = $this->projectInterface->all()->count();
        $totalProperties = $this->propertyInterface->all()->count();
        $totalCustomers = $this->customerInterface->all()->count();

        $data = [
            'total_company' => $totalCompanies,
            'total_project' => $totalProjects,
            'total_property' => $totalProperties,
            'total_customers' => $totalCustomers
        ];

        return _success($data, __('message.get_total_success'), HTTP_SUCCESS);
    }

    public function getCustomField($request, $patternType)
    {
        $companyId = auth()->user()->company;
        $pageSize = $request->page_size ?? PAGE_SIZE;

        if ($patternType == CUSTOMER) {
            $patternType = [CUSTOMER];
        } else {
            $patternType = [PROPERTY];
        }

        $customFields = $this->customFieldInterface->getListCustomField($companyId, $patternType)->paginate($pageSize);
        $data = [
            'custom_fields' => $customFields->items(),
            'items_total' => $customFields->total()
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function getCompanyDetail()
    {
        $data = $this->companyInterface->getCompanyDetail()->get();

        return _success($data, __('message.show_info_success'), HTTP_SUCCESS);
    }

    public function detailAdminCompanySystem($request, $id)
    {
        $pageSize = $request->page_size;
        $detailCompanySystem = $this->companyInterface->detailCompanySystem($id);
        $users = $this->companyInterface->getAccountAdminCompany($id)->paginate($pageSize);

        $data = [
            'companies' => $detailCompanySystem,
            'users' => $users->items(),
            'total_users' => $users->total()
        ];
        return _success($data, __('message.show_info_success'), HTTP_SUCCESS);
    }

    public function detailDivisionCompanySystem($request, $id)
    {
        $detailCompanySystem = $this->companyInterface->detailCompanySystem($id);
        $getDivisionCompany = $this->companyInterface->getDivisionCompany($id);
        $myCollectionObj = collect($getDivisionCompany);
        $perPage = $request->page_size ?? PAGE_SIZE;
        $page = $request->page;
        $divisions = $this->paginate($myCollectionObj, $perPage, $page);
        $division = $divisions->items();
        if ($page != 1) {
            $division = array_values($division);
        }
        $data = [
            'companies' => $detailCompanySystem,
            'divisions' => $division,
            'total_divisions' => $divisions->total()
        ];
        return _success($data, __('message.show_info_success'), HTTP_SUCCESS);
    }

    // Export list Property in comany
    public function export($request)
    {
        $companies = $this->companyInterface->getListCompany($request)->get();

        $currentDate = date('Ymd');
        $userId = auth()->user()->id;
        $fileName =  $currentDate . __('filename.export_company');
        $filePath = 'company/' . $userId . '/' . $fileName;

        $exportedObject = new CompanyExport($companies);
        $link = $this->fileService->saveFileToS3($filePath, $exportedObject);
        $data = ['link' => $link];
        return _success($data, __('message.export_success'), HTTP_SUCCESS);
    }

    public function paginate($items, $perPage, $page)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return  new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page);
    }

    public function  destroyDivision($id)
    {
        $division =  $this->divisionInterface->find($id);
        if (!$division) {
            return _error(null, __('message.not_found'), HTTP_SUCCESS);
        }
        // Check user count, and manager count in division

        if ($division->user_count + $division->manager_count) {
            return _error(null, __('message.division_not_empty'), HTTP_SUCCESS);
        }

        $deleteDivision = $this->divisionInterface->delete($id);
        if ($deleteDivision) {
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        }
        return _error(null, __('message.deleted_fail'), HTTP_SUCCESS);
    }
}

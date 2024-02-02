<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAccountAdminCompanyRequest;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\CurdCustomFieldRequest;
use App\Http\Requests\GetListAccountCompanyRequest;
use App\Http\Requests\GetListCompanyRequest;
use App\Http\Requests\UpdateAccountAdminCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Services\CompanyService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function store(CreateCompanyRequest $request)
    {
        try {
            return $this->companyService->create($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            return $this->companyService->update($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function destroy($id)
    {
        try {
            return $this->companyService->destroy($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function index(GetListCompanyRequest $request)
    {
        try {
            return $this->companyService->index($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function show($id)
    {
        try {
            return $this->companyService->show($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function detail()
    {
        try {
            $userId = auth()->user()->id;
            return $this->companyService->getUserCompany($userId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function addAccountAdminCmsCompany(CreateAccountAdminCompanyRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->companyService->addAccountAdmin($request);
            DB::commit();
            return _success($data, __('message.created_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function updateAccountCmsCompany(UpdateAccountAdminCompanyRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->companyService->updateAccountAdmin($request, $id);
            DB::commit();
            return  $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function deleteAccountCmsCompany($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->companyService->deleteAccountAdmin($id);
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function getListAccountCmsCompany(GetListAccountCompanyRequest $request)
    {
        try {
            return $this->companyService->getListAccountAdmin($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function showAccountCmsCompany($id)
    {
        try {
            return $this->companyService->showAccountAdmin($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function curdCustomField(CurdCustomFieldRequest $request)
    {
        try {
            return $this->companyService->curdCustomField($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function getCustomField(Request $request, $patternType)
    {
        try {
            return $this->companyService->getCustomField($request, $patternType);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardTotal()
    {
        try {
            return $this->companyService->dashboardTotal();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    //Count number of users and projects of company
    public function getCompanyDetail()
    {
        try {
            return $this->companyService->getCompanyDetail();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    public function detailAdminCompanySystem(Request $request, $id)
    {
        try {
            return $this->companyService->detailAdminCompanySystem($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    public function detailDivisionCompanySystem(Request $request, $id)
    {
        try {
            return $this->companyService->detailDivisionCompanySystem($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function export(Request $request)
    {
        try {
            return $this->companyService->export($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function destroyDivision($id)
    {
        try {
            return $this->companyService->destroyDivision($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

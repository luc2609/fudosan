<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddAvailableDivisionRequest;
use App\Http\Requests\ChangeAuthencationCmsRequest;
use App\Http\Requests\CreateUserColorRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Exception;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use App\Http\Requests\GetListProjectUserRequest;
use App\Http\Requests\ImportEmployeeCsvRequest;
use App\Http\Requests\ImportUserCsvRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\EmployeeImport;
use App\Imports\UserImport;
use App\Imports\ValidateEmployeeImport;
use App\Imports\ValidateUserImport;
use App\Services\CertificateService;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $authService;
    protected $userService;
    protected $certificateService;
    protected $fileService;

    public function __construct(
        AuthService $authService,
        UserService $userService,
        CertificateService $certificateService,
        FileService $fileService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->certificateService = $certificateService;
        $this->fileService = $fileService;
    }

    // List information of employee in company
    public function index(Request $request, $roleId)
    {
        try {
            return $this->userService->list($request, $roleId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    public function store(CreateUserRequest $request, $roleId)
    {
        DB::beginTransaction();
        try {
            $auth = auth()->user();
            $user = $this->userService->create($request, $auth, $roleId);
            DB::commit();
            return _success($user, __('message.created_success'), HTTP_CREATED);;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Export list users in company
    public function export(Request $request, $roleId)
    {
        try {
            $params = $request->all();
            $auth = auth()->user();
            return $this->userService->export($params, $auth, $roleId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Detail employee
    public function show($id)
    {
        try {
            return $this->userService->show($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get all available divisions add employee
    public function getAddAvailableDivisions(Request $request, $id)
    {
        try {
            $auth = auth()->user();
            return $this->userService->getAddAvailableDivisions($request, $id, $auth);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Add available divisions
    public function addAvailableDivisions(AddAvailableDivisionRequest $request, $id)
    {
        try {
            $auth = auth()->user();
            return $this->userService->addAvailableDivisions($request, $id, $auth);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Destroy account
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            return $this->userService->delete($id, $user);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $data = $this->userService->update($request, $id, $user);
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Update Avatar
    public function updateAvatar(Request $request, $id)
    {
        try {
            $auth = auth()->user();
            return $this->userService->updateAvatar($request, $id, $auth);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function indexProject(GetListProjectUserRequest $request, $id)
    {
        try {
            return $this->userService->indexProject($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // CURD certificate for App
    public function curdCertificateApp(Request $request, $userId)
    {
        try {
            $params = $request->all();
            $curdCertificate = $this->certificateService->curdCertificateApp($params, $userId);
            if ($curdCertificate) {
                return $curdCertificate;
            }
            return _success(null, __('message.updated_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // all user in company
    public function indexCompanyUser(Request $request)
    {
        try {
            $params = $request->all();
            return $this->userService->indexCompanyUser($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function indexDivisionUser(Request $request)
    {
        try {
            return $this->userService->indexDivisionUser($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function importUser(ImportUserCsvRequest $request)
    {
        DB::beginTransaction();
        try {
            $validator = new ValidateUserImport();
            $fileImport = new UserImport;
            $data = $this->fileService->importFile($request, $validator, $fileImport);
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Show divisions
    public function showDivisions($id, Request $request)
    {
        try {
            return $this->userService->showDivisions($id, $request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Delete available division
    public function destroyAvailableDivisions($id, $divisionId)
    {
        try {
            $auth = auth()->user();
            return $this->userService->destroyAvailableDivision($id, $divisionId, $auth);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function totalNotifies()
    {
        try {
            return $this->userService->countTotalNotifies();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }


    public function exportEmployee(Request $request)
    {
        try {
            return $this->userService->exportEmployee($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    public function importEmployee(ImportEmployeeCsvRequest $request)
    {
        DB::beginTransaction();
        try {
            $validator = new ValidateEmployeeImport();
            $fileImport = new EmployeeImport;
            $data = $this->fileService->importFile($request, $validator, $fileImport);
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }


    public function createEmployee(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $auth = auth()->user();
            $user = $this->userService->createEmployee($request, $auth);
            DB::commit();
            return _success($user, __('message.created_success'), HTTP_CREATED);;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function changeAuthenticationCms(ChangeAuthencationCmsRequest $request)
    {
        try {
            $user = $this->userService->changeAuthenticationCms($request);
            return _success($user, __('message.created_success'), HTTP_CREATED);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function createUserColor(CreateUserColorRequest $request)
    {
        try {
            $user = $this->userService->createUserColor($request);
            return _success($user, __('message.created_success'), HTTP_CREATED);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

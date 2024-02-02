<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DivisionRequest;
use App\Http\Requests\GetListProjectDivisionRequest;
use App\Http\Requests\ImportDivisionCsvRequest;
use App\Http\Requests\UserAvailableRequest;
use App\Imports\DivisionImport;
use App\Imports\ValidateDivisionImport;
use App\Services\DivisionService;
use App\Services\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DivisionController extends Controller
{
    protected $divisionService;
    protected $fileService;

    public function __construct(DivisionService $divisionService, FileService $fileService)
    {
        $this->divisionService = $divisionService;
        $this->fileService = $fileService;
    }

    // List division in company
    public function index(Request $request)
    {
        try {
            return $this->divisionService->list($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Export list division in company
    public function export(Request $request)
    {
        try {
            $params = $request->all();

            return $this->divisionService->export($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // List division in company vie role
    public function indexListDivision(Request $request)
    {
        try {
            $params = $request->all();
            return $this->divisionService->listDivisionVieRole($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    // Get managers of division
    public function showManagersOfDivision($id, Request $request)
    {
        try {
            $params = $request->all();

            return $this->divisionService->showManagersOfDivision($id, $params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get users of division
    public function showUsersOfDivision($id, Request $request)
    {
        try {
            $params = $request->all();

            return $this->divisionService->showUsersOfDivision($id, $params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get all manager and user of division
    public function showEmployeeOfDivison($id, Request $request)
    {
        try {
            return $this->divisionService->showEmployeeOfDivison($id, $request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Create divison
    public function store(DivisionRequest $request)
    {
        try {
            $name = $request->name;

            return $this->divisionService->create($name);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Update division
    public function update(DivisionRequest $request, $id)
    {
        try {
            $data = $request->all();

            return $this->divisionService->update($id, $data);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Delete division
    public function destroy($id)
    {
        try {
            return $this->divisionService->delete($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Show available managers
    public function showAvailableManagers($id, Request $request)
    {
        try {
            return $this->divisionService->showAvailableManagers($id, $request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Add available manager
    public function addAvailableManagers($id, UserAvailableRequest $request)
    {
        try {
            $managerID = $request->user_id;

            return $this->divisionService->addAvailableManagers($id, $managerID);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Delete available manager
    public function destroyAvailableManagers($id, UserAvailableRequest $request)
    {
        try {
            $managerID = $request->user_id;

            return $this->divisionService->destroyAvailableManagers($id, $managerID);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Show available users
    public function showAvailableUsers($id, Request $request)
    {
        try {
            $params = $request->all();

            return $this->divisionService->showAvailableUsers($id, $params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Add available user
    public function addAvailableUsers($id, UserAvailableRequest $request)
    {
        try {
            $userId = $request->user_id;

            return $this->divisionService->addAvailableUsers($id, $userId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Delete available user
    public function destroyAvailableUsers($id, UserAvailableRequest $request)
    {
        try {
            $userId = $request->user_id;
            return $this->divisionService->destroyAvailableUsers($id, $userId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Project division
    public function indexProject(GetListProjectDivisionRequest $request, $id)
    {
        try {
            return $this->divisionService->indexProject($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function importDivision(ImportDivisionCsvRequest $request)
    {
        DB::beginTransaction();
        try {
            $validator = new ValidateDivisionImport();
            $fileImport = new DivisionImport;
            $data = $this->fileService->importFile($request, $validator, $fileImport);
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

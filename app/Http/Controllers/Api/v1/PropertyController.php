<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckDuplicateProperty;
use App\Http\Requests\CreatePropertyRequest;
use App\Http\Requests\GetListProjectPropertyRequest;
use App\Http\Requests\ImportPropertyCsvRequest;
use App\Http\Requests\ShowBrokerageFeeRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Imports\PropertyImport;
use App\Imports\ValidatePropertyImport;
use App\Services\FileService;
use App\Services\PropertyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    protected $propertyService;
    protected $fileService;

    public function __construct(
        PropertyService $propertyService,
        FileService $fileService
    ) {
        $this->propertyService = $propertyService;
        $this->fileService = $fileService;
    }

    // Get list property in company
    public function index(Request $request)
    {
        try {
            $params = $request->all();

            return $this->propertyService->list($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Export list property in company
    public function export(Request $request)
    {
        try {
            $params = $request->all();

            return $this->propertyService->export($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Check duplicate property
    public function checkDuplicate(CheckDuplicateProperty $request)
    {
        try {
            $params = $request->all();
            $checkDuplicate = $this->propertyService->checkDuplicate($params, null);
            if ($checkDuplicate) {
                return $checkDuplicate;
            }

            return _success(null, __('message.success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Create property
    public function store(CreatePropertyRequest $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $checkDuplicate = $this->propertyService->checkDuplicate($params, null);
            if ($checkDuplicate) {
                return $checkDuplicate;
            }
            $data = $this->propertyService->create($params);
            DB::commit();

            return _success($data, __('message.created_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Update property
    public function update($id, UpdatePropertyRequest $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $checkDuplicate = $this->propertyService->checkDuplicate($params, $id);
            $checkProperty = $this->propertyService->checkProperty($id);
            $checkFiles = $this->propertyService->checkFiles($id, $params);
            if ($checkDuplicate) {
                $response = $checkDuplicate;
            } else if ($checkProperty) {
                $response = $checkProperty;
            } else if ($checkFiles) {
                $response = $checkFiles;
            } else {
                $data = $this->propertyService->update($id, $params);
                DB::commit();
                $response = _success($data, __('message.updated_success'), HTTP_SUCCESS);
            }

            return $response;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Show Brokerage Fee
    public function showBrokerageFee(ShowBrokerageFeeRequest $request)
    {
        try {
            $params = $request->all();

            return $this->propertyService->showBrokerageFee($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Destroy property
    public function destroy($id)
    {
        try {
            return $this->propertyService->delete($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Show property
    public function show(Request $request, $id)
    {
        try {
            return $this->propertyService->show($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Project property
    public function indexProject(GetListProjectPropertyRequest $request, $id)
    {
        try {
            return $this->propertyService->indexProject($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function importProperty(ImportPropertyCsvRequest $request)
    {
        DB::beginTransaction();
        try {
            $validator = new ValidatePropertyImport();
            $fileImport = new PropertyImport;
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

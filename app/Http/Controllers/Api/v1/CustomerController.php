<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Exception;
use App\Http\Requests\CheckDuplicateCustomer;
use App\Http\Requests\GetListProjectCustomerRequest;
use App\Http\Requests\ImportCustomerCsvRequest;
use App\Imports\CustomerImport;
use App\Imports\ValidateCustomerImport;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    protected $customerService;
    protected $fileService;

    public function __construct(
        CustomerService $customerService,
        FileService $fileService
    ) {
        $this->customerService = $customerService;
        $this->fileService = $fileService;
    }

    // Get list customer in company
    public function index(Request $request)
    {
        try {
            $params = $request->all();

            return $this->customerService->list($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list customer in company v2
    public function listCustomer(Request $request)
    {
        try {
            $params = $request->all();
            return $this->customerService->list($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Check duplicate property
    public function checkDuplicate(CheckDuplicateCustomer $request)
    {
        try {
            $params = $request->all();
            $checkDuplicate = $this->customerService->checkDuplicate($params, null);
            if ($checkDuplicate) {
                return $checkDuplicate;
            }
            $token = $this->customerService->createSessionCustomer($params);
            $data = ['token' => $token];
            return _success($data, __('success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Create customer
    public function store(CustomerRequest $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $data = $this->customerService->create($params);
            $this->customerService->deleteSessionCustomer($params['token']);
            DB::commit();
            return _success($data, __('message.created_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            return $this->customerService->showCustomer($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Update customer
    public function update(UpdateCustomerRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $checkCustomer = $this->customerService->checkCustomer($id);
            $checkDuplicate = $this->customerService->checkDuplicate($params, $id);
            if ($checkCustomer) {
                $res = $checkCustomer;
            } else if ($checkDuplicate) {
                DB::rollBack();
                $res = $checkDuplicate;
            } else {
                $data = $this->customerService->updateCustomer($id, $params);
                DB::commit();
                $res = _success($data, __('message.updated_success'), HTTP_SUCCESS);
            }
            return $res;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            return $this->customerService->deleteCustomer($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Export list customer in company
    public function export(Request $request)
    {
        try {
            $params = $request->all();

            return $this->customerService->export($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // QRcode for customer
    public function qrcode($token)
    {
        try {
            return $this->customerService->qrCode($token);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get Session Customer
    public function getSessionCustomer($token)
    {
        try {
            return $this->customerService->getSessionCustomer($token);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    // Project Customer
    public function indexProject(GetListProjectCustomerRequest $request, $id)
    {
        try {
            return $this->customerService->indexProject($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function importCustomer(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = new ValidateCustomerImport();
            $fileImport = new CustomerImport;
            $importCustomer = $this->fileService->importFile($request, $validator, $fileImport);
            DB::commit();
            return $importCustomer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

<?php

namespace App\Services;

use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\CustomerCustomValue\CustomerCustomValueRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectCustomer\ProjectCustomerRepositoryInterface;
use App\Exports\CustomerExport;
use App\Models\SessionCustomer;
use App\Repositories\CustomerAdvertisingForm\CustomerAdvertisingFormRepositoryInterface;
use App\Repositories\CustomerPurchasePurpose\CustomerPurchasePurposeRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use DateTime;

class CustomerService
{
    protected $customerInterface;
    protected $userInterface;
    protected $fileService;
    protected $projectInterface;
    protected $projectCustomerInterface;
    protected $customerCustomValuesInterface;
    protected $customerAdvertisingFormRepositoryInterface;
    protected $customerPurchasePurposeRepositoryInterface;

    public function __construct(
        CustomerRepositoryInterface $customerInterface,
        UserRepositoryInterface $userInterface,
        FileService $fileService,
        ProjectRepositoryInterface $projectInterface,
        ProjectCustomerRepositoryInterface $projectCustomerInterface,
        CustomerCustomValueRepositoryInterface $customerCustomValuesInterface,
        CustomerAdvertisingFormRepositoryInterface $customerAdvertisingFormRepositoryInterface,
        CustomerPurchasePurposeRepositoryInterface $customerPurchasePurposeRepositoryInterface
    ) {
        $this->customerInterface = $customerInterface;
        $this->userInterface = $userInterface;
        $this->fileService = $fileService;
        $this->projectInterface = $projectInterface;
        $this->projectCustomerInterface = $projectCustomerInterface;
        $this->customerCustomValuesInterface = $customerCustomValuesInterface;
        $this->customerAdvertisingFormRepositoryInterface = $customerAdvertisingFormRepositoryInterface;
        $this->customerPurchasePurposeRepositoryInterface = $customerPurchasePurposeRepositoryInterface;
    }

    // List customer in company
    public function list($params)
    {
        $companyId = auth()->user()->company;
        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $listCustomers = $this->customerInterface->listInCompany($companyId, $params)->paginate($pageSize);
        return _success($listCustomers, __('message.customer_list_success'), HTTP_SUCCESS);
    }

    // Export list Property in comany
    public function export($params)
    {
        $companyId = auth()->user()->company;
        $customers = $this->customerInterface->listInCompany($companyId, $params)->get();
        $currentDate = date('Ymd');
        $userId = auth()->user()->id;
        $fileName = $currentDate .  __('filename.export_customer');
        $filePath = 'customer/' . $userId . '/' . $fileName;

        $exportedObject = new CustomerExport($customers);
        $link = $this->fileService->saveFileToS3($filePath, $exportedObject);
        $data = ['link' => $link];

        return _success($data, __('message.export_success'), HTTP_SUCCESS);
    }

    // Show  Customer
    public function showCustomer($request, $id)
    {
        $checkCustomer = $this->checkCustomer($id);
        if ($checkCustomer) {
            return $checkCustomer;
        }
        $companyId = auth()->user()->company;
        $params = $request->all();
        $customer = $this->customerInterface->showCustomer($params, $companyId, $id);

        return _success($customer, __('message.show_success'), HTTP_SUCCESS);
    }

    // Delete Customer
    public function deleteCustomer($id)
    {
        $checkCustomer = $this->checkCustomer($id);
        if ($checkCustomer) {
            return $checkCustomer;
        }

        $checkCustomerInProject = $this->checkCustomerInProject($id);
        if ($checkCustomerInProject) {
            return $checkCustomerInProject;
        }
        $customer = $this->customerInterface->find($id);
        $customer->customerCustomValues()->delete();
        $this->customerInterface->delete($id);
        return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
    }

    // Check duplicate customer
    public function checkDuplicate($params, $customerId)
    {
        $user = auth()->user();
        $companyId = $user->company;
        $attributesCheckDuplicate = [
            'last_name' => $params['last_name'] ?? '',
            'first_name' => $params['first_name'] ?? '',
            // 'birthday' => $params['birthday'] ?? '',
            'phone' => $params['phone'] ?? '',
            'company_id' => $companyId,
        ];
        $listCustomerCheckDuplicate =  $this->customerInterface->getByAttributes($attributesCheckDuplicate);
        if ($listCustomerCheckDuplicate->count() > 0) {
            if ($listCustomerCheckDuplicate[0]->id == $customerId) {
                return false;
            }

            $data = [
                'id' =>  $listCustomerCheckDuplicate[0]->id
            ];

            return _error($data, __('message.duplicate_customer'), HTTP_SUCCESS);
        }

        return false;
    }

    //Create session customer
    public function createSessionCustomer($params)
    {
        $saveTemp = SessionCustomer::create([
            'last_name' => $params['last_name'] ?? '',
            'first_name' => $params['first_name'] ?? '',
            'kana_last_name' => $params['kana_last_name'] ?? '',
            'kana_first_name' => $params['kana_first_name'] ?? '',
            'gender' => $params['gender'] ?? null,
            'phone' => $params['phone'] ?? '',
            'birthday' => $params['birthday'] ?? '',
            'token' => Str::random(32),
            'bearer_token' => request()->bearerToken(),
        ]);
        return $saveTemp['token'];
    }

    // Delete session Customer
    public function deleteSessionCustomer($token)
    {
        return $this->customerInterface->deleteSessionCustomer($token);
    }

    // Create customer
    public function create($params)
    {
        $purchaseTime = isset($params['purchase_time']) ? new DateTime($params['purchase_time']) : null;
        $authUser = auth()->user();
        $createdId = $authUser->id;
        $companyId = $authUser->company;
        $token = $params['token'];
        $sessionCustomer = $this->customerInterface->getSessionCustomer($token);
        $attributes = [
            'last_name' => $params['last_name'],
            'first_name' => $params['first_name'],
            'kana_last_name' => $params['kana_last_name'],
            'kana_first_name' => $params['kana_first_name'],
            // 'gender' => $sessionCustomer['gender'],
            'phone' => $params['phone'],
            'birthday' => $params['birthday'] ?? null,
            'postal_code' => $params['postal_code'] ?? '',
            'email' => $params['email'] ?? '',
            'address' => $params['address'] ?? '',
            'province' => $params['province'] ?? '',
            'district' => $params['district'] ?? '',
            'residence_year_id' => $params['residence_year_id'] ?? null,
            'budget' => $params['budget'] ?? null,
            'deposit' => $params['deposit'] ?? null,
            'purchase_time' => $purchaseTime,
            'contact_method_id' => $params['contact_method_id'] ?? null,
            'memo' => $params['memo'] ?? null,
            'status' => 2,
            'create_by_id' => $createdId,
            'company_id' => $companyId
        ];
        $customer = $this->customerInterface->create($attributes);
        $customerId = $customer->id;
        // advertising form
        if (isset($params['advertising_form_ids'])) {
            if (is_array($params['advertising_form_ids'])) {
                $customer->advertisingForms()->attach($params['advertising_form_ids']);
            }
            foreach (($params['advertising_form_ids']) as $advertisingFormId) {
                if ($advertisingFormId == OTHER_ADVERTISING_FORM_ID && isset($params['other_advertising_form'])) {
                    $customerAdvertisingFormId = $this->customerAdvertisingFormRepositoryInterface->findCustomerAdvertisingForm($customerId, $advertisingFormId)->id;
                    $attributes = ['note_other' => $params['other_advertising_form']];
                    $this->customerAdvertisingFormRepositoryInterface->update($customerAdvertisingFormId, $attributes);
                }
            }
        }

        // purchase purpose
        if (isset($params['purchase_purpose_ids'])) {
            if (is_array($params['purchase_purpose_ids'])) {
                $customer->purchasePurposes()->attach($params['purchase_purpose_ids']);
            }
            foreach (($params['purchase_purpose_ids']) as $purchasePurposeId) {

                if ($purchasePurposeId == OTHER_PURCHASE_PURPOSE_ID && isset($params['other_purchase_purpose'])) {
                    $customerPurchasePurposeId = $this->customerPurchasePurposeRepositoryInterface->findCustomerPurchasePurposeId($customerId, $purchasePurposeId)->id;
                    $attributes = ['note_other' => $params['other_purchase_purpose']];
                    $this->customerPurchasePurposeRepositoryInterface->update($customerPurchasePurposeId, $attributes);
                }
            }
        }

        // custom fields
        $customFieldParams = [];
        if (isset($params['custom_fields'])) {
            $customFieldParams = $params['custom_fields'];
        }
        foreach ($customFieldParams  as $customField) {
            if (is_string($customField)) {
                $customField = json_decode($customField, true);
            }
            $params = [
                'custom_field_id' => $customField['custom_field_id'],
                'value' => $customField['value'],
                'customer_id' => $customerId
            ];
            $this->customerCustomValuesInterface->create($params);
        }
        return [
            'id' => $customerId
        ];
    }

    // Update Customer
    public function updateCustomer($id, $params)
    {
        $customer = $this->customerInterface->find($id);
        $purchaseTime = isset($params['purchase_time']) ? new DateTime($params['purchase_time']) : null;

        $attributes = [
            'last_name' => $params['last_name'],
            'first_name' => $params['first_name'],
            'kana_last_name' => $params['kana_last_name'],
            'kana_first_name' => $params['kana_first_name'],
            'gender' => $params['gender'] ?? null,
            'phone' => $params['phone'] ?? '',
            'birthday' => $params['birthday'] ?? null,
            'postal_code' => $params['postal_code'] ?? '',
            'province' => $params['province'] ?? '',
            'district' => $params['district'] ?? '',
            'email' => $params['email'] ?? '',
            'address' => $params['address'] ?? '',
            'residence_year_id' => $params['residence_year_id'] ?? null,
            'budget' => $params['budget'] ?? null,
            'deposit' => $params['deposit'] ?? null,
            'purchase_time' => $purchaseTime,
            'contact_method_id' => $params['contact_method_id'] ?? null,
            'memo' => $params['memo'] ?? null
        ];

        // advertising forms
        if (isset($params['advertising_form_ids'])) {
            $currentAdvertisingFormIds = $customer->advertisingForms()->pluck('advertising_form_id')->toArray();
            $newAdvertisingFormIds =  $params['advertising_form_ids'];

            $addAdvertisingFormIds = array_diff($newAdvertisingFormIds, $currentAdvertisingFormIds);
            $deleteAdvertisingFormIds = array_diff($currentAdvertisingFormIds, $newAdvertisingFormIds);

            if (count($deleteAdvertisingFormIds)) {
                $customer->advertisingForms()->detach($deleteAdvertisingFormIds);
            }

            if (count($addAdvertisingFormIds)) {
                $customer->advertisingForms()->attach($addAdvertisingFormIds);
            }

            foreach (($params['advertising_form_ids']) as $advertisingFormId) {
                if ($advertisingFormId == OTHER_ADVERTISING_FORM_ID && isset($params['other_advertising_form'])) {
                    $customerAdvertisingFormId = $this->customerAdvertisingFormRepositoryInterface->findCustomerAdvertisingForm($id, $advertisingFormId)->id;
                    $attributeForms = ['note_other' => $params['other_advertising_form']];
                    $this->customerAdvertisingFormRepositoryInterface->update($customerAdvertisingFormId, $attributeForms);
                }
            }
        }

        // purchase purpose
        if (isset($params['purchase_purpose_ids'])) {
            $currentPurchasePurposeIds = $customer->purchasePurposes()->pluck('purchase_purpose_id')->toArray();
            $newPurchasePurposeIds =  $params['purchase_purpose_ids'];

            $addPurchasePurposeIds = array_diff($newPurchasePurposeIds, $currentPurchasePurposeIds);
            $deletePurchasePurposeIds = array_diff($currentPurchasePurposeIds, $newPurchasePurposeIds);

            if (count($deletePurchasePurposeIds)) {
                $customer->purchasePurposes()->detach($deletePurchasePurposeIds);
            }

            if (count($addPurchasePurposeIds)) {
                $customer->purchasePurposes()->attach($addPurchasePurposeIds);
            }
            foreach (($params['purchase_purpose_ids']) as $purchasePurposeId) {
                if ($purchasePurposeId == OTHER_PURCHASE_PURPOSE_ID && isset($params['other_purchase_purpose'])) {
                    $customerPurchasePurposeId = $this->customerPurchasePurposeRepositoryInterface->findCustomerPurchasePurposeId($id, $purchasePurposeId)->id;
                    $attributesPurchase = ['note_other' => $params['other_purchase_purpose']];
                    $this->customerPurchasePurposeRepositoryInterface->update($customerPurchasePurposeId, $attributesPurchase);
                }
            }
        }

        // update custom field
        $customFieldParams = [];
        if (isset($params['custom_fields'])) {
            $customFieldParams = $params['custom_fields'];
        }
        foreach ($customFieldParams as $customField) {
            if (is_string($customField)) {
                $customField = json_decode($customField, true);
            }
            if (isset($customField['id'])) {
                $this->customerCustomValuesInterface->update(
                    $customField['id'],
                    [
                        'value' => $customField['value']
                    ]
                );
            } else {
                $params = [
                    'custom_field_id' => $customField['custom_field_id'],
                    'value' => $customField['value'],
                    'customer_id' => $id
                ];
                $this->customerCustomValuesInterface->insert($params);
            }
        }

        $this->customerInterface->update($id, $attributes);

        return [
            'id' => $id
        ];
    }

    // Check customer
    public function checkCustomer($id)
    {
        $userId = auth()->user()->id;
        $user = $this->userInterface->find($userId);

        $company = $user->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }

        $customer = $this->customerInterface->find($id);

        if (!$customer) {
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        } else if ($customer->company_id != $company->id) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }

        return false;
    }

    // QRCode for customer
    public function qrCode($token)
    {
        $url = env('CMS_URL') . '/customer-token/' . $token;
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $encode = [
            'encode_qr_code' => base64_encode($qrCode)
        ]; //encode image QRCode
        return _success($encode, __('message.get_success'), HTTP_SUCCESS);
    }

    // Get customer via token
    public function getSessionCustomer($token)
    {
        $data = $this->customerInterface->getSessionCustomer($token);

        return _success($data, __('message.get_success'), HTTP_SUCCESS);
    }

    // Check customer exist in the project
    public function checkCustomerInProject($id)
    {
        $countCustomer = $this->projectCustomerInterface->getList()->where('customer_id', $id)->count();
        if ($countCustomer > 0) {
            return _error('false', __('message.not_delete_customer'), HTTP_BAD_REQUEST);
        }
        return false;
    }

    //Project Customer
    public function indexProject($request, $id)
    {
        $checkCustomer = $this->checkCustomer($id);
        if ($checkCustomer) {
            return $checkCustomer;
        }

        $pageSize = $request->page_size ?? PAGE_SIZE;
        $countProjectCustomer = $this->projectInterface->countProjectCustomer($id);
        $listProjectCustomers = $this->projectInterface->listProjectCustomer($request, $id)->paginate($pageSize);
        $customer = $this->customerInterface->find($id);
        $data = [
            'object_name' => $customer->last_name . ' ' . $customer->first_name,
            'quantity_project' =>  $countProjectCustomer,
            'projects' => $listProjectCustomers->items(),
            'items_total' => $listProjectCustomers->total(),
            'current_page' => $request->page,
        ];

        return _success($data, __('success'), HTTP_SUCCESS);
    }
}

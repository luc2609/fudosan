<?php

namespace App\Services;

use App\Events\DivisionBrokeRageEvent;
use App\Events\DivisionContractEvent;
use App\Events\DivisionRevenueEvent;
use App\Events\UserBrokeRageEvent;
use App\Events\UserContractEvent;
use App\Events\UserRevenueEvent;
use App\Models\Customer;
use App\Models\ProjectCustomer;
use App\Repositories\Calendar\CalendarRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectCustomer\ProjectCustomerRepositoryInterface;
use App\Repositories\ProjectFile\ProjectFileRepositoryInterface;
use App\Repositories\ProjectHistory\ProjectHistoryRepositoryInterface;
use App\Repositories\ProjectPhase\ProjectPhaseRepositoryInterface;
use App\Repositories\ProjectProperty\ProjectPropertyRepositoryInterface;
use App\Repositories\ProjectUser\ProjectUserRepositoryInterface;
use App\Repositories\Property\PropertyRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDivision\UserDivisionRepositoryInterface;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProjectService
{
    protected $projectInterface;
    protected $userInterface;
    protected $customerInterface;
    protected $divisionInterface;
    protected $propertyInterface;
    protected $projectFileInterface;
    protected $projectUserInterface;
    protected $projectPhaseInterface;
    protected $fileService;
    protected $projectCustomerInterface;
    protected $projectPropertyInterface;
    protected $projectHistoryInterface;
    protected $calendarInterface;
    protected $projectPhaseService;
    protected $notifyService;
    protected $userDivisionInterface;
    protected $rankingService;
    protected $mailService;

    public function __construct(
        ProjectRepositoryInterface $projectInterface,
        UserRepositoryInterface $userInterface,
        CustomerRepositoryInterface $customerInterface,
        DivisionRepositoryInterface $divisionInterface,
        PropertyRepositoryInterface $propertyInterface,
        ProjectFileRepositoryInterface $projectFileInterface,
        ProjectUserRepositoryInterface $projectUserInterface,
        ProjectPhaseRepositoryInterface $projectPhaseInterface,
        FileService $fileService,
        ProjectCustomerRepositoryInterface $projectCustomerInterface,
        ProjectPropertyRepositoryInterface $projectPropertyInterface,
        ProjectHistoryRepositoryInterface $projectHistoryInterface,
        CalendarRepositoryInterface $calendarInterface,
        ProjectPhaseService $projectPhaseService,
        NotifyService $notifyService,
        UserDivisionRepositoryInterface $userDivisionInterface,
        RankingService $rankingService,
        MailService $mailService
    ) {
        $this->projectInterface = $projectInterface;
        $this->userInterface = $userInterface;
        $this->customerInterface = $customerInterface;
        $this->divisionInterface = $divisionInterface;
        $this->propertyInterface = $propertyInterface;
        $this->projectFileInterface = $projectFileInterface;
        $this->projectUserInterface = $projectUserInterface;
        $this->projectPhaseInterface = $projectPhaseInterface;
        $this->fileService = $fileService;
        $this->projectCustomerInterface = $projectCustomerInterface;
        $this->projectPropertyInterface = $projectPropertyInterface;
        $this->projectHistoryInterface = $projectHistoryInterface;
        $this->calendarInterface = $calendarInterface;
        $this->projectPhaseService = $projectPhaseService;
        $this->notifyService = $notifyService;
        $this->userDivisionInterface = $userDivisionInterface;
        $this->rankingService = $rankingService;
        $this->mailService = $mailService;
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($request)
    {
        $user = auth()->user();
        $checkListProject = $this->checkListProject($user->id);
        if ($checkListProject) {
            return $checkListProject;
        }
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $listProject = $this->projectInterface->index($request, $user->company, $user)->paginate($pageSize);
        $data = [
            'project' => $listProject->items(),
            'items_total' => $listProject->total()
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProject($request, $id)
    {
        $checkProject = $this->checkProject($id);
        if ($checkProject) {
            return $checkProject;
        }
        $user = auth()->user();
        $companyId = $user->company;
        $project = $this->projectInterface->show($id);
        $nextBackProject = $this->projectInterface->nextBackProject($request, $id, $companyId, $user);

        $data = [
            'project' => $project,
            'user_in_charge' => $project->userInCharge(),
            'sub_user_in_charge' => $project->subUserInCharge(),
            'related_users' => $project->relatedUsers()
        ];
        $data = array_merge($data, $nextBackProject);
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function create($params)
    {
        $companyId = auth()->user()->company;

        $purchaseTime = isset($params['transaction_time']) ? new DateTime($params['transaction_time']) : null;
        if (isset($params['revenue'])) {
            $revenue = (int)$params['revenue'];
        } else if (isset($params['property_id'])) {
            $revenue = $this->projectInterface->brokerageFeeOfProject($params['property_id']);
        } else {
            $revenue = null;
        }

        if (isset($params['price'])) {
            $price = (int)$params['price'];
        } else if (isset($params['property_id'])) {
            $price = $this->projectInterface->revenueOfProject($params['property_id']);
        } else {
            $price = null;
        }
        $attributes = [
            'division_id' => $params['division_id'],
            'company_id' => $companyId,
            'price' => $price,
            'deposit_price' => $params['deposit_price'] ?? null,
            'monthly_price' => $params['monthly_price'] ?? null,
            'description' => $params['description'] ?? null,
            'transaction_time' => $purchaseTime,
            'type' => $params['type'] ?? null,
            'revenue' => $revenue,
            'history' => '',
            'current_phase_id' => 0
        ];

        $project = $this->projectInterface->create($attributes);
        $projectId = $project->id;

        // Project property
        if (isset($params['property_id'])) {
            $dataProjectProperty = [];
            foreach ($params['property_id'] as $propertyID) {
                $dataProjectProperty[] = [
                    'property_id' => $propertyID,
                    'project_id' => $projectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $this->projectPropertyInterface->insert($dataProjectProperty);
        }

        $projectUsers = [];
        // user_in_charge_id
        $projectUsers[] = [
            'user_id' => $params['user_in_charge_id'],
            'project_id' => $projectId,
            'is_contract' => false,
            'user_type' => USER_IN_CHARGE_TYPE
        ];

        // sub_user_in_charge_id
        if (isset($params['sub_user_in_charge_id'])) {
            $projectUsers[] = [
                'user_id' => $params['sub_user_in_charge_id'],
                'project_id' => $projectId,
                'is_contract' => false,
                'user_type' => SUB_USER_IN_CHARGE_TYPE
            ];
        }

        // relate_user_ids
        if (isset($params['relate_user_ids'])) {
            foreach ($params['relate_user_ids'] as $relateUserId) {
                $projectUsers[] = [
                    'user_id' => $relateUserId,
                    'project_id' => $projectId,
                    'is_contract' => false,
                    'user_type' => RELATED_USER_TYPE
                ];
            }
        }

        $this->projectUserInterface->insert($projectUsers);
        $userInCharge = $this->userInterface->find($params['user_in_charge_id']);
        $username = $userInCharge->first_name . $userInCharge->last_name;
        $userIds = $project->users->pluck('id')->toArray();

        // project history
        $projectHistory = [];
        $projectHistory[] = [
            'user_id' => $params['user_in_charge_id'],
            'project_id' => $projectId,
            'status' => IN_PROGRESS,
            'user_type' => USER_IN_CHARGE_TYPE,
            'division_id' => $params['division_id'],
            'start_date' => now()
        ];
        $this->projectHistoryInterface->insert($projectHistory);

        // advertising web
        if (isset($params['advertising_web_ids'])) {
            if (is_array($params['advertising_web_ids'])) {
                $project->advertisingWebs()->attach($params['advertising_web_ids']);
            }
        }

        // sale_purposes
        if (isset($params['sale_purpose_ids'])) {
            if (is_array($params['sale_purpose_ids'])) {
                $project->salePurposes()->attach($params['sale_purpose_ids']);
            }
        }

        // purchase_purposes
        if (isset($params['purchase_purpose_ids'])) {
            if (is_array($params['purchase_purpose_ids'])) {
                $project->purchasePurposes()->attach($params['purchase_purpose_ids']);
            }
        }
        // Project customer
        if (isset($params['customer_id'])) {
            $dataProjectCustomer = [];
            foreach ($params['customer_id'] as $customerID) {
                $dataProjectCustomer[] = [
                    'customer_id' => $customerID,
                    'project_id' => $projectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $this->projectCustomerInterface->insert($dataProjectCustomer);
        }
        // documents
        if (isset($params['documents'])) {
            $files = $params['documents'];
            $filePath = 'project/' . $projectId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->projectFileInterface->create([
                    'project_id' => $projectId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                ]);
            }
        }

        // phase
        $projectPhases = [];
        $createdName = auth()->user()->username;
        for ($i = 1; $i < 11; $i++) {
            $projectPhase = [
                'project_id' => $projectId,
                'm_phase_project_id' => $i,
                'status' => 1,
                'created_name' => $createdName
            ];

            $projectPhases[] = $projectPhase;
        }
        $this->projectPhaseInterface->insert($projectPhases);
        $projectPhaseOne = $this->projectPhaseInterface->findByProjectId($projectId, NO_PHASE);
        $this->projectInterface->update($projectId, [
            'current_phase_id' => $projectPhaseOne->id
        ]);
        $this->projectInterface->createTitleProject($projectId, $projectPhaseOne->id);

        // Push notify
        if (isset($params['user_in_charge_id'])) {
            $userId = $params['user_in_charge_id'];
            $deviceTokenUsers = $this->userInterface->listDeviceToken(array($userId));

            $title = __('message.title_create_project');
            $content = __('message.content_push_create_project');
            try {
                $this->notifyService->pushNotify($deviceTokenUsers, $title, $content);
            } catch (\Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - Push noti create project: ' . $e->getMessage());
            }
        }
        if ($userIds) {
            $deviceTokenUsers = $this->userInterface->listDeviceToken($userIds);
            $title = __('message.title_noti_invite_project');
            $content = $username . '案件は「決済」のフェーズが完了しました';
            try {
                $this->notifyService->pushNotify($deviceTokenUsers, $title, $content);
            } catch (\Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - Push noti create project: ' . $e->getMessage());
            }
        }
        return [
            'id' => $projectId
        ];
    }

    /**
     * @param $params
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update($params, $id)
    {
        $project = $this->projectInterface->find($id);
        $purchaseTime = isset($params['transaction_time']) ? new DateTime($params['transaction_time']) : null;
        if (isset($params['revenue'])) {
            $revenue = $params['revenue'];
        } else if (isset($params['property_id'])) {
            $revenue = $this->projectInterface->revenueOfProject($params['property_id']);
        } else {
            $revenue = null;
        }
        $attributes = [
            'title' => $params['title'],
            'division_id' => $params['division_id'],
            'price' => $params['price'] ?? null,
            'deposit_price' => $params['deposit_price'] ?? null,
            'monthly_price' => $params['monthly_price'] ?? null,
            'transaction_time' => $purchaseTime,
            'description' => $params['description'] ?? null,
            'revenue' => $revenue,
        ];

        $addProjectUsers = [];
        // user_in_charge_id
        $projectUserInCharge = $project->projectUserInCharge();
        if ($projectUserInCharge) {
            $this->projectUserInterface->update(
                $projectUserInCharge->id,
                [
                    'user_id' => $params['user_in_charge_id']
                ]
            );
        } else {
            $addProjectUsers[] = [
                'user_id' => $params['user_in_charge_id'],
                'project_id' => $id,
                'is_contract' => false,
                'user_type' => USER_IN_CHARGE_TYPE
            ];
        }

        // sub_user_in_charge_id
        $projectSubUserInCharge = $project->projectSubUserInCharge();
        if (isset($params['sub_user_in_charge_id'])) {
            if ($projectSubUserInCharge) {
                $this->projectUserInterface->update(
                    $projectSubUserInCharge->id,
                    [
                        'user_id' => $params['sub_user_in_charge_id']
                    ]
                );
            } else {
                $addProjectUsers[] = [
                    'user_id' => $params['sub_user_in_charge_id'],
                    'project_id' => $id,
                    'is_contract' => false,
                    'user_type' => SUB_USER_IN_CHARGE_TYPE
                ];
            }
        } else if ($projectSubUserInCharge) {
            $project->projectSubUserInCharge()->delete();
        }

        // relate user
        $newRelateUserIds = [];
        if (isset($params['relate_user_ids'])) {
            $newRelateUserIds = $params['relate_user_ids'];
        }
        $currentRelateUserIds = $project->projectRelatedUsers->pluck('user_id')->toArray();

        $addRelateUserIds = array_diff($newRelateUserIds, $currentRelateUserIds);
        $deleteRelateUserIds = array_diff($currentRelateUserIds, $newRelateUserIds);

        if (count($deleteRelateUserIds)) {
            $project->projectRelatedUsers()
                ->whereIn('user_id', $deleteRelateUserIds)->delete();
        }

        if (count($addRelateUserIds)) {
            foreach ($addRelateUserIds as $relateUserId) {
                $addProjectUsers[] = [
                    'user_id' => $relateUserId,
                    'project_id' => $id,
                    'is_contract' => false,
                    'user_type' => RELATED_USER_TYPE
                ];
            }
        }
        $this->projectUserInterface->insert($addProjectUsers);

        // advertising web
        $newAdvertisingWebIds = [];
        if (isset($params['advertising_web_ids'])) {
            $newAdvertisingWebIds = $params['advertising_web_ids'];
        }
        $currentAdvertisingWebIds = $project->advertisingWebs->pluck('id')->toArray();

        $addAdvertisingWebIds = array_diff($newAdvertisingWebIds, $currentAdvertisingWebIds);
        $deleteAdvertisingWebIds = array_diff($currentAdvertisingWebIds, $newAdvertisingWebIds);

        if (count($deleteAdvertisingWebIds)) {
            $project->advertisingWebs()->detach($deleteAdvertisingWebIds);
        }

        if (count($addAdvertisingWebIds)) {
            $project->advertisingWebs()->attach($addAdvertisingWebIds);
        }

        //customer
        if (isset($params['customer_id'])) {
            $project->projectCustomers()->delete();
            $dataProjectCustomer = [];
            foreach ($params['customer_id'] as $customerID) {
                $dataProjectCustomer[] = [
                    'customer_id' => $customerID,
                    'project_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $this->projectCustomerInterface->insert($dataProjectCustomer);
        }

        //property
        $project->projectProperties()->delete();
        if (isset($params['property_id'])) {
            $dataProjectProperty = [];
            foreach ($params['property_id'] as $propertyId) {
                $dataProjectProperty[] = [
                    'property_id' => $propertyId,
                    'project_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $this->projectPropertyInterface->insert($dataProjectProperty);
        }

        // delete documents
        if (isset($params['delete_document_ids'])) {
            $deleteDocumentIds = $params['delete_document_ids'];

            foreach ($deleteDocumentIds as $fileId) {
                $file = $this->projectFileInterface->find($fileId);

                if ($file) {
                    $this->fileService->deleteFileS3($file->url);
                    $this->projectFileInterface->delete($fileId);
                }
            }
        }

        // documents
        if (isset($params['documents'])) {
            $files = $params['documents'];
            $filePath = 'project/' . $id . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->projectFileInterface->create([
                    'project_id' => $id,
                    'name' => $file->getClientOriginalName(),
                    'url' => $fileUrl,
                ]);
            }
        }

        // sale purposes
        $newSalePurposeIds = [];
        if (isset($params['sale_purpose_ids'])) {
            $newSalePurposeIds = $params['sale_purpose_ids'];
        }
        $currentSalePurposeIds = $project->salePurposes->pluck('id')->toArray();

        $addSalePurposeIds = array_diff($newSalePurposeIds, $currentSalePurposeIds);
        $deleteSalePurposeIds = array_diff($currentSalePurposeIds, $newSalePurposeIds);

        if (count($deleteSalePurposeIds)) {
            $project->salePurposes()->detach($deleteSalePurposeIds);
        }

        if (count($addSalePurposeIds)) {
            $project->salePurposes()->attach($addSalePurposeIds);
        }

        // purchase purposes
        $newPurchasePurposeIds = [];
        if (isset($params['purchase_purpose_ids'])) {
            $newPurchasePurposeIds = $params['purchase_purpose_ids'];
        }
        $currentPurchasePurposeIds = $project->purchasePurposes->pluck('id')->toArray();

        $addPurchasePurposeIds = array_diff($newPurchasePurposeIds, $currentPurchasePurposeIds);
        $deletePurchasePurposeIds = array_diff($currentPurchasePurposeIds, $newPurchasePurposeIds);

        if (count($deletePurchasePurposeIds)) {
            $project->purchasePurposes()->detach($deletePurchasePurposeIds);
        }

        if (count($addPurchasePurposeIds)) {
            $project->purchasePurposes()->attach($addPurchasePurposeIds);
        }

        $this->projectInterface->update($id, $attributes);
        $this->projectInterface->createTitleProject($id, $project->current_phase_id);
        return [
            'id' => $id
        ];
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showReportProject($id)
    {
        $checkProject = $this->checkProject($id);
        if ($checkProject) {
            return $checkProject;
        }
        $reports = $this->projectInterface->showProject($id);
        $project = $this->projectInterface->show($id);
        $data = [
            'reports' => $reports,
            'user_in_charge' => $project->userInCharge(),
            'sub_user_in_charge' => $project->subUserInCharge(),
            'related_users' => $project->relatedUsers(),
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    /**
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkProject($id)
    {
        $user = auth()->user();
        $company = $user->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $project = $this->projectInterface->find($id);
        if (!$project) {
            return _error(null, __('message.project_not_found'), HTTP_NOT_FOUND);
        } else if ($project->company_id != $company->id) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        return false;
    }

    /**
     * @param $userId
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkListProject($userId)
    {
        $user = $this->userInterface->find($userId);
        $company = $user->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        return false;
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReportProject($request, $id)
    {
        $userId = auth()->user()->id;
        $checkProject = $this->checkProject($id);
        $checkRoles = $this->checkRoles($id, $userId);
        if ($checkProject) {
            return $checkProject;
        }
        if ($checkRoles) {
            return $checkRoles;
        }

        $data = $this->projectInterface->createReportProject($request, $id, $userId);
        return _success($data, __('message.created_success'), HTTP_SUCCESS);
    }

    /**
     * @param $id
     * @param $userId
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkRoles($id, $userId)
    {
        $user = $this->userInterface->find($userId);
        $project = $this->projectInterface->find($id);
        if (($user->hasRole(MANAGER_ROLE) && ($user->divisions->contains('id', $project->division_id)))
            || ($user->hasRole(ADMIN_CMS_COMPANY_ROLE))
        ) {
            return false;
        }
        return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
    }

    /**
     * @param $id
     * @param $userId
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkRoleUpdate($id, $userId)
    {
        $user = $this->userInterface->find($userId);
        $project = $this->projectInterface->find($id);
        $userInCharge = $project->projectUserInCharge()->user_id;
        if (($user->hasRole(MANAGER_ROLE) && ($user->divisions->contains('id', $project->division_id)))
            || ($user->hasRole(ADMIN_CMS_COMPANY_ROLE))
            || ($userId == $userInCharge)
        ) {
            return false;
        }
        return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
    }

    /**
     * @param $user
     * @param $params
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkParamsProject($user, $params, $id)
    {
        $company = $user->company()->first();

        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $companyId = $company->id;

        // check property
        if (isset($params['property_id'])) {
            $propertyIds = $params['property_id'];
            foreach ($propertyIds as $propertyId) {
                $property = $this->propertyInterface->find($propertyId);
                if ($property->company_id != $companyId) {
                    return _error(null, __('message.property_not_correct'), HTTP_BAD_REQUEST);
                }
            }
        }

        // check customer
        $customerIds = $params['customer_id'];
        foreach ($customerIds as $customerId) {
            $customer = $this->customerInterface->find($customerId);
            if ($customer->company_id != $companyId) {
                return _error(null, __('message.customer_not_correct'), HTTP_BAD_REQUEST);
            }
        }

        // check division
        $divisionId = $params['division_id'];
        $division = $this->divisionInterface->find($divisionId);
        if ($division->company_id != $companyId) {
            return _error(null, __('message.division_not_correct'), HTTP_BAD_REQUEST);
        }

        // check user_in_charge_id
        $userInChargeId = $params['user_in_charge_id'];
        $userInCharge = $this->userInterface->find($userInChargeId);
        if ($userInCharge->company != $companyId) {
            return _error(null, __('message.user_in_charge_id_not_correct'), HTTP_BAD_REQUEST);
        }

        // check sub_user_in_charge_id
        if (isset($params['sub_user_in_charge_id'])) {
            $subUserInChargeId = $params['sub_user_in_charge_id'];
            $subUserInCharge = $this->userInterface->find($subUserInChargeId);
            if ($subUserInCharge->company != $companyId) {
                return _error(null, __('message.sub_user_in_charge_id_not_correct'), HTTP_BAD_REQUEST);
            }
        }

        // check relate_user_ids
        $relateUserIds = $params['relate_user_ids'] ?? [];
        foreach ($relateUserIds as $relateUserId) {
            $relateUser = $this->userInterface->find($relateUserId);
            if ($relateUser->company != $companyId) {
                return _error(null, __('message.relate_user_id_not_correct'), HTTP_BAD_REQUEST);
            }
        }

        if ($id) {
            $project = $this->projectInterface->find($id);

            // Check document count
            $documentsCurrentCount = count($project->documents);
            $documentsDeleteCount = 0;

            if (isset($params['delete_document_ids'])) {
                $deleteDocumentIds = $params['delete_document_ids'];

                foreach ($deleteDocumentIds as $deleteDocumentId) {
                    if (!$project->documents->contains('id', $deleteDocumentId)) {
                        return _error(null, __('message.delete_document_id_not_correct'), HTTP_BAD_REQUEST);
                    }
                }

                $documentsDeleteCount = count($deleteDocumentIds);
            }

            if (isset($params['documents'])) {
                $documentsAddCount = count($params['documents']);

                if ($documentsCurrentCount - $documentsDeleteCount + $documentsAddCount > 5) {
                    return _error('false', __('message.document_over_limited'), HTTP_BAD_REQUEST);
                }
            }
        }
        return false;
    }

    /**
     * @param $request
     * @param $id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReportProject($request, $id, $postId)
    {
        $userId = auth()->user()->id;
        $checkProject = $this->checkProject($id);
        $checkRoles = $this->checkRoles($id, $userId);
        if ($checkProject) {
            return $checkProject;
        }
        if ($checkRoles) {
            return $checkRoles;
        }

        $data = $this->projectInterface->updatReportProject($request, $postId);
        return _success($data, __('message.updated_success'), HTTP_SUCCESS);
    }

    /**
     * @param $request
     * @param $id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createComment($request, $id, $postId)
    {
        $userId = auth()->user()->id;
        $checkProject = $this->checkProject($id);
        $checkRoles = $this->checkRoles($id, $userId);
        $checkPost = $this->checkPost($id, $postId);
        if ($checkProject) {
            return $checkProject;
        }
        if ($checkRoles) {
            return $checkRoles;
        }
        if ($checkPost) {
            return $checkPost;
        }

        $data = $this->projectInterface->createComment($request, $postId, $userId);
        return _success($data, __('message.created_success'), HTTP_SUCCESS);
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id)
    {
        $project = $this->projectInterface->find($id);
        foreach ($project->documents as $file) {
            if ($file) {
                $this->fileService->deleteFileS3($file->url);
            }
        }
        $this->projectCustomerInterface->delete($id);
        $this->projectInterface->delete($id);
        $project->users()->delete();
        $project->calendars()->delete();
    }

    /**
     * @param $request
     * @param $id
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateComment($request, $id, $postId, $commentId)
    {
        $userId = auth()->user()->id;
        $checkProject = $this->checkProject($id);
        $checkRoles = $this->checkRoles($id, $userId);
        $checkPost = $this->checkPost($id, $postId);

        if ($checkProject) {
            return $checkProject;
        }

        if ($checkRoles) {
            return $checkRoles;
        }

        if ($checkPost) {
            return $checkPost;
        }

        $data = $this->projectInterface->updateComment($request, $commentId);
        return _success($data, __('message.updated_success'), HTTP_SUCCESS);
    }

    public function checkPost($id, $postId)
    {
        $project = $this->projectInterface->find($id);

        $post = $project->posts->contains('id', $postId);
        if (!$post) {
            return _error(null, __('message.post_not_found'), HTTP_NOT_FOUND);
        }
        return false;
    }

    /**
     * @param $id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePost($id, $postId)
    {
        $userId = auth()->user()->id;
        $checkProject = $this->checkProject($id);
        $checkRoles = $this->checkRoles($id, $userId);
        $checkPost = $this->checkPost($id, $postId);
        if ($checkProject) {
            return $checkProject;
        }
        if ($checkRoles) {
            return $checkRoles;
        }
        if ($checkPost) {
            return $checkPost;
        }

        $data = $this->projectInterface->deletePost($postId);
        if ($data) {
            return _success(null, __('message.delete_success'), HTTP_SUCCESS);
        }
        return _error(null, __('message.comment_not_found'), HTTP_NOT_FOUND);
    }

    /**
     * @param $id
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment($id, $postId, $commentId)
    {
        $userId = auth()->user()->id;
        $checkProject = $this->checkProject($id);
        $checkRoles = $this->checkRoles($id, $userId);
        $checkPost = $this->checkPost($id, $postId);

        if ($checkProject) {
            return $checkProject;
        }
        if ($checkRoles) {
            return $checkRoles;
        }
        if ($checkPost) {
            return $checkPost;
        }

        $data = $this->projectInterface->deleteComment($postId, $commentId);
        if ($data) {
            return _success(null, __('message.delete_success'), HTTP_SUCCESS);
        }
        return _error(null, __('message.comment_not_found'), HTTP_NOT_FOUND);
    }

    /**
     * @param $id
     * @param $params
     * @param $user
     * @return false
     */
    public function updateClose($id, $params, $user)
    {
        try {
            $customerIds = ProjectCustomer::where('project_id', $id)->first();
            $customer = Customer::find($customerIds['customer_id']);
            $project = $this->projectInterface->find($id);
            $subUserInCharge = $project->projectUserInCharge();
            $subUserInChargeId = $subUserInCharge->user_id;
            $username = $this->userInterface->find($subUserInChargeId)->username;
            $nowDate = Carbon::now()->toDateTimeString();
            $divisionId = $project->division_id;
            $companyId = $project->company_id;
            $managers = $this->userDivisionInterface->getManagerListOfDivision($divisionId, null)->pluck('id')->toArray();
            if (isset($params['reason'])) {
                $data = [
                    'date' => $nowDate,
                    'content' => $params['reason'],
                    'id' => $user->id,
                    'name' => $user->username,
                    'avatar' => $user->avatar
                ];
                if ($project->reason) {
                    $arrayJsonReason = $project->reason;
                    $arrayJsonReasonEncode = json_encode($data);
                    $arrayJsonReason[] = json_decode($arrayJsonReasonEncode);
                } else {
                    $arrayJsonReason = [$data];
                }
                $reason = json_encode($arrayJsonReason);
            } else {
                $reason = $project->reason;
            }
            if ($params['close_status'] == SUCCESS_CLOSE || $params['close_status'] == FAIL_CLOSE) {
                $title = '【終了】' . ' ' . $customer->last_name . ' ' . $customer->first_name . 'さま ';
                $this->projectInterface->update(
                    $id,
                    [
                        'close_status' => $params['close_status'],
                        'title' => $title,
                    ]
                );

                $this->userInterface->update($subUserInChargeId, ['close_project_date' => now()]);

                if ($params['close_status'] == SUCCESS_CLOSE) {
                    Log::info('THANH: UPDATE PROJECT CLOSE SUCCESS');
                    $overviewUser = ["overview" => $this->rankingService->index(null, TOTAL_RANKING_USER)];
                    $userContractRanking = $this->userInterface->indexContractRanking($params, $companyId);
                    $userContract = array_merge($userContractRanking, $overviewUser);
                    Log::info(json_encode($userContract));
                    event(new UserContractEvent($userContract, $companyId));

                    $userRevenueRanking = $this->userInterface->indexRevenueRanking($params, $companyId);
                    $userRevenue = array_merge($userRevenueRanking, $overviewUser);
                    Log::info(json_encode($userRevenue));
                    event(new UserRevenueEvent($userRevenue, $companyId));

                    $userBrokeRageRanking = $this->userInterface->indexBrokeRageRanking($params, $companyId);
                    $userBrokeRage = array_merge($userBrokeRageRanking, $overviewUser);
                    Log::info(json_encode($userBrokeRage));
                    event(new UserBrokeRageEvent($userBrokeRage, $companyId));

                    $overviewDivision = ["overview" => $this->rankingService->index(null, TOTAL_RANKING_DIVISION)];
                    $divisionContractRanking = $this->divisionInterface->indexContractRanking($params, $companyId);
                    $divisionContract = array_merge($divisionContractRanking, $overviewDivision);
                    Log::info(json_encode($divisionContract));
                    event(new DivisionContractEvent($divisionContract, $companyId));

                    $divisionRevenueRanking = $this->divisionInterface->indexRevenueRanking($params, $companyId);
                    $divisionRevenue = array_merge($divisionRevenueRanking, $overviewDivision);
                    Log::info(json_encode($divisionRevenue));
                    event(new DivisionRevenueEvent($divisionRevenue, $companyId));

                    $divisionBrokeRageRanking = $this->divisionInterface->indexBrokeRageRanking($params, $companyId);
                    $divisionBrokeRage = array_merge($divisionBrokeRageRanking, $overviewDivision);
                    Log::info(json_encode($divisionBrokeRage));
                    event(new DivisionBrokeRageEvent($divisionBrokeRage, $companyId));
                }

                // Push notify
                if ($managers) {
                    $deviceTokenUsers = $this->userInterface->listDeviceToken($managers);
                    $label = __('message.title_close_project');
                    $content = $user->username . 'さんがあなたの' . $title . '案件のクローズリクエストを承諾しました。';
                    $this->notifyService->pushNotify($deviceTokenUsers, $label, $content);
                }
            } else  if ($params['close_status'] == REQUEST_CLOSE) {
                $this->projectInterface->update(
                    $id,
                    [
                        'close_status' => $params['close_status'],
                        'reason' => $reason
                    ]
                );
                // Push notify
                if ($managers) {
                    $deviceTokenUsers = $this->userInterface->listDeviceToken($managers);
                    $title = __('message.title_noti_request_close');
                    $content = $username . 'さんが' . $project->title . '案件のクローズリクエストを送信しましたので確認してください';
                    $this->notifyService->pushNotify($deviceTokenUsers, $title, $content);
                }
            } else {
                $this->projectInterface->update(
                    $id,
                    [
                        'close_status' => $params['close_status'],
                        'reason' => $reason
                    ]
                );
            }
            return false;
        } catch (Exception $e) {
            Log::error(__METHOD__ . __LINE__ . ': System error: ' . $e->getMessage());
        }
    }

    /**
     * @param $id
     * @param $params
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkRoleClose($id, $params)
    {
        $authUserId = auth()->user()->id;
        $authUser = $this->userInterface->find($authUserId);
        $project = $this->projectInterface->find($id);
        $currentCloseStatus = $project->close_status;
        $closeStatus = $params['close_status'];
        $response = false;
        $userInChargeId = $project->projectUserInCharge()->user_id ?? null;
        if (
            // ($currentCloseStatus == IN_PROGRESS && $closeStatus != REQUEST_CLOSE)
            // || ($currentCloseStatus == REQUEST_CLOSE && $closeStatus == REQUEST_CLOSE)
            ($currentCloseStatus == FAIL_CLOSE)
            || ($currentCloseStatus == SUCCESS_CLOSE)
            || ($currentCloseStatus == REJECT_CLOSE && $closeStatus == REJECT_CLOSE)
        ) {
            $response = _error(null, __('message.close_status_incorrect'), HTTP_BAD_REQUEST);
        } else if ((($currentCloseStatus == IN_PROGRESS || $currentCloseStatus == REJECT_CLOSE) && $authUserId != $userInChargeId)
            || ($currentCloseStatus == REQUEST_CLOSE && $closeStatus == IN_PROGRESS && $authUserId != $userInChargeId)
            || ($currentCloseStatus == REQUEST_CLOSE && $closeStatus != IN_PROGRESS && !$authUser->hasRole(MANAGER_ROLE))
        ) {
            $response = _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        return $response;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProjectHistory($id)
    {
        $checkProject = $this->checkProject($id);
        if ($checkProject) {
            return $checkProject;
        }
        $history = $this->projectInterface->showProjectHistory($id);
        return _success($history, __('message.get_list_success'), HTTP_SUCCESS);
    }

    /**
     * List project request close
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexRequestClose($request)
    {
        $user = auth()->user();
        $checkListProject = $this->checkListProject($user->id);
        if ($checkListProject) {
            return $checkListProject;
        }
        $pageSize = $request->page_size ?? PAGE_SIZE;

        $listProject = $this->projectInterface->indexRequestClose($user, $user->company, $request);
        $itemTotal = $listProject->get()->count();
        $data = [
            'project' => $listProject->paginate($pageSize)->items(),
            'items_total' => $itemTotal
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    /**
     * @return bool
     */
    public function cronjobPhase()
    {
        $currentDay = Carbon::now();
        $meetingDay = $currentDay->format('Y-m-d H:i');
        $startDay = $currentDay->format('Y-m-d');
        $projectCalendars = $this->projectInterface->listProjectCalendar($startDay);
        foreach ($projectCalendars as $projectCalendar) {
            $meetingEndTime = Carbon::parse($projectCalendar->meeting_end_time)->format('Y-m-d H:i');
            if ($meetingDay == $meetingEndTime) {
                $mPhase = $this->projectPhaseInterface->mPhaseProject($projectCalendar->project_phase_id);
                $project = $this->projectInterface->find($projectCalendar->id);
                $userId = $this->calendarInterface->findUser($projectCalendar->id, $projectCalendar->project_phase_id)->user_id;
                $userInCharge = $project->projectUserInCharge()->user_id;
                $userIds = [$userId, $userInCharge];
                $userIds = array_unique($userIds);
                $title = $project->title;
                $calendars = $this->projectInterface->findListCalendar($projectCalendar->id, $meetingEndTime, $projectCalendar->calendar_id);
                $data = [
                    'is_action_noti' => NO_ACTION_NOTI
                ];
                $this->projectInterface->update($projectCalendar->id, $data);
                $type = 'transfer_phase_project';
                if ($calendars->count() > 0) {
                    $nextCalendar = $calendars->first();
                    $newPhase = $nextCalendar->project_phase_id;
                    $newMPhase = $this->projectPhaseInterface->mPhaseProject($newPhase);
                    $content = $title . ': 【' . $mPhase->name . '】から【' . $newMPhase->name . '】に変更してもよろしいでしょうか？';
                    $phaseName = [
                        'new_phase_name' => $newMPhase->name,
                        'current_phase_name' => $mPhase->name,
                    ];

                    $dataNextPhase = array_merge($nextCalendar->toArray(), $phaseName);
                    $data = array_merge(['type' => $type], $dataNextPhase);
                } else {
                    $content = '最新のフェーズにいますので、次のフェーズをスケージュールしてください';
                    $data =  [
                        'type' => $type,
                        'project_id' => $projectCalendar->id
                    ];
                }
                // Push notify
                if ($userIds) {
                    try {
                        $deviceTokenUsers = $this->userInterface->listDeviceToken($userIds);
                        $label = __('message.label');
                        $this->notifyService->pushNotify($deviceTokenUsers, $label, $content, $data);
                        Log::info(__METHOD__ . ' - ' . __LINE__ . ' : ' . $label . ' : ' . $content);
                    } catch (\Exception $e) {
                        Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - Push noti transfer phase project when end time!');
                    }
                }
            }
        }
        return true;
    }

    public function pushNotiPhaseProject()
    {
        $currentDay = Carbon::now();
        $startDay = $currentDay->format('Y-m-d H:i');
        $projectCalendars = $this->projectInterface->listProjectCalendarCrontab($startDay);
        if (count($projectCalendars) > 0) {
            foreach ($projectCalendars as $projectCalendar) {
                $phaseProject = $this->nameProject($projectCalendar['m_phase_project_id']);
                $phaseProjectNew = $this->nameProject($projectCalendar['project_phase_id']);
                $content = $projectCalendar['title'] . ': 【' . $phaseProject . '】から【' . $phaseProjectNew . '】に変更してもよろしいでしょうか？';
                $data = [
                    "type" => "transfer_phase_project",
                    "project_id" => $projectCalendar['id'],
                    "start_date" => $projectCalendar['start_date'],
                    "end_date" => $projectCalendar['end_date'],
                    "project_phase_id" => $projectCalendar['project_phase_id'],
                    "meeting_start_time" => $projectCalendar['meeting_start_time'],
                    "meeting_end_time" => $projectCalendar['meeting_end_time'],
                    "current_phase_id" => $projectCalendar['current_phase_id'],
                    "new_phase_name" => $phaseProjectNew,
                    "current_phase_name" => $phaseProject
                ];
                // Push notify
                if ($projectCalendar['user_id']) {
                    try {
                        $deviceTokenUsers = $this->userInterface->listDeviceToken([$projectCalendar['user_id']]);
                        $label = __('message.label');
                        $this->notifyService->pushNotify($deviceTokenUsers, $label, $content, $data);
                        Log::info(__METHOD__ . ' - ' . __LINE__ . ' : ' . $label . ' : ' . $content);
                    } catch (\Exception $e) {
                        Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - Push noti transfer phase project when end time!');
                    }
                }
            }
        }
        return true;
    }

    public function nameProject($phaseId)
    {
        switch ($phaseId) {
            case PHASE_ONE:
                $phaseName = "来店";
                break;
            case PHASE_TWO:
                $phaseName = "見学";
                break;
            case PHASE_THREE:
                $phaseName = "仮受付申込";
                break;
            case PHASE_FOUR:
                $phaseName = "事前審査";
                break;
            case PHASE_FIVE:
                $phaseName = "契約";
                break;
            case PHASE_SIX:
                $phaseName = "本審査";
                break;
            case PHASE_SEVEN:
                $phaseName = "立会";
                break;
            case PHASE_EIGHT:
                $phaseName = "金消契約";
                break;
            case PHASE_NINE:
                $phaseName = "決済";
                break;
            default:
                $phaseName = "新規";
                break;
        }
        return $phaseName;
    }

    public function updatePhase($projectId, $request)
    {
        $project = $this->projectInterface->find($projectId);
        $userInCharge = $project->projectUserInCharge();
        $userId = $userInCharge->user_id;
        $username = $project->users->where('id', $userId)->pluck('username')->first();
        $currentPhaseId = $project->current_phase_id;
        $mPhaseId = $request->project_phase_id;
        $phaseProjectId = $this->projectPhaseInterface->findByProjectId($projectId, $mPhaseId)->id;
        $createdAt = $this->calendarInterface->findCalendarProject($projectId, $mPhaseId)->meeting_start_time;
        $this->projectPhaseInterface->updateHistory($project, $currentPhaseId, $mPhaseId, $phaseProjectId, $username, $userId, IS_ACTION_NOTI, $createdAt);
        $this->projectInterface->createTitleProject($projectId, $phaseProjectId);
        $data = $this->projectInterface->show($projectId);

        // send mail only phase 5
        if ($mPhaseId == PHASE_FIVE) {
            $this->mailService->sendEmail(
                env('SYSTEM_MAIL'),
                ['title' => $data->title],
                __('text.transfer_phase_5_project'),
                'mail.transfer_phase_5_project'
            );
        }
        return _success($data, __('message.updated_success'), HTTP_CREATED);
    }

    /**
     * @param $companyId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function deleteProjectCompany($companyId)
    {
        try {
            $projects = $this->projectInterface->getProjectInCompany($companyId);
            foreach ($projects as $project) {
                $projectId = $project->id;
                $this->delete($projectId);
            }
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - Delete calendar company');
            return _errorSystem($e);
        }
    }

    public function cancelProject($id)
    {
        try {
            $user = Auth::user();
            $projects = $this->projectInterface->find($id);
            $userInCharge = $projects->userInCharge();
            if ($user->id != $userInCharge->id) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }
            $this->projectInterface->update($id, ['close_status' => PROJECT_CANCEL]);
            return _success(null, __('message.cancel_success'), HTTP_SUCCESS);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . $e->getMessage());
            return _errorSystem($e);
        }
    }
}

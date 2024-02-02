<?php

namespace App\Services;

use App\Exports\DivisionExport;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\UserDivision\UserDivisionRepositoryInterface;

class DivisionService
{
    protected $divisionInterface;
    protected $userInterface;
    protected $fileService;
    protected $projectInterface;
    protected $userDivisionInterface;

    public function __construct(
        DivisionRepositoryInterface $divisionInterface,
        UserRepositoryInterface $userInterface,
        FileService $fileService,
        ProjectRepositoryInterface $projectInterface,
        UserDivisionRepositoryInterface $userDivisionInterface
    ) {
        $this->divisionInterface = $divisionInterface;
        $this->userInterface = $userInterface;
        $this->fileService = $fileService;
        $this->projectInterface = $projectInterface;
        $this->userDivisionInterface = $userDivisionInterface;
    }

    // List division in company
    public function list($request)
    {
        $companyId = auth()->user()->company;
        $params = $request->all();
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $divisions = $this->divisionInterface->listInCompany($companyId, $params)->paginate($pageSize);

        $data = [
            'divisions' => $divisions->items(),
            'items_total' => $divisions->total()
        ];

        return _success($data, __('message.division_list_success'), HTTP_SUCCESS);
    }

    // List division in company
    public function export($params)
    {
        $companyId = auth()->user()->company;
        $divisions = $this->divisionInterface->listInCompany($companyId, $params)->get();

        $currentDate = date('Ymd');
        $userId = auth()->user()->id;
        $fileName = $currentDate . __('filename.export_division');
        $filePath = 'division/' . $userId . '/' . $fileName;

        $exportedObject = new DivisionExport($divisions);
        $link = $this->fileService->saveFileToS3($filePath, $exportedObject);
        $data = ['link' => $link];

        return _success($data, __('message.export_success'), HTTP_SUCCESS);
    }

    // List division vie role account login
    public function listDivisionVieRole($params)
    {
        $user = auth()->user();
        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $divisions = $this->divisionInterface->listDivisionVieRole($user, $params)->paginate($pageSize);

        $data = [
            'divisions' => $divisions->items(),
            'items_total' => $divisions->total()
        ];

        return _success($data, __('message.division_list_success'), HTTP_SUCCESS);
    }

    // Get managers of division
    public function showManagersOfDivision($id, $params)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }
        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $listManagers = $this->userDivisionInterface->getManagerListOfDivision($id, $params)->paginate($pageSize);
        $data = [
            'managers' => $listManagers->items(),
            'items_total' => $listManagers->total()
        ];

        return _success($data, __('message.division_show_success'), HTTP_SUCCESS);
    }

    // Get users of division
    public function showUsersOfDivision($id, $params)
    {
        $authUser = auth()->user();

        $checkDivision = $this->checkDivision($id, $authUser);
        if ($checkDivision) {
            return   $checkDivision;
        }
        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $listUsers = $this->userInterface->getUserListOfDivision($id, $params)->paginate($pageSize);

        // Check auth user is manger of division
        $isManagerOfDivision = false;
        if (
            $authUser->hasRole(MANAGER_ROLE) &&
            $authUser->divisions->contains('id', $id)
        ) {
            $isManagerOfDivision = true;
        }

        $data = [
            'users' => $listUsers->items(),
            'auth_is_manager' =>  $isManagerOfDivision,
            'items_total' => $listUsers->total()
        ];

        return _success($data, __('message.division_show_success'), HTTP_SUCCESS);
    }

    // Get all manager and user of division
    public function showEmployeeOfDivison($id, $request)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return  $checkDivision;
        }
        $companyId = $auth->company;
        $params = $request->all();
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $listManagerUsers = $this->userInterface->getManagerUserListOfDivision($id, $companyId, $params)->paginate($pageSize);

        $data = [
            'manager_users' => $listManagerUsers->items(),
            'items_total' => $listManagerUsers->total()
        ];

        return _success($data, __('message.division_show_success'), HTTP_SUCCESS);
    }

    // Create division
    public function create($name)
    {
        $user = auth()->user();

        $company = $user->company()->first();

        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }

        $params = ['name' => $name];
        // TODO: Hàm findByParams a thấy dùng rất nhiều lần, sao không viết vào base ???
        $division = $this->divisionInterface->findByParams($company->id, $params);

        if ($division) {
            return _error(null, __('message.division_existed'), HTTP_BAD_REQUEST);
        }

        $division = $this->divisionInterface->create([
            'name' => $name,
            'company_id' => $company->id
        ]);

        $data = [
            'id' => $division->id,
            'name' => $name
        ];

        return _success($data, __('message.created_success'), HTTP_SUCCESS);
    }

    // Update division
    public function update($id, $data)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }

        $division = $this->divisionInterface->find($id);
        if ($division->name != $data['name']) {
            $params = ['name' => $data['name']];
            $companyId = $auth->company;
            $otherDivision = $this->divisionInterface->findByParams($companyId, $params);

            if ($otherDivision) {
                return _error(null, __('message.division_existed'), HTTP_BAD_REQUEST);
            }
            $division = $this->divisionInterface->update($id, [
                'name' => $data['name']
            ]);

            $data = [
                'id' => $division->id,
                'name' => $data['name']
            ];

            return _success($data, __('message.updated_success'), HTTP_SUCCESS);
        }
    }

    // Delete division
    public function delete($id)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }

        // Check user count, and manager count in division
        $division =  $this->divisionInterface->find($id);
        if ($division->user_count + $division->manager_count) {
            return _error(null, __('message.division_not_empty'), HTTP_SUCCESS);
        }

        $deleteDivision = $this->divisionInterface->delete($id);
        if ($deleteDivision) {
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        }
        return _error(null, __('message.deleted_fail'), HTTP_SUCCESS);
    }

    // Show available managers to add division
    public function showAvailableManagers($id, $request)
    {
        $user = auth()->user();
        $checkDivision = $this->checkDivision($id, $user);
        if ($checkDivision) {
            return $checkDivision;
        }
        $params = $request->all();
        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $listManagers = $this->userInterface->getAvailableManagerListOfDivision($id, $params)->paginate($pageSize);

        $data = [
            'managers' => $listManagers->items(),
            'items_total' => $listManagers->total()
        ];

        return _success($data, __('message.division_show_success'), HTTP_SUCCESS);
    }

    // Add available managers to division
    public function addAvailableManagers($id, $managerID)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }

        $manager = $this->userInterface->find($managerID);
        $companyId = $auth->company;
        if (
            !$manager->hasRole(MANAGER_ROLE) ||
            $manager->company != $companyId
        ) {
            return _error(null, __('message.manager_incorrect'), HTTP_BAD_REQUEST);
        }

        $existManager = $manager->divisions()->where('divisions.id', $id)->exists();
        if ($existManager) {
            return _error(null, __('message.manager_exists_division'), HTTP_BAD_REQUEST);
        }

        $addManager = $this->divisionInterface->addAvailableManagers($id, $managerID);
        if ($addManager) {
            return _error(null, __('message.created_fail'), HTTP_SUCCESS);
        }
        return _success(null, __('message.created_success'), HTTP_SUCCESS);
    }

    // Destroy available managers in division
    public function destroyAvailableManagers($id, $managerID)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }

        $division = $this->divisionInterface->find($id);

        $existManager = $division->managers()->where('users.id', $managerID)->exists();
        if (!$existManager) {
            return _error(null, __('message.manager_incorrect'), HTTP_BAD_REQUEST);
        }

        $destroyManager = $this->divisionInterface->destroyManger($id, $managerID);
        if ($destroyManager) {
            return _error(null, __('message.deleted_fail'), HTTP_SUCCESS);
        }
        return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
    }

    // Show available users
    public function showAvailableUsers($id, $params)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }
        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $listUsers = $this->userInterface->getAvailableUserListOfDivision($id, $params)->paginate($pageSize);

        $data = [
            'users' => $listUsers->items(),
            'items_total' => $listUsers->total()
        ];

        return _success($data, __('message.division_show_success'), HTTP_SUCCESS);
    }

    // Add available users to division
    public function addAvailableUsers($id, $userId)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }

        $user = $this->userInterface->find($userId);
        $companyId =  $auth->company;

        if (
            !$user->hasRole(USER_ROLE) ||
            $user->company != $companyId
        ) {
            return _error(null, __('message.user_incorrect'), HTTP_BAD_REQUEST);
        }

        if ($user->division == $id) {
            return _error(null, __('message.user_exists_division'), HTTP_BAD_REQUEST);
        }

        $addAvailableUsers = $this->divisionInterface->addAvailableUsers($id, $user->id);
        if ($addAvailableUsers) {
            return _error(null, __('message.created_fail'), HTTP_SUCCESS);
        }

        return _success(null, __('message.created_success'), HTTP_SUCCESS);
    }

    // Destroy available users
    public function destroyAvailableUsers($id, $userId)
    {
        $authUser = auth()->user();
        $checkDivision = $this->checkDivision($id, $authUser);
        if ($checkDivision) {
            return $checkDivision;
        }

        $user = $this->userInterface->find($userId);
        if ($user->division != $id) {
            return _error(null, __('message.user_incorrect'), HTTP_BAD_REQUEST);
        }

        if ($authUser->hasRole(MANAGER_ROLE)) {
            if (!$authUser->divisions->contains('id', $id)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }
        }

        $this->divisionInterface->destroyUser($userId);
        return _success(null, __('部署に営業担当者を成功に削除'), HTTP_SUCCESS);
    }


    // Check division
    protected function checkDivision($id, $user)
    {
        $user = $this->userInterface->find($user->id);

        $company = $user->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }

        $division = $this->divisionInterface->find($id);

        if (!$division) {
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        } else if ($division->company_id != $company->id) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }

        return false;
    }

    // Project division
    public function indexProject($request, $id)
    {
        $auth = auth()->user();
        $checkDivision = $this->checkDivision($id, $auth);
        if ($checkDivision) {
            return $checkDivision;
        }

        $pageSize = $request->page_size ?? PAGE_SIZE;
        $countProjectDivision = $this->projectInterface->countProjectDivision($id);
        $listProjectDivisions = $this->projectInterface->listProjectDivision($request, $id)->paginate($pageSize);

        $data = [
            'object_name' => $this->divisionInterface->find($id)->name,
            'quantity_project' => $countProjectDivision,
            'projects' => $listProjectDivisions->items(),
            'items_total' => $listProjectDivisions->total(),
            'current_page' => $request->page,
        ];

        return _success($data, __('success'), HTTP_SUCCESS);
    }
}

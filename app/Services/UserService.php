<?php

namespace App\Services;

use App\Exports\EmployeeExport;
use App\Exports\ManagerExport;
use App\Exports\UserExport;
use App\Repositories\Certificate\CertificateRepositoryInterface;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectUser\ProjectUserRepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\CalendarUser\CalendarUserRepositoryInterface;
use App\Repositories\UserColor\UserColorRepositoryInterface;
use App\Repositories\UserDivision\UserDivisionRepositoryInterface;
use App\Repositories\UserRole\UserRoleRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $userInterface;
    protected $projectInterface;
    protected $certificateInterface;
    protected $projectUserInterface;
    protected $roleInterface;
    protected $userDivisionInterface;
    protected $divisionInterface;
    protected $fileService;
    protected $calendarUserInterface;
    protected $userRoleRepositoryInterface;
    protected $userColorRepositoryInterface;

    public function __construct(
        UserRepositoryInterface $userInterface,
        ProjectRepositoryInterface $projectInterface,
        CertificateRepositoryInterface $certificateInterface,
        ProjectUserRepositoryInterface $projectUserInterface,
        RoleRepositoryInterface $roleInterface,
        UserDivisionRepositoryInterface $userDivisionInterface,
        DivisionRepositoryInterface $divisionInterface,
        FileService $fileService,
        CalendarUserRepositoryInterface $calendarUserInterface,
        UserRoleRepositoryInterface $userRoleRepositoryInterface,
        UserColorRepositoryInterface $userColorRepositoryInterface
    ) {
        $this->userInterface = $userInterface;
        $this->projectInterface = $projectInterface;
        $this->certificateInterface = $certificateInterface;
        $this->projectUserInterface = $projectUserInterface;
        $this->roleInterface = $roleInterface;
        $this->userDivisionInterface = $userDivisionInterface;
        $this->divisionInterface = $divisionInterface;
        $this->fileService = $fileService;
        $this->calendarUserInterface = $calendarUserInterface;
        $this->userRoleRepositoryInterface = $userRoleRepositoryInterface;
        $this->userColorRepositoryInterface = $userColorRepositoryInterface;
    }

    // Detail user after login
    public function show($id)
    {
        $user = $this->userInterface->find($id);
        $companyId = $user->company;
        $divisionId = $user->division;
        $divisions = $this->divisionInterface->getDivisionListOfManager($id)->get();
        $showUser = $this->userInterface->show($id);
        if ($user->hasRole(MANAGER_ROLE)) {
            $listManagement = $this->userInterface->listManagement($companyId);
        } else if ($user->hasRole(USER_ROLE)) {
            $listManagement = $this->userInterface->listManagementStaff($companyId, $divisionId);
        } else if ($user->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
            $listManagement = null;
        }
        $data = [
            'user' => $showUser,
            'division_account_current' => $divisions,
            'management' => $listManagement
        ];

        return _success($data, __('message.show_info_success'), HTTP_SUCCESS);
    }

    //Project User
    public function indexProject($request, $id)
    {
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $countProjectUser = $this->projectInterface->countProjectUser($id);
        $listProjectUser = $this->projectInterface->getProjectUser($request, $id)->paginate($pageSize);
        $data = [
            'object_name' => $this->userInterface->find($id)->first_name . $this->userInterface->find($id)->last_name,
            'quantity_project' => $countProjectUser,
            'projects' => $listProjectUser->items(),
            'items_total' => $listProjectUser->total(),
            'current_page' => $request->page,
        ];
        return _success($data, __('success'), HTTP_SUCCESS);
    }

    // all user in company
    public function indexCompanyUser($params)
    {
        $user = auth()->user();
        $companyId = $user->company;
        $listUsers = $this->userInterface->indexCompanyUser($companyId, $params);
        $itemTotal =  $listUsers->count();
        $users = _paginateCustom($listUsers, $params);
        $data = [
            'users' => $users,
            'items_total' => $itemTotal,
        ];
        return _success($data, __('message.list_success'), HTTP_SUCCESS);
    }

    // Delete account employee
    public function delete($id, $auth)
    {
        $user = $this->userInterface->find($id);
        $divisionId = $user->division;
        $checkCompany = $this->checkCompany($id, $auth);
        if ($checkCompany) {
            return $checkCompany;
        }
        $existProject = $this->projectUserInterface->getProjectInProgressUser($id)->exists();
        if (($user->hasRole(MANAGER_ROLE) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE))) ||
            (($user->hasRole(USER_ROLE)) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE) || ($auth->hasRole(MANAGER_ROLE) && $auth->divisions->contains('id', $divisionId))))
        ) {
            if ($existProject) {
                return _error(null, __('message.related_user_project_exists'),  HTTP_BAD_REQUEST);
            }
            $this->userInterface->delete($id);
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        } else {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
    }

    public function checkCompany($id, $auth)
    {
        $user = $this->userInterface->find($auth->id);
        $company = $user->company()->first();

        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $staff = $this->userInterface->find($id);

        if (!$staff) {
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        } else if ($staff->company != $company->id) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }

        return false;
    }

    public function indexDivisionUser($request)
    {
        $user = auth()->user();
        $companyId = $user->company;
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $users = $this->userInterface->indexDivisionUser($companyId, $user, $request)->paginate($pageSize);

        $data = [
            'users' => $users->items(),
            'items_total' => $users->total()
        ];
        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    // create account user
    public function create($request, $auth, $roleId)
    {
        $company = $auth->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $user = $this->userInterface->createUser($request, $auth);
        $userId = $user->id;
        $atrtibutes = array(
            'user_id' => $userId,
            'role_id' => $roleId
        );
        $this->roleInterface->create($atrtibutes);
        if ($request->divisions) {
            $divisionIds = $request->divisions;
            $divisions = [];
            if ($roleId == MANAGER) {
                foreach ($divisionIds as $divisionId) {
                    $divisions[] = [
                        'user_id' => $userId,
                        'division_id' => $divisionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $this->userDivisionInterface->insert($divisions);
            } else {
                $user->division = $divisionIds[0];
                $user->commission_rate = $request->commission_rate ?? NO_COMMISSION_RATE;
                $user->save();
            }
        }
        if ($roleId == USER) {
            $user->commission_rate = $request->commission_rate ?? NO_COMMISSION_RATE;
            $user->save();
        } else {
            $user->commission_rate = COMMISSION_RATE_MAX;
            $user->save();
        }
        return $user;
    }

    // List user in company
    public function list($request, $roleId)
    {
        $user = auth()->user();
        $userId = $user->id;
        $companyId = $user->company;
        $divisions = $this->divisionInterface->getDivisionListOfManager($userId)->get();
        $params = $request->all();
        $pageSize = $request->page_size ?? PAGE_SIZE;
        if ($roleId == MANAGER) {
            $listManagers = $this->userInterface->listManagerInCompany($companyId, $params)->paginate($pageSize);
            $data = [
                'employees' => $listManagers->items(),
                'items_total' => $listManagers->total()
            ];
        } else if ($roleId == USER) {
            $listUsers = $this->userInterface->listInCompany($companyId, $params)->paginate($pageSize);
            $data = [
                'employees' =>  $listUsers->items(),
                'division_account_current' => $divisions,
                'items_total' => $listUsers->total()
            ];
        } else {
            $data = [];
        }

        return _success($data, __('message.list_success'), HTTP_SUCCESS);
    }

    // Export list user in company
    public function export($params, $auth, $roleId)
    {
        $companyId = $auth->company;
        $userId = $auth->id;
        $currentDate = date('Ymd');
        if ($roleId == MANAGER) {
            $managers = $this->userInterface->listManagerInCompany($companyId, $params)->get();
            $fileName = $currentDate .  __('filename.export_manager');
            $filePath = 'employee/' . $userId . '/' . $fileName;
            $exportedObject = new ManagerExport($managers);
        } else if ($roleId == USER) {
            $users = $this->userInterface->listInCompany($companyId, $params)->get();
            $fileName = $currentDate .  __('filename.export_staff');;
            $filePath = 'employee/' . $userId . '/' . $fileName;
            $exportedObject = new UserExport($users);
        }
        $link = $this->fileService->saveFileToS3($filePath, $exportedObject);
        $data = ['link' => $link];
        return _success($data, __('message.export_success'), HTTP_SUCCESS);
    }

    // Update info
    public function update($request, $id, $auth)
    {
        $user = $this->userInterface->find($id);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $divisionId = $user->division;
        $attributes = $request->toArray();
        $userRole = $this->userRoleRepositoryInterface->findId($id);
        $roleId = $request->role_id;
        $checkCompany = $this->checkCompany($id, $auth);
        if ($checkCompany) {
            return $checkCompany;
        }
        if (
            $auth->id == $id ||
            ($user->hasRole(MANAGER_ROLE) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE))) ||
            ($user->hasRole(USER_ROLE) && $auth->hasRole(ADMIN_CMS_COMPANY_ROLE)) ||
            ($auth->hasRole(MANAGER_ROLE) && $auth->divisions->contains('id', $divisionId))
        ) {
            if (isset($roleId) && $auth->hasRole(USER_ROLE)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            } else {
                $this->userRoleRepositoryInterface->update($userRole->id, ['role_id' => $roleId]);
            }
            if (isset($attributes['divisions'])) {
                $newDivisions = $attributes['divisions'];

                if ((($roleId == USER)) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE) || ($auth->hasRole(MANAGER_ROLE) && $auth->divisions->contains('id', $divisionId)))) {
                    $existProject = $user->projects()->exists();
                    if ($user->hasRole(MANAGER_ROLE)) {
                        $currentDivisions = $user->divisions()->pluck('divisions.id')->toArray();
                        $deleteDivisions = array_diff($currentDivisions, $newDivisions);
                        if (count($deleteDivisions) > 0) {
                            foreach ($deleteDivisions as $deleteDivision) {
                                $existProject = $user->projects->contains('division_id', $deleteDivision);
                                if ((!($user->divisions->contains('division_id', $deleteDivision)) || $user->divisions = null) && $existProject) {
                                    return _error(null, __('message.unable_remove_employee_department'), HTTP_SUCCESS);
                                }
                            }
                            // $user->divisions()->detach($deleteDivisions);
                        }
                    }
                    if (count($newDivisions) > 0) {
                        if (($user->division != $newDivisions[0]) && $existProject) {
                            return _error(null, __('message.project_exists_user'), HTTP_SUCCESS);
                        }
                        $attributes['division'] = $attributes['divisions'][0];
                    } else {
                        $user->division = null;
                        $user->save();
                    }
                } else if (($roleId == MANAGER) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE))) {
                    $currentDivisions = $user->divisions()->pluck('divisions.id')->toArray();
                    $addDivisions = array_diff($newDivisions, $currentDivisions);
                    $deleteDivisions = array_diff($currentDivisions, $newDivisions);
                    if (count($deleteDivisions) > 0) {
                        foreach ($deleteDivisions as $deleteDivision) {
                            $existProject = $user->projects->contains('division_id', $deleteDivision);
                            if ((!($user->divisions->contains('division_id', $deleteDivision)) || $user->divisions = null) && $existProject) {
                                return _error(null, __('message.unable_remove_employee_department'), HTTP_SUCCESS);
                            }
                        }
                        $user->divisions()->detach($deleteDivisions);
                    }

                    if (count($addDivisions)) {
                        $user->divisions()->attach($addDivisions);
                    }
                }
            }

            $this->userInterface->update($id, $attributes);
            $data = $this->userInterface->show($id);
            return _success($data, __('message.updated_success'), HTTP_CREATED);
        } else {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
    }

    // Update avatar
    public function updateAvatar($request, $id, $auth)
    {
        $user = $this->userInterface->find($id);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $divisionId = $user->division;
        if (!$auth->hasRole(ADMIN_CMS_SYSTEM_ROLE)) {
            $checkCompany = $this->checkCompany($id, $auth);
            if ($checkCompany) {
                return $checkCompany;
            }
        }
        if (
            $auth->id == $id ||
            ($user->hasRole(MANAGER_ROLE) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE))) ||
            $auth->hasRole(ADMIN_CMS_SYSTEM_ROLE) ||
            (($user->hasRole(USER_ROLE)) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE) ||
                ($auth->hasRole(MANAGER_ROLE) && $auth->divisions->contains('id', $divisionId))))
        ) {
            if (isset($request->avatar)) {
                $file = $request->avatar;
                $filePath = 'public/user/' . $user->id . '/avatar';
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);
                $user->avatar = $fileUrl;
                $user->save();
            }
            return _success($user->avatar, __('message.updated_success'), HTTP_CREATED);
        } else {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
    }

    // Add division
    public function addAvailableDivisions($request, $id, $auth)
    {
        $user = $this->userInterface->find($id);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $divisionId = $user->division;
        $divisionIds = $request->divisions;
        $checkCompany = $this->checkCompany($id, $auth);
        if ($checkCompany) {
            return $checkCompany;
        }
        if (($user->hasRole(MANAGER_ROLE) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE)))) {
            foreach ($divisionIds as $divisionId) {
                $existDivision = $user->divisions()->where('divisions.id',  $divisionId)->exists();
                if (!$existDivision) {
                    $user->divisions()->attach($divisionId);
                } else {
                    $data = [
                        'division_id' => $divisionId
                    ];
                    return _error($data, __('message.division_already_exists'), HTTP_SUCCESS);
                }
            }
        } else if (($user->hasRole(USER_ROLE)) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE) || ($auth->hasRole(MANAGER_ROLE) && $auth->divisions->contains('id', $divisionId)))) { {
                $existProject = $user->projects()->exists();
                if (!$existProject) {
                    $user->division = $divisionIds[0];
                    $user->save();
                } else {
                    return _error(null, __('message.project_exists_user'), HTTP_SUCCESS);
                }
            }
        } else {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        return _success(null, __('message.division_added_success'), HTTP_CREATED);
    }

    // get list available divisions to add
    public function getAddAvailableDivisions($request, $id, $auth)
    {
        $user = $this->userInterface->find($id);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $params = $request->all();
        $pageSize = $request->page_size ?? PAGE_SIZE;
        if ($user->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
            $data = [
                'divisions' =>  [],
                'items_total' => 0
            ];
        } else {
            if ($user->hasRole(USER_ROLE)) {
                $listDivisions = $this->divisionInterface->find($user->division);
                return _success($listDivisions, __('message.show_available_division_success'), HTTP_SUCCESS);
            } else if ($user->hasRole(MANAGER_ROLE)) {
                $listDivisions = $this->divisionInterface->getAvailableDivisionListOfManager($id, $params, $auth);
            }
            $listDivisions = $listDivisions->paginate($pageSize);
            $data = [
                'divisions' =>  $listDivisions->items(),
                'items_total' => $listDivisions->total()
            ];
        }
        return _success($data, __('message.show_available_division_success'), HTTP_SUCCESS);
    }

    // Show divisions
    public function showDivisions($id, $request)
    {
        $user = $this->userInterface->find($id);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $pageSize = $request->page_size ?? PAGE_SIZE;
        if ($user->hasRole(MANAGER_ROLE)) {
            $listDivisions = $this->divisionInterface->getDivisionListOfManager($id);
        }
        $listDivisions = $listDivisions->paginate($pageSize);
        $data = [
            'divisions' =>  $listDivisions->items(),
            'items_total' => $listDivisions->total()
        ];
        return _success($data, __('message.division_list_success'), HTTP_SUCCESS);
    }

    // Destroy available divisions
    public function destroyAvailableDivision($id, $divisionId, $auth)
    {
        $user = $this->userInterface->find($id);
        $division = $this->divisionInterface->find($divisionId);
        if (!$user) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        if (!$division) {
            return _error(null, __('message.division_incorrect'), HTTP_BAD_REQUEST);
        }
        if (
            ($user->hasRole(MANAGER_ROLE) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE))) ||
            (($user->hasRole(USER_ROLE)) && ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE) || ($auth->hasRole(MANAGER_ROLE) && $auth->divisions->contains('id', $divisionId))))
        ) {
            $existProject = $user->projects->contains('division_id', $divisionId);
            if ($existProject) {
                return _error(null, __('message.unable_remove_employee_department'), HTTP_SUCCESS);
            }
            if ($user->hasRole(MANAGER_ROLE)) {
                $this->userDivisionInterface->deleteDivision($id, $divisionId);
            } else {
                $user->division = null;
                $user->save();
            }
            return _success(null, __('message.deleted_success'), HTTP_CREATED);
        } else {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
    }

    public function countTotalNotifies()
    {
        $user = auth()->user();
        $companyId = $user->company;
        if ($user->hasRole(MANAGER_ROLE)) {
            $divisionIds = $user->divisions->pluck('id');
            $closeProjectNotifies = $this->projectInterface->countRequestClose($companyId, $divisionIds);
        }

        if ($user->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
            $closeProjectNotifies = $this->projectInterface->countRequestClose($companyId, null);
        }

        if ($user->hasRole(USER_ROLE)) {
            $closeProjectNotifies = $this->projectUserInterface->countProjectRequestClose($user->id);
        }

        $data = [
            'request_close_project' => $closeProjectNotifies
        ];
        return _success($data, __('message.success'), HTTP_SUCCESS);
    }


    public function exportEmployee($request)
    {
        $user = Auth()->user();
        $companyId = $user->company;
        $userId = $user->id;
        $params = $request->all();
        $currentDate = date('Ymd');
        $employee = $this->userInterface->indexCompanyUser($companyId, $params)->get();
        foreach ($employee as $user) {
            if (count($user->divisions) > 0) {
                $divisionName = "";
                foreach ($user->divisions as $division) {
                    $divisionName = $divisionName . $division['name'] . ', ';
                }
                $user['all_division_name'] = $divisionName;
            } else {
                $user['all_division_name'] = "";
            }
        }
        $fileName = $currentDate .  __('filename.export_manager');
        $filePath = 'employee/' . $userId . '/' . $fileName;
        $exportedObject = new EmployeeExport($employee);
        $link = $this->fileService->saveFileToS3($filePath, $exportedObject);
        $data = ['link' => $link];
        return _success($data, __('message.export_success'), HTTP_SUCCESS);
    }


    // create account user
    public function createEmployee($request, $auth)
    {
        $company = $auth->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $user = $this->userInterface->createUser($request, $auth);
        $userId = $user->id;
        $roleId = $request->role_id;
        if ($roleId != null) {
            $atrtibutes = array(
                'user_id' => $userId,
                'role_id' => $roleId
            );
            $this->userRoleRepositoryInterface->create($atrtibutes);
        }
        if ($request->divisions) {
            $divisionIds = $request->divisions;
            $divisions = [];
            if ($roleId == MANAGER) {
                foreach ($divisionIds as $divisionId) {
                    $divisions[] = [
                        'user_id' => $userId,
                        'division_id' => $divisionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $this->userDivisionInterface->insert($divisions);
            } else {
                $user->division = $divisionIds[0];
                $user->commission_rate = $request->commission_rate ?? NO_COMMISSION_RATE;
                $user->save();
            }
        }
        if ($roleId == USER) {
            $user->commission_rate = $request->commission_rate ?? NO_COMMISSION_RATE;
            $user->save();
        } else {
            $user->commission_rate = COMMISSION_RATE_MAX;
            $user->save();
        }

        return $this->userInterface->show($userId);
    }

    public function changeAuthenticationCms($request)
    {
        $user = Auth::user();
        return $this->userInterface->update($user->id, ['authentication' => $request->status]);
    }
    public function createUserColor($request)
    {
        $user = Auth::user();
        $color = $this->userColorRepositoryInterface->getColorByUserID($user->id);
        if ($color) {
            if ($request->type == COLOR_APP) {
                return $this->userColorRepositoryInterface->update($color->id, ['color_app' => json_encode($request->color)]);
            } else {
                return $this->userColorRepositoryInterface->update($color->id, ['color_web' => json_encode($request->color)]);
            }
        } else {
            if ($request->color) {
                if ($request->type == COLOR_APP) {
                    return $this->userColorRepositoryInterface->create([
                        'user_id' => $user->id,
                        'color_app' => json_encode($request->color)
                    ]);
                } else {
                    return $this->userColorRepositoryInterface->create([
                        'user_id' => $user->id,
                        'color_web' => json_encode($request->color)
                    ]);
                }
            }
        }
    }
}

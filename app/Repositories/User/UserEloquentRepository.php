<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Base\BaseEloquentRepository;
use App\Models\UserDeviceToken;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserEloquentRepository extends BaseEloquentRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function findOneByEmail($email)
    {
        return $this->_model->where('email', $email)->first();
    }

    public function getUserListOfDivision($divisionId, $params)
    {
        $query = $this->_model->select([
            'users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name', 'email', 'phone', 'avatar',
            'master_positions.name as position_name'
        ])
            ->where('division', $divisionId)
            ->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', USER_ROLE);
            })
            ->leftJoin('master_positions', 'users.position', 'master_positions.id');

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $query->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }
        return $query->orderBy('id', 'DESC');
    }

    public function getManagerUserListOfDivision($divisionId, $companyId, $params)
    {
        $query = $this->_model->select([
            'users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name', 'email', 'phone', 'user_roles.role_id', 'avatar',
            'master_positions.name as position_name'
        ])
            ->where(function ($roleQuery) use ($divisionId, $companyId) {
                $roleQuery
                    ->where('division', $divisionId)
                    ->whereHas('roles', function ($query) {
                        $query->where('roles.slug', '=', USER_ROLE);
                    })->orWhere(function ($q) use ($divisionId) {
                        $q->whereHas('roles', function ($query) {
                            $query->where('roles.slug', '=', MANAGER_ROLE);
                        })
                            ->whereHas('divisions', function ($query) use ($divisionId) {
                                $query->where('divisions.id', '=', $divisionId);
                            });
                    })
                    ->orWhere(function ($q) use ($companyId) {
                        $q->whereHas('roles', function ($query) use ($companyId) {
                            $query->where('roles.slug', '=', ADMIN_CMS_COMPANY_ROLE)
                                ->where('users.company', $companyId);
                        });
                    });
            })
            ->leftJoin('master_positions', 'users.position', 'master_positions.id')
            ->join('user_roles', 'users.id', 'user_roles.user_id')
            ->orderBy('id', 'DESC');

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $query->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }

        return $query;
    }

    public function getAvailableManagerListOfDivision($divisionId, $params)
    {
        $companyId = auth()->user()->company;

        $managerList = $this->_model->select([
            'users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name',
            'master_positions.name as position_name'
        ])
            ->where('company', $companyId)
            ->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', MANAGER_ROLE);
            })
            ->whereDoesntHave('divisions', function ($query) use ($divisionId) {
                $query->where('divisions.id', '=', $divisionId);
            })
            ->leftJoin('master_positions', 'users.position', 'master_positions.id')
            ->orderBy('id', 'DESC');

        if (isset($params['position_id'])) {
            $positionId = $params['position_id'];

            $managerList->where('master_positions.id', $positionId);
        }

        if (isset($params['other_division_id'])) {
            $otherDivisionId = $params['other_division_id'];

            if ($otherDivisionId) {
                $managerList->whereHas('divisions', function ($query) use ($otherDivisionId) {
                    $query->where('divisions.id', '=', $otherDivisionId);
                });
            } else {
                $managerList->doesntHave('divisions');
            }
        }

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $managerList->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }

        return $managerList;
    }

    public function getAvailableUserListOfDivision($divisionId, $params)
    {
        $companyId = auth()->user()->company;

        $userList = $this->_model->select([
            'users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name',
            'master_positions.name as position_name'
        ])
            ->where('company', $companyId)
            ->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', USER_ROLE);
            })
            ->where(function ($query) {
                $query->Where('division', null);
            })
            ->leftJoin('master_positions', 'users.position', 'master_positions.id')
            ->orderBy('id', 'DESC');

        if (isset($params['position_id'])) {
            $positionId = $params['position_id'];

            $userList->where('master_positions.id', $positionId);
        }

        if (isset($params['other_division_id'])) {
            $otherDivisionId = $params['other_division_id'];

            if ($otherDivisionId) {
                $userList->where('division', $otherDivisionId);
            } else {
                $userList->whereNull('division');
            }
        }

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $userList->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }

        return $userList;
    }

    // List users in company
    public function listInCompany($companyId, $params)
    {
        $listUser = $this->_model->select([
            'users.*',
            'master_positions.name as position_name',
            'divisions.name as division_name',
            'roles.name as role_name'
        ])
            ->leftJoin(
                'master_positions',
                'users.position',
                'master_positions.id'
            )
            ->leftJoin(
                'divisions',
                'users.division',
                'divisions.id'
            )
            ->join('user_roles as ur', 'ur.user_id', 'users.id')
            ->join('roles', 'ur.role_id', 'roles.id')
            ->where('company', $companyId)
            ->where('role_id', USER);

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $listUser->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }

        if (isset($params['division'])) {
            $divisionId = $params['division'];
            $listUser->where('division',  $divisionId);
        }
        $listUser->orderBy('id', 'DESC');

        return $listUser;
    }

    // Create account User
    public function createUser($request, $auth)
    {
        $attributes = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'kana_first_name' => $request->kana_first_name,
            'kana_last_name' => $request->kana_last_name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'position' => $request->position,
            'company' => $auth->company,
        ];
        $user = $this->create($attributes);
        return $user;
    }

    // Update division for User
    public function updateDivisionUser($request, $id)
    {
        $staff = $this->find($id);
        $staff->division = $request->division_id;
        $staff->save();
    }

    // detail user
    public function show($userId)
    {
        return $this->_model->where('users.id', $userId)
            ->select([
                'users.*',
                'master_positions.name as position_name',
                'divisions.name as division_name',
                'companies.name as company_name',
                'user_roles.role_id as role_id',
                'roles.name as role_name'
            ])
            ->leftJoin('master_positions', 'users.position', 'master_positions.id')
            ->leftJoin('divisions', 'users.division', 'divisions.id')
            ->leftJoin('companies', 'users.company', 'companies.id')
            ->join('user_roles', 'users.id', 'user_roles.user_id')
            ->leftJoin('roles', 'roles.id', 'user_roles.role_id')
            ->with('divisions', 'certificates', 'projectIds')
            ->first();
    }

    // List management manager
    public function listManagement($companyId)
    {
        return $this->_model->where('company', $companyId)
            ->select('users.first_name', 'last_name', 'kana_first_name', 'kana_last_name')
            ->join('user_roles as usr', 'users.id', 'usr.user_id')
            ->where('usr.role_id', ADMIN_CMS_COMPANY)
            ->get();
    }

    public function listManagementStaff($companyId, $divisionId)
    {
        return $this->_model->where('company', $companyId)
            ->select('users.first_name', 'last_name', 'kana_first_name', 'kana_last_name')
            ->join('user_roles as usr', 'users.id', 'usr.user_id')
            ->where('usr.role_id', MANAGER)
            ->join('user_divisions as usd', 'users.id', 'usd.user_id')
            ->where('usd.division_id', $divisionId)
            ->get();
    }

    // Device token
    public function listDeviceToken($userId)
    {
        return UserDeviceToken::whereIn('user_id', $userId)->pluck('device_token')->toArray();
    }

    // List user contract ranking
    public function indexContractRanking($params, $companyId, $startDate = null, $endDate = null, $limit = null, $user = null)
    {
        $query = $this->_model
            ->leftJoin('divisions', 'divisions.id', 'users.division')
            ->leftJoin('user_roles', 'user_roles.user_id', 'users.id')
            ->select('users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name', 'avatar', 'divisions.name as division_name', 'user_roles.role_id', 'close_project_date')
            ->with(['divisions' => function ($query) {
                $query->select('divisions.id', 'name');
            }])
            ->where('users.company', $companyId);
        if ($startDate != null && $endDate != null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate) {
                $query->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereBetween('projects.updated_at', [$startDate, $endDate]);
            }]);
        } elseif ($startDate == null && $endDate) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($endDate) {
                $query->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereDate('projects.updated_at', '<=', $endDate);
            }]);
        } elseif ($startDate && $endDate == null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate) {
                $query->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereDate('projects.updated_at', '>=', $startDate);
            }]);
        } else {
            $query->withCount(['projects as evaluate_ranking' => function ($query) {
                $query->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE);
            }]);
        }
        if (isset($params['divisions'])) {
            $divisionIds = $params['divisions'];
            $query->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', MANAGER_ROLE);
            })
                ->whereHas('divisions', function ($query) use ($divisionIds) {
                    $query->whereIn('divisions.id', $divisionIds);
                })->orWhere(function ($q) use ($divisionIds) {
                    $q->whereHas('roles', function ($query) {
                        $query->where('roles.slug', '=', USER_ROLE);
                    })
                        ->whereIn('division', $divisionIds);
                });
        }
        $query->orderBy('evaluate_ranking', 'DESC')
            ->orderBy('close_project_date', 'ASC')
            ->orderBy('users.id', 'DESC');

        $userIds = $query->pluck('users.id')->toArray();
        $userIndex = $query->get()->search(function ($user) {
            return $user->id === Auth::id();
        });

        $authRanking = null;
        if ($user != null) {
            if (!isset($params['divisions']) || (isset($params['divisions']) && in_array($user->id, $userIds))) {
                $userIndex = ['user_index' => $userIndex + POSITION];
                $listAuthRanking =  $this->listAuthRanking($companyId,  $user->id, $startDate, $endDate, CONTRACT_RANKING_TYPE);
                $authRanking = array_merge($listAuthRanking,  $userIndex);
            }
        }
        if ($limit != null) {
            $users =  $query->limit($limit)->get();
        } else {
            $users = $query->get();
        }

        return [
            'users' => $users,
            'auth_ranking' => $authRanking
        ];
    }

    // List user revenue ranking
    public function indexRevenueRanking($params, $companyId, $startDate = null, $endDate = null, $limit = null, $user = null)
    {
        $query =   $this->_model
            ->leftJoin('divisions', 'divisions.id', 'users.division')
            ->leftJoin('user_roles', 'user_roles.user_id', 'users.id')
            ->select('users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name', 'avatar', 'divisions.name as division_name', 'user_roles.role_id', 'close_project_date')
            ->with(['divisions' => function ($query) {
                $query->select('divisions.id', 'name');
            }])
            ->where('users.company', $companyId);
        if ($startDate != null && $endDate != null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('ROUND(SUM(price * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereBetween('projects.updated_at', [$startDate, $endDate]);
            }]);
        } elseif ($startDate == null && $endDate) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($endDate) {
                $query->select(DB::raw('ROUND(SUM(price * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereDate('projects.updated_at', '<=', $endDate);
            }]);
        } elseif ($startDate && $endDate ==  null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate) {
                $query->select(DB::raw('ROUND(SUM(price * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereDate('projects.updated_at', '>=', $startDate);
            }]);
        } else {
            $query->withCount(['projects as evaluate_ranking' => function ($query) {
                $query->select(DB::raw('ROUND(SUM(price * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE);
            }]);
        }

        if (isset($params['divisions'])) {
            $divisionIds = $params['divisions'];
            $query->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', MANAGER_ROLE);
            })
                ->whereHas('divisions', function ($query) use ($divisionIds) {
                    $query->whereIn('divisions.id', $divisionIds);
                })->orWhere(function ($q) use ($divisionIds) {
                    $q->whereHas('roles', function ($query) {
                        $query->where('roles.slug', '=', USER_ROLE);
                    })
                        ->whereIn('division', $divisionIds);
                });
        }
        $query->orderBy('evaluate_ranking', 'DESC')
            ->orderBy('close_project_date', 'ASC')
            ->orderBy('users.id', 'DESC');

        $userIds = $query->pluck('users.id')->toArray();
        $userIndex = $query->get()->search(function ($user) {
            return $user->id === Auth::id();
        });

        $authRanking = null;
        if ($user != null) {
            if (!isset($params['divisions']) || (isset($params['divisions']) && in_array($user->id, $userIds))) {
                $userIndex = ['user_index' => $userIndex + POSITION];
                $listAuthRanking =  $this->listAuthRanking($companyId,  $user->id, $startDate, $endDate, REVENUE_RANKING_TYPE);
                $authRanking = array_merge($listAuthRanking,  $userIndex);
            }
        }
        if ($limit != null) {
            $users =  $query->limit($limit)->get();
        } else {
            $users = $query->get();
        }

        return [
            'users' => $users,
            'auth_ranking' => $authRanking
        ];
    }

    // List user brokerage ranking
    public function indexBrokerageRanking($params, $companyId, $startDate = null, $endDate = null, $limit = null, $user = null)
    {
        $query =  $this->_model
            ->leftJoin('divisions', 'divisions.id', 'users.division')
            ->leftJoin('user_roles', 'user_roles.user_id', 'users.id')
            ->select('users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name', 'avatar', 'divisions.name as division_name', 'user_roles.role_id', 'close_project_date')
            ->with(['divisions' => function ($query) {
                $query->select('divisions.id', 'name');
            }])
            ->where('users.company', $companyId);
        if ($startDate != null && $endDate != null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('ROUND(SUM(revenue * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereBetween('projects.updated_at', [$startDate, $endDate]);
            }]);
        } elseif ($startDate == null && $endDate) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($endDate) {
                $query->select(DB::raw('ROUND(SUM(revenue * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereDate('projects.updated_at', '<=', $endDate);
            }]);
        } elseif ($startDate && $endDate == null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate) {
                $query->select(DB::raw('ROUND(SUM(revenue * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->whereDate('projects.updated_at', '>=', $startDate);
            }]);
        } else {
            $query->withCount(['projects as evaluate_ranking' => function ($query) {
                $query->select(DB::raw('ROUND(SUM(revenue * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE);
            }]);
        }

        if (isset($params['divisions'])) {
            $divisionIds = $params['divisions'];
            $query->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', MANAGER_ROLE);
            })
                ->whereHas('divisions', function ($query) use ($divisionIds) {
                    $query->whereIn('divisions.id', $divisionIds);
                })->orWhere(function ($q) use ($divisionIds) {
                    $q->whereHas('roles', function ($query) {
                        $query->where('roles.slug', '=', USER_ROLE);
                    })
                        ->whereIn('division', $divisionIds);
                });
        }

        $query->orderBy('evaluate_ranking', 'DESC')
            ->orderBy('close_project_date', 'ASC')
            ->orderBy('users.id', 'DESC');
        $userIds = $query->pluck('users.id')->toArray();
        $userIndex = $query->get()->search(function ($user) {
            return $user->id === Auth::id();
        });

        $authRanking = null;
        if ($user != null) {
            if (!isset($params['divisions']) || (isset($params['divisions']) && in_array($user->id, $userIds))) {
                $userIndex = ['user_index' => $userIndex + POSITION];
                $listAuthRanking =  $this->listAuthRanking($companyId,  $user->id, $startDate, $endDate, BROKERAGE_RAKING_TYPE);
                $authRanking = array_merge($listAuthRanking,  $userIndex);
            }
        }
        if ($limit != null) {
            $users =  $query->limit($limit)->get();
        } else {
            $users = $query->get();
        }

        return [
            'users' => $users,
            'auth_ranking' => $authRanking
        ];
    }

    // All user in company
    public function indexCompanyUser($companyId, $params)
    {
        $userList = $this->_model->select([
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.kana_first_name',
            'users.kana_last_name',
            'users.avatar',
            'users.email',
            'users.phone',
            'users.last_login',
            'master_positions.name as position_name',
            'companies.name as company_name',
            'divisions.name as division_name',
            'user_roles.role_id as role_id',
            'roles.name as role_name',
        ])
            ->leftJoin('user_roles', 'users.id', 'user_roles.user_id')
            ->leftJoin('roles', 'roles.id', 'user_roles.role_id')
            ->leftJoin('divisions', 'divisions.id', 'users.division')
            ->leftJoin('master_positions', 'master_positions.id', 'users.position')
            ->leftJoin('companies', 'companies.id', 'users.company')
            ->where('company', $companyId)
            ->where('user_roles.role_id', '<>', ADMIN_CMS_SYSTEM)
            ->with('divisions');

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $userList->where(DB::raw('concat(last_name, " ",first_name)'), 'like BINARY', "%" . $name . "%");
        }

        if (isset($params['division'])) {
            $divisionId = $params['division'];
            $userList->where('division',  $divisionId)
                ->orWhereHas(
                    'divisions',
                    function ($query) use ($divisionId) {
                        $query->where([
                            'division_id' => $divisionId
                        ]);
                    }
                );
        }

        if (isset($params['sort_by']) == 'role') {

            if ($params['sort_type'] == 'ASC') {
                $userList->orderBy('role_id', 'ASC');
            } else {
                $userList->orderBy('role_id', 'DESC');
            }
        }

        if (isset($params['role'])) {
            $userList = $userList->where('roles.id', $params['role']);
        }
        return $userList->orderBy('id', 'DESC');
    }

    public function indexDivisionUser($companyId, $user, $request)
    {
        $query = $this->_model->where('company', $companyId)->select([
            'users.id',
            'first_name',
            'last_name',
            'kana_first_name',
            'kana_last_name',
            'avatar',
            'email',
            'phone',
            'master_positions.name as position_name',
            'user_roles.role_id as role_id'
        ])
            ->join('user_roles', 'users.id', 'user_roles.user_id')
            ->join('master_positions', 'master_positions.id', 'users.position');

        if ($user->hasRole(MANAGER_ROLE)) {
            $userDivisions = $user->divisions;
            $divisionIds = [];
            foreach ($userDivisions as $userDivision) {
                array_push($divisionIds, $userDivision['id']);
            }
            $query->whereIn('users.division', $divisionIds);
        }

        if ($user->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
            $query->whereIn('user_roles.role_id', [USER, MANAGER]);
        }
        if (isset($request->name)) {
            $name = _trimSpace($request->name);
            $query->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }
        return $query->orderBy('users.id', 'DESC');
    }

    public function degreeDate($request)
    {
        $degreeDate = $request->degree_date;
        $year = substr($degreeDate, 0, -3);
        if (strlen($year) == 4) {
            $degreeDate = new DateTime($request->degree_date);
        } else if (strlen($year) == 3) {
            $degreeDate = new DateTime('0' . $request->degree_date);
        } else if (strlen($year) == 2) {
            $degreeDate = new DateTime('00' . $request->degree_date);
        } else {
            $degreeDate = new DateTime('000' . $request->degree_date);
        }
        return $degreeDate;
    }

    // List managers in company
    public function listManagerInCompany($companyId, $params)
    {
        $listManager = $this->_model->select([
            'users.*',
            'master_positions.name as position_name',
            'roles.name as role_name'
        ])
            ->leftJoin(
                'master_positions',
                'users.position',
                'master_positions.id'
            )
            ->join('user_roles as ur', 'ur.user_id', 'users.id')
            ->join('roles', 'ur.role_id', 'roles.id')
            ->where('company', $companyId)
            ->where('role_id', MANAGER)
            ->with('divisions');

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $listManager->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }

        if (isset($params['division'])) {
            $divisionId = $params['division'];
            $listManager->join('user_divisions as ud', 'ud.user_id', 'users.id')->where('ud.division_id', $divisionId);
        }
        $listManager->orderBy('id', 'DESC');
        return $listManager;
    }

    public function getUserCommission($userId)
    {
        $userProjects = $this->_model
            ->where('users.id', $userId)
            ->with('projects', function ($query) {
                $query->select(DB::raw(
                    '
                        MONTH(projects.updated_at) AS month,
                        YEAR(projects.updated_at) AS year,
                        COUNT(projects.id) AS project_count,
                        SUM(projects.price) AS revenue
                    '
                ))
                    ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                    ->where('projects.close_status', SUCCESS_CLOSE)
                    ->groupBy(DB::raw('month, year'))
                    ->orderBy(DB::raw('month'), 'DESC')
                    ->orderBy(DB::raw('year'), 'DESC');
            })->first();
        return $userProjects;
    }
    // index auth ranking
    public function listAuthRanking($companyId,  $userId, $startDate, $endDate, $type)
    {
        $query = $this->_model
            ->leftJoin('divisions', 'divisions.id', 'users.division')
            ->leftJoin('user_roles', 'user_roles.user_id', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name', 'avatar', 'divisions.name as division_name', 'user_roles.role_id')
            ->with(['divisions' => function ($query) {
                $query->select('divisions.id', 'name');
            }])
            ->where('users.company', $companyId);

        if ($type == CONTRACT_RANKING_TYPE) {
            if ($startDate != null && $endDate != null) {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate, $userId) {
                    $query->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('project_users.user_id', $userId)
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                        ->whereBetween('projects.updated_at', [$startDate, $endDate]);
                }]);
            } else {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($userId) {
                    $query->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('project_users.user_id', $userId)
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE);
                }]);
            }
        }

        if ($type == REVENUE_RANKING_TYPE) {
            if ($startDate != null && $endDate != null) {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate, $userId) {
                    $query->select(DB::raw('ROUND(SUM(price * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                        ->where('project_users.user_id', $userId)
                        ->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                        ->whereBetween('projects.updated_at', [$startDate, $endDate]);
                }]);
            } else {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($userId) {
                    $query->select(DB::raw('ROUND(SUM(price * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                        ->where('project_users.user_id', $userId)
                        ->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE);
                }]);
            }
        }

        if ($type == BROKERAGE_RAKING_TYPE) {
            if ($startDate != null && $endDate != null) {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate, $userId) {
                    $query->select(DB::raw('ROUND(SUM(revenue * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                        ->where('project_users.user_id', $userId)
                        ->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                        ->whereBetween('projects.updated_at', [$startDate, $endDate]);
                }]);
            } else {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($userId) {
                    $query->select(DB::raw('ROUND(SUM(revenue * users.commission_rate /' . COMMISSION_RATE_MAX . '),2) as revenue_sum'))
                        ->where('project_users.user_id', $userId)
                        ->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE);
                }]);
            }
        }
        return $query->first()->toArray();
    }

    public function findUserExist($companyId, $phone = null, $mail = null) {
        $user = $this->_model->where('company_id', $companyId);
        if ($phone || $mail) {
            $user = $user->where('phone', $phone)
                ->orWhere('email', $mail);
        }

        return $user->first();
    }
}

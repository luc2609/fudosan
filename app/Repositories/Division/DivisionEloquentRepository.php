<?php

namespace App\Repositories\Division;

use App\Models\Division;
use App\Models\User;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class DivisionEloquentRepository extends BaseEloquentRepository implements DivisionRepositoryInterface
{
    public function getModel()
    {
        return Division::class;
    }

    // List division in company
    public function listInCompany($companyId, $params)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE];
        $listDivision = $this->_model->where('company_id', $companyId)
            ->withCount([
                'projects as project_count',
                'projects as project_in_progress_count' => function ($query) use ($paramInProgress) {
                    $query->whereIn('close_status', $paramInProgress);
                },
                'projects as project_success_count' => function ($query) use ($paramClose) {
                    $query->whereIn('close_status', $paramClose);
                }
            ])
            ->orderBy('id', 'DESC');

        if (isset($params['name'])) {
            $name = $params['name'];
            $listDivision->where('name', 'like BINARY', '%' . $name . '%');
        }

        return $listDivision;
    }

    // List division in company with Role
    public function listDivisionVieRole($user, $params)
    {
        $userId = $user->id;
        $companyId = $user->company;
        $divisionId = $user->division;
        $listDivision = $this->_model->where('company_id', $companyId);

        if ($user->hasRole(MANAGER_ROLE)) {
            $listDivision = $this->getDivisionListOfManager($userId);
        }
        if ($user->hasRole(USER_ROLE)) {
            $listDivision->where('id', $divisionId);
        }
        if (isset($params['name'])) {
            $name = $params['name'];
            $listDivision->where('name', 'like BINARY', '%' . $name . '%');
        }
        $listDivision->orderBy('id', 'DESC');
        return $listDivision;
    }

    // list division role ranking
    public function listDivisionVieRoleRanking($companyId, $divisionId, $startDate, $endDate, $type)
    {
        $query = $this->_model
            ->select('divisions.id', 'name')
            ->leftJoin('projects', 'projects.division_id', 'divisions.id')
            ->where('divisions.company_id', $companyId)
            ->where('divisions.id', $divisionId);
        $division = $query->first();
        if ($type == CONTRACT_RANKING_TYPE) {
            if ($startDate != null && $endDate != null) {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate, $divisionId) {
                    $query->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('projects.division_id', $divisionId)
                        ->whereBetween('projects.updated_at', [$startDate, $endDate]);
                }]);
            } else {
                $query->where('projects.close_status', SUCCESS_CLOSE)
                    ->withCount(['projects as evaluate_ranking' => function ($query) use ($divisionId) {
                        $query->where('projects.close_status', SUCCESS_CLOSE)
                            ->where('projects.division_id', $divisionId);
                    }]);
            }
        }

        if ($type == REVENUE_RANKING_TYPE) {
            if ($startDate != null && $endDate != null) {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate, $divisionId) {
                    $query->select(DB::raw('SUM(price) as evaluate_ranking'))
                        ->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('projects.division_id', $divisionId)
                        ->whereBetween('projects.updated_at', [$startDate, $endDate]);
                }]);
            } else {
                $query->withCount(['projects as evaluate_ranking' => function ($query)  use ($divisionId) {
                    $query->select(DB::raw('SUM(price) as evaluate_ranking'))
                        ->where('projects.division_id', $divisionId)
                        ->where('projects.close_status', SUCCESS_CLOSE);
                }]);
            }
        }

        if ($type == BROKERAGE_RAKING_TYPE) {
            if ($startDate != null && $endDate != null) {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate, $divisionId) {
                    $query->select(DB::raw('SUM(revenue) as evaluate_ranking'))
                        ->where('projects.close_status', SUCCESS_CLOSE)
                        ->where('projects.division_id', $divisionId)
                        ->whereBetween('projects.updated_at', [$startDate, $endDate]);
                }]);
            } else {
                $query->withCount(['projects as evaluate_ranking' => function ($query) use ($divisionId) {
                    $query->select(DB::raw('SUM(revenue) as evaluate_ranking'))
                        ->where('projects.division_id', $divisionId)
                        ->where('projects.close_status', SUCCESS_CLOSE);
                }]);
            }
        }
        if ($query->first() == null) {
            $result = (array)$division;
        } else {
            $result = $query->first()->toArray();
        }
        return $result;
    }

    public function listDivisionCreatedCalendar($user, $params)
    {
        if (!$user->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
            return $this->listDivisionVieRole($user, $params)->get();
        }
        return null;
    }

    // find division by params
    public function findByParams($companyId, $params)
    {
        $query =  $this->_model->where('company_id', $companyId);

        if (isset($params['name'])) {
            $name = $params['name'];

            $query->where('name', $name);
        }

        return $query->first();
    }

    // Add manger to division
    public function addAvailableManagers($id, $managerID)
    {
        $division = $this->_model->find($id);
        if ($division) {
            $division->managers()->attach($managerID);
            return false;
        }
        return true;
    }

    // Delete manger of division
    public function destroyManger($id, $managerID)
    {
        $division = $this->_model->find($id);
        if ($division) {
            $division->managers()->updateExistingPivot($managerID, ['deleted_at' => now()]);
            return false;
        }
        return true;
    }

    // Add user to division
    public function addAvailableUsers($id, $userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->division = $id;
            $user->save();
            return false;
        }
        return true;
    }

    // Delete user of division
    public function destroyUser($userId)
    {
        $user = User::find($userId);
        $user->division = null;
        $user->save();
    }

    public function getAvailableDivisionListOfManager($id, $params, $auth)
    {
        $companyId = $auth->company;
        $divisionList = $this->listInCompany($companyId, $params)->select([
            'divisions.id', 'name'
        ])
            ->whereDoesntHave('managers', function ($query) use ($id) {
                $query->where('users.id', '=', $id);
            });
        if (isset($params['name'])) {
            $divisionList->where('name', 'like BINARY', '%' . $params['name'] . '%');
        }
        return $divisionList;
    }

    // list division of manager
    public function getDivisionListOfManager($managerId)
    {
        return  $this->_model->select(['divisions.id', 'name'])
            ->whereHas('managers', function ($query) use ($managerId) {
                $query->where('users.id', '=', $managerId);
            });
    }

    // Show available division of User
    public function getAvailableDivisionListOfUser($params, $auth)
    {
        $companyId = $auth->company;
        $userId = $auth->id;

        if ($auth->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
            $divisionList = $this->listInCompany($companyId, $params)->select([
                'divisions.id', 'name'
            ]);
        } else if ($auth->hasRole(MANAGER_ROLE)) {
            $divisionList =  $this->getDivisionListOfManager($userId);
        }
        if (isset($params['name'])) {
            $name = $params['name'];
            $divisionList->where('name', 'like BINARY', '%' . $name . '%');
        }
        return $divisionList;
    }

    // List division contract ranking
    public function indexContractRanking($params, $companyId,  $startDate = null, $endDate = null, $limit = null, $user = null)
    {
        $query = $this->_model
            ->leftJoin('projects', 'projects.division_id', 'divisions.id')
            ->select(
                'divisions.id',
                'name',
                DB::raw('(select count(*) from projects where projects.division_id = divisions.id and projects.close_status = ' . SUCCESS_CLOSE . ' ) as evaluate_ranking'),
                DB::raw('(select max(`updated_at`) from projects where projects.division_id = divisions.id and projects.close_status = ' . SUCCESS_CLOSE . ') as date'),
            )
            ->where('divisions.company_id', $companyId)
            ->groupBy('divisions.id');
        if ($startDate && $endDate) {
            $query->whereBetween('projects.updated_at', [$startDate, $endDate]);
        } elseif ($startDate && $endDate == null) {
            $query->whereDate('projects.updated_at', '>=', $startDate);
        } elseif ($startDate == null && $endDate) {
            $query->whereDate('projects.updated_at', '<=', $endDate);
        }

        if (isset($params['divisions'])) {
            $divisionIds = $params['divisions'];
            $query->whereIn('divisions.id', $divisionIds);
        }

        $query->orderBy('evaluate_ranking', 'DESC')->orderBy('date', 'ASC')->orderBy('divisions.id', 'DESC');

        $authDivisions = null;
        if ($user != null) {
            $authDivisions = $this->indexDivisionRanking($user, $params, $query, $companyId, $startDate, $endDate, CONTRACT_RANKING_TYPE);
        }

        if ($limit != null) {
            $divisions = $query->limit($limit)->get();
        } else {
            $divisions = $query->get();
        }
        return  [
            'divisions' => $divisions,
            'auth_division_ranking' => $authDivisions
        ];
    }

    // List division revuenue ranking
    public function indexRevenueRanking($params, $companyId,  $startDate = null, $endDate = null, $limit = null, $user = null)
    {
        $query = $this->_model
            ->leftJoin('projects', 'projects.division_id', 'divisions.id')
            ->select(
                'divisions.id',
                'name',
                DB::raw('(select max(`updated_at`) from projects where projects.division_id = divisions.id and projects.close_status = ' . SUCCESS_CLOSE . ') as date')
            )
            ->where('divisions.company_id', $companyId)
            ->groupBy('divisions.id');
        if ($startDate != null && $endDate != null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate) {
                $query->where('close_status', SUCCESS_CLOSE)
                    ->select(
                        DB::raw('SUM(price) as evaluate_ranking'),
                    )
                    ->whereBetween('projects.updated_at', [$startDate, $endDate]);
            }]);
        } elseif ($startDate == null && $endDate) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($endDate) {
                $query->where('close_status', SUCCESS_CLOSE)
                    ->select(
                        DB::raw('SUM(price) as evaluate_ranking'),
                    )
                    ->whereDate('projects.updated_at', '<=', $endDate);
            }]);
        } elseif ($startDate && $endDate == null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate) {
                $query->where('close_status', SUCCESS_CLOSE)
                    ->select(
                        DB::raw('SUM(price) as evaluate_ranking'),
                    )
                    ->whereDate('projects.updated_at', '>=', $startDate);
            }]);
        } else {
            $query->withCount(['projects as evaluate_ranking' => function ($query) {
                $query->select(
                    DB::raw('SUM(price) as evaluate_ranking')
                )
                    ->where('close_status', SUCCESS_CLOSE);
            }]);
        }

        if (isset($params['divisions'])) {
            $divisionIds = $params['divisions'];
            $query->whereIn('divisions.id', $divisionIds);
        }

        $query->orderBy('evaluate_ranking', 'DESC')->orderBy('date', 'ASC')->orderBy('divisions.id', 'DESC');

        $authDivisions = null;
        if ($user != null) {
            $authDivisions = $this->indexDivisionRanking($user, $params, $query, $companyId, $startDate, $endDate, REVENUE_RANKING_TYPE);
        }
        if ($limit != null) {
            $divisions = $query->limit($limit)->get();
        } else {
            $divisions = $query->get();
        }
        return  [
            'divisions' => $divisions,
            'auth_division_ranking' => $authDivisions
        ];
    }

    // List division brokerage fee ranking
    public function indexBrokerageRanking($params, $companyId, $startDate = null, $endDate = null, $limit = null, $user = null)
    {
        $query = $this->_model
            ->leftJoin('projects', 'projects.division_id', 'divisions.id')
            ->select(
                'divisions.id',
                'name',
                DB::raw('(select max(`updated_at`) from projects where projects.division_id = divisions.id and projects.close_status = ' . SUCCESS_CLOSE . ') as date')
            )
            ->where('divisions.company_id', $companyId)
            ->groupBy('divisions.id');


        if ($startDate != null && $endDate != null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate, $endDate) {
                $query->where('close_status', SUCCESS_CLOSE)
                    ->select(DB::raw('SUM(revenue) as evaluate_ranking'))
                    ->whereBetween('projects.updated_at', [$startDate, $endDate]);
            }]);
        } elseif ($startDate == null && $endDate) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($endDate) {
                $query->where('close_status', SUCCESS_CLOSE)
                    ->select(DB::raw('SUM(revenue) as evaluate_ranking'))
                    ->whereDate('projects.updated_at', '<=', $endDate);
            }]);
        } elseif ($startDate && $endDate == null) {
            $query->withCount(['projects as evaluate_ranking' => function ($query) use ($startDate) {
                $query->where('close_status', SUCCESS_CLOSE)
                    ->select(DB::raw('SUM(revenue) as evaluate_ranking'))
                    ->whereDate('projects.updated_at', '=>', $startDate);
            }]);
        } else {
            $query->withCount(['projects as evaluate_ranking' => function ($query) {
                $query
                    ->select(DB::raw('SUM(revenue) as evaluate_ranking'))
                    ->where('close_status', SUCCESS_CLOSE);
            }]);
        }

        if (isset($params['divisions'])) {
            $divisionIds = $params['divisions'];
            $query->whereIn('divisions.id', $divisionIds);
        }

        $query->orderBy('evaluate_ranking', 'DESC')->orderBy('date', 'ASC')->orderBy('divisions.id', 'DESC');

        $authDivisions = null;
        if ($user != null) {
            $authDivisions = $this->indexDivisionRanking($user, $params, $query, $companyId, $startDate, $endDate, BROKERAGE_RAKING_TYPE);
        }

        if ($limit != null) {
            $divisions = $query->limit($limit)->get();
        } else {
            $divisions = $query->get();
        }
        return  [
            'divisions' => $divisions,
            'auth_division_ranking' => $authDivisions
        ];
    }

    // public show index division ranking
    public function indexDivisionRanking($user, $params, $query, $companyId, $startDate, $endDate, $type)
    {
        $divisionVieRanking = $this->listDivisionCreatedCalendar($user, $params);
        $authDivisions = [];
        if ($divisionVieRanking != null) {
            foreach ($divisionVieRanking as $division) {
                $divisionIndex =  $query->get()->search(function ($divisionIds) use ($division) {
                    return $divisionIds->id === $division->id;
                });
                $divisionIndex = ['division_index' => $divisionIndex + POSITION];
                $divisionId = $division->id;
                if (!isset($params['divisions']) || (isset($params['divisions']) && in_array($divisionId, $params['divisions']))) {
                    $divisionVieRole = $this->listDivisionVieRoleRanking($companyId, $divisionId, $startDate, $endDate, $type);
                    $authDivision = array_merge($divisionVieRole, $divisionIndex);
                    array_push($authDivisions, $authDivision);
                }
            }
        }
        return $authDivisions;
    }
}

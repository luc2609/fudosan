<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Project;
use App\Models\UserRole;
use App\Models\User;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class CompanyEloquentRepository extends BaseEloquentRepository implements CompanyRepositoryInterface
{
    public function getModel()
    {
        return Company::class;
    }

    public function getList()
    {
        return $this->_model;
    }

    public function getListCompany($request)
    {
        $query =  $this->_model
            ->select('id', 'name', 'phone', 'address')
            ->withCount(['users as total_admin' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', ADMIN_COMPANY);
            }])
            ->withCount(['users as total_managers' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', MANAGER);
            }])
            ->withCount(['users as total_staff' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', USER);
            }]);
        if ($request->name) {
            $query->where('companies.name', 'like BINARY', '%' . $request->name . '%');
        }
        return $query;
    }

    public function getUserCompany($userId)
    {
        return $this->_model->join('users', 'users.company', '=', 'companies.id')
            ->select('users.*')
            ->where('users.id', $userId)
            ->first();
    }

    //Create Account Admin CMS Company
    public function addAccountAdminCmsCompany($request)
    {
        $params = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'kana_first_name' => $request->kana_first_name,
            'kana_last_name' => $request->kana_last_name,
            'email' => $request->email,
            'password' => $request->password,
            'position' => $request->position,
            'company' => $request->company,
            'commission_rate' => $request->commission_rate ?? COMMISSION_RATE_MAX,
            'phone' => $request->phone,
        ];
        $user = User::create($params);
        UserRole::create(['user_id' => $user->id, 'role_id' => ADMIN_CMS_COMPANY]);
        return $user;
    }

    public function updateAccountCmsCompany($request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'first_name' => $request->first_name ?? $user->first_name,
                'last_name' => $request->last_name ?? $user->last_name,
                'kana_first_name' => $request->kana_first_name ?? $user->kana_first_name,
                'kana_last_name' => $request->kana_last_name ?? $user->kana_last_name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ?? $user->password,
                'position' => $request->position ?? $user->position,
                'company' => $request->company ?? $user->company,
                'phone' => $request->phone ?? $user->phone
            ]);
            return $user;
        }
        return false;
    }

    public function deleteAccountCmsCompany($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->userRoles()->delete();
            $user->delete();
            return true;
        }
        return false;
    }

    public function getListAccountCmsCompany($companyId)
    {
        $query = User::select('*');
        $query->where('company', $companyId);
        return $query;
    }

    public function getCompanyDetail()
    {
        return $this->_model
            ->withCount('projects')
            ->withCount(['users as total_users' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', '!=', ADMIN_CMS_SYSTEM);
            }])

            ->withCount(['users as admins' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', ADMIN_CMS_COMPANY);
            }])
            ->withCount(['users as managers' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', MANAGER);
            }])
            ->withCount(['users as staffs' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', USER);
            }]);
    }

    public function getCompanyInfo($companyId)
    {
        $url = env('AWS_URL') . '/';
        return $this->_model
            ->select(
                'companies.*',
                DB::raw('CONCAT("' . $url . '", companies.logo_image) as logo_image')
            )
            ->withCount(['users as total_users' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', '!=', ADMIN_CMS_SYSTEM);
            }])
            ->withCount(['users as managers' => function ($q) {
                $q->join('user_roles', 'users.id', 'user_roles.user_id')
                    ->where('user_roles.role_id', MANAGER);
            }])
            ->withCount(['projects as finish_projects' => function ($q) {
                $q->where('close_status', SUCCESS_CLOSE);
            }])
            ->withCount(['projects as in_progess_projects' => function ($q) {
                $q->whereIN('close_status', [IN_PROGRESS, REQUEST_CLOSE]);
            }])
            ->with('admins')
            ->find($companyId);
    }

    public function detailCompanySystem($id)
    {
        return $this->_model->where('id', $id)->select('name', 'phone', 'address')->first();
    }

    public function getAccountAdminCompany($id)
    {
        return $this->_model->leftJoin('users', 'users.company', 'companies.id')
            ->leftJoin('user_roles', 'users.id', 'user_roles.user_id')
            ->where([
                'users.company' => $id,
                'users.deleted_at' => null
            ])
            ->where('user_roles.role_id', '!=', ROLE_ADMIN)
            ->select(
                'users.id',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as username'),
                'users.phone',
                'users.email',
                'users.last_login',
                'user_roles.role_id',
                DB::raw('(select count(*) from oauth_access_tokens where oauth_access_tokens.user_id = users.id) as count_token'),
            );
    }

    public function getDivisionCompany($id)
    {
        $query = $this->_model->leftJoin('divisions', 'divisions.company_id', 'companies.id')
            ->where([
                'divisions.company_id' => $id,
                'divisions.deleted_at' => null
            ])
            ->select(
                'divisions.id',
                'divisions.name',
            )->get();
        $divisions = [];
        foreach ($query as $q) {
            $divisionId = $q->id;
            $division = $q->toArray();
            $param = [
                'user_count' => User::where([
                    'company' => $id,
                    'division' => $divisionId
                ])->orWhereHas('divisions', function ($query) use ($id, $divisionId) {
                    $query->where([
                        'company' => $id,
                        'division_id' => $divisionId
                    ]);
                })->pluck('users.id')->count(),

                'project_success_count' => Project::where([
                    'company_id' => $id,
                    'division_id' => $divisionId,
                    'close_status' => SUCCESS_CLOSE
                ])->get()->count()
            ];
            $data =  array_merge($division, $param);
            $divisions[] = $data;
        }
        return $divisions;
    }
}

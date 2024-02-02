<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\User\UserEloquentRepository;
use App\Services\FileService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Repositories\User\UserRepositoryInterface;

class EmployeeImport extends FileService implements ToCollection
{
    protected $userModel;

    protected $userRoleModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->userRoleModel = new UserRole();
    }

    public function collection(Collection $collection)
    {
        $auth = Auth::user();
        $company = $auth->company;
        $currentTime = date('Y-m-d H:i:s');
        $password = Hash::make(request()->password);
        foreach ($collection as $row) {
            if ((int) $row[0] == 0) {
                continue;
            } else {
                $items = [
                    'first_name' => trim($row[1]),
                    'last_name' => trim($row[2]),
                    'kana_first_name' => trim($row[3]),
                    'kana_last_name' => trim($row[4]),
                    'email' => trim($row[6]),
                    'phone' => trim($row[5]),
                    'company' => $company,
                    'password' => $password,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];
                $userRepo = new UserEloquentRepository();
                $user = $userRepo->findUserExist($company, trim($row[5]), trim($row[6]));
                $roleId = null;
                if (trim($row[7]) != null) {
                    $roleId = Role::where('name', 'like BINARY', trim($row[7]))->first()->id;
                }
                if ($user) {
                    $userId = $user->id;
                    $user->update($items);
                    $userRole = UserRole::where('user_id', $userId)->first();
                    if ($roleId != null) {
                        if ($userRole->role_id != $roleId && !$auth->hasRole(ADMIN_CMS_COMPANY_ROLE)) {
                            continue;
                        } else {
                            $userRole->update([
                                'role_id' => $roleId,
                                'created_at' => $currentTime,
                                'updated_at' => $currentTime,
                            ]);
                        }
                    }
                } else {
                    $account = $this->userModel->create($items);
                    if ($roleId != null) {
                        $userRole = [
                            'user_id' => $account->id,
                            'role_id' => $roleId,
                            'created_at' => $currentTime,
                            'updated_at' => $currentTime,
                        ];
                        $this->userRoleModel->insert($userRole);
                    }
                }
            }
        }
    }
}

<?php

namespace App\Permissions;

use App\Models\Role;

trait HasPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasPermission($slug_permission)
    {
        $roles = $this->roles;

        if (!count($roles)) {
            return false;
        }

        foreach ($roles as $role) {
            $permissions = $role->permissions;

            if (count($permissions)) {
                $permission = $permissions->where('slug', $slug_permission)->count();

                if ($permission) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasRole($slugRole)
    {
        if ($this->roles->contains('slug', $slugRole)) {
            return true;
        }
        return false;
    }

    //
    public function hasPermissionInSystem($system, $permissionSystem)
    {
        $permissionCmsSystem = $permissionSystem[CMS_SYSTEM] ?? null;
        $permissionCmsCompany = $permissionSystem[CMS_COMPANY] ?? null;
        $permissionApp = $permissionSystem[APP] ?? null;

        switch ($system) {
            case CMS_SYSTEM:
                $response = $this->hasPermission($permissionCmsSystem);
                break;
            case CMS_COMPANY:
                $response = $this->hasPermission($permissionCmsCompany);
                break;
            case APP:
                $response = $this->hasPermission($permissionApp);
                break;
            default:
                $response = false;
                break;
        }
        return $response;
    }
}

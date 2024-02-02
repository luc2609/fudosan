<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class Role extends Model
{
    use HasFactory, UnixTimestampSerializable;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}

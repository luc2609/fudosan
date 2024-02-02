<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $fillable = [
        'name',
        'province',
        'district',
        'street',
        'phone',
        'website',
        'status',
        'address',
        'commission_rate',
        'note',
        'logo_image'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'company', 'id');
    }

    public function divisions()
    {
        return $this->hasMany(Division::class, 'company_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'company_id', 'id')->select('id', 'company_id');
    }

    public function admins()
    {
        return $this->hasMany(User::class, 'company', 'id')
            ->select(
                'users.id',
                'first_name',
                'last_name',
                'kana_first_name',
                'kana_last_name',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as username'),
                DB::raw('CONCAT(users.kana_last_name, " ", users.kana_first_name) as furigana'),
                'company'
            )
            ->leftJoin('user_roles', 'users.id', 'user_roles.user_id')
            ->leftJoin('roles', 'user_roles.role_id', 'roles.id')
            ->where('user_roles.role_id', ADMIN_CMS_COMPANY);
    }

    public function property()
    {
        return $this->hasOne(Property::class, 'company_id', 'id')->select('id', 'company_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'company_id', 'id')->select('id', 'company_id');
    }
}

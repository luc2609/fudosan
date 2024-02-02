<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;

class Project extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $guarded = ['id'];
    protected $appends = ['user_in_charge_name'];

    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'project_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    public function propertyAndStations()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id')
            ->with('propertyStations');
    }

    public function documents()
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'project_customers')
            ->whereNull('project_customers.deleted_at')->withTimestamps();
    }

    public function advertisingWebs()
    {
        return $this->belongsToMany(
            MasterAdvertisingWeb::class,
            'project_advertising_webs',
            'project_id',
            'advertising_web_id'
        )->whereNull('project_advertising_webs.deleted_at')->withTimestamps();
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class);
    }

    public function projectCustomers()
    {
        return $this->hasMany(ProjectCustomer::class, 'project_id', 'id');
    }

    public function projectUserInCharge()
    {
        return $this->projectUsers()->where('user_type', USER_IN_CHARGE_TYPE)->first();
    }

    public function projectSubUserInCharge()
    {
        return $this->projectUsers()->where('user_type', SUB_USER_IN_CHARGE_TYPE)->first();
    }

    public function projectRelatedUsers()
    {
        return $this->projectUsers()->where('user_type', RELATED_USER_TYPE);
    }

    public function projectPhases()
    {
        return $this->hasMany(ProjectPhase::class, 'project_id', 'id');
    }

    public function userInCharge()
    {
        $url = env('AWS_URL') . '/';
        return $this->projectUsers()->where('user_type', USER_IN_CHARGE_TYPE)
            ->leftJoin('users', 'users.id', 'project_users.user_id')
            ->selectRaw('users.id,CONCAT(users.last_name," ",users.first_name) as username,CONCAT(users.kana_last_name," ",users.kana_first_name) as furigana,CONCAT("' . $url . '", users.avatar) as avatar')
            ->first();
    }

    public function subUserInCharge()
    {
        $url = env('AWS_URL') . '/';
        return $this->projectUsers()->where('user_type', SUB_USER_IN_CHARGE_TYPE)
            ->leftJoin('users', 'users.id', 'project_users.user_id')
            ->selectRaw('users.id,CONCAT(users.last_name," ",users.first_name) as username,CONCAT(users.kana_last_name," ",users.kana_first_name) as furigana,CONCAT("' . $url . '", users.avatar) as avatar')
            ->first();
    }

    public function relatedUsers()
    {
        $url = env('AWS_URL') . '/';
        return $this->projectUsers()->where('user_type', RELATED_USER_TYPE)
            ->leftJoin('users', 'users.id', 'project_users.user_id')
            ->selectRaw('users.id,CONCAT(users.last_name," ",users.first_name) as username,CONCAT(users.kana_last_name," ",users.kana_first_name) as furigana,CONCAT("' . $url . '", users.avatar) as avatar')->get();
    }

    public function masterPhaseProjectId()
    {
        $projectPhase = $this->projectPhases()->where('id', $this->current_phase_id)->first();

        $masterPhaseProjectId = null;
        if ($projectPhase) {
            $masterPhaseProjectId =  $projectPhase->m_phase_project_id;
        }

        return $masterPhaseProjectId;
    }

    public function salePurposes()
    {
        return $this->belongsToMany(MasterSalePurpose::class, 'project_sale_purposes', 'project_id', 'sale_purpose_id');
    }

    public function purchasePurposes()
    {
        return $this->belongsToMany(MasterPurchasePurpose::class, 'project_purchase_purposes', 'project_id', 'purchase_purpose_id');
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class, 'project_id', 'id');
    }

    public function getUserInChargeNameAttribute()
    {
        return $this->projectUsers()->where('project_id', $this->id)
            ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
            ->join('users', 'users.id', 'project_users.user_id')
            ->selectRaw('users.id,CONCAT(users.last_name," ",users.first_name) as username')->first();
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'project_properties')
            ->whereNull('project_properties.deleted_at')->withTimestamps();
    }
    public function projectProperties()
    {
        return $this->hasMany(ProjectProperty::class, 'project_id', 'id');
    }

    public function getReasonAttribute()
    {
        if (!is_null($this->attributes['reason'])) {
            return json_decode($this->attributes['reason']);
        }
        return null;
    }

    public function getHistoryAttribute()
    {
        if (!is_null($this->attributes['history'])) {
            return json_decode($this->attributes['history']);
        }
        return null;
    }
}

<?php

namespace App\Models;

use App\Permissions\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Time\UnixTimestampSerializable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPermissions, SoftDeletes, UnixTimestampSerializable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['username', 'furigana'];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function passwordSecurityTypeLogin()
    {
        return $this->hasMany(PasswordSecurity::class)->where('type', TOKEN_LOGIN_TYPE);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company');
    }

    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'user_divisions')
            ->whereNull('user_divisions.deleted_at')->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users')
            ->whereNull('project_users.deleted_at')->withTimestamps();
    }

    public function projectIds()
    {
        return $this->belongsToMany(Project::class, 'project_users')
            ->select('projects.id as project_id');
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'user_id', 'id');
    }

    public function getAvatarAttribute()
    {
        if ($this->attributes['avatar']) {
            return Storage::disk('s3')->url($this->attributes['avatar']);
        }

        return null;
    }

    public function getUserNameAttribute()
    {
        return  $this->attributes['last_name'] . ' ' . $this->attributes['first_name'];
    }

    public function getFuriganaAttribute()
    {
        return $this->attributes['kana_last_name'] . ' ' . $this->attributes['kana_first_name'];
    }

    public function deviceTokens()
    {
        return $this->hasMany(UserDeviceToken::class, 'user_id', 'id');
    }

    public function accessTokens()
    {
        return $this->hasMany(AuthAccessToken::class, 'user_id', 'id');
    }
}

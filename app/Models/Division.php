<?php

namespace App\Models;

use App\Time\UnixTimestampSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'company_id'
    ];

    protected $appends = ['manager_count', 'user_count'];

    public function managers()
    {
        return $this->belongsToMany(User::class, 'user_divisions')
            ->whereNull('user_divisions.deleted_at')->withTimestamps();
    }


    public function getManagerCountAttribute()
    {
        $divisionId =  $this->id;

        return User::whereHas('divisions', function ($query) use ($divisionId) {
            $query->where('divisions.id', '=', $divisionId);
        })
            ->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', MANAGER_ROLE);
            })
            ->count();
    }

    public function getUserCountAttribute()
    {
        $divisionId =  $this->id;
        return User::where('division', $divisionId)
            ->whereHas('roles', function ($query) {
                $query->where('roles.slug', '=', USER_ROLE);
            })->count();
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}

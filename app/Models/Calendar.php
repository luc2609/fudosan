<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;
use Illuminate\Support\Facades\DB;

class Calendar extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'calendar_users', 'calendar_id', 'user_id')
            ->select(
                'users.id',
                'is_host',
                'first_name',
                'last_name',
                'kana_first_name',
                'kana_last_name',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as username'),
                DB::raw('CONCAT(users.kana_last_name, " ", users.kana_first_name) as furigana'),
                'email',
                'is_accept',
                'notify_id',
                'master_notify_calendars.notify as notify_name',
                'avatar',
                'division',
                'user_roles.role_id',
            )
            ->join('user_roles', 'user_roles.user_id', 'users.id')
            ->leftJoin('master_notify_calendars', 'master_notify_calendars.id', 'calendar_users.notify_id')
            ->where('calendar_users.sub_calendar_id', null);
    }
    public function calendarUsers()
    {
        return $this->belongsToMany(User::class, 'calendar_users', 'calendar_id', 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(CalendarFile::class, 'calendar_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getAuthUserAcceptAttribute()
    {
        $authUserAccept = null;
        $authUserId =  auth()->user()->id;
        if ($this->users->contains('id', $authUserId)) {
            $authUserAccept = $this->users->where('id', $authUserId)->first()->is_accept;
        }

        return $authUserAccept;
    }

    public function getCustomersAttribute()
    {
        $projectId = $this->project_id;
        $project = Project::find($projectId);
        $customers = null;
        if ($project) {
            $customers = $project->customers->map->only(['id', 'first_name', 'last_name', 'kana_first_name', 'kana_last_name']);
        }

        return $customers;
    }

    public function subCalendars()
    {
        return $this->hasMany(SubCalendar::class, 'calendar_id', 'id');
    }

    public function subCalendarNows()
    {
        $now = now()->format('Y-m-d');
        return $this->hasMany(SubCalendar::class, 'calendar_id', 'id')
            ->where(DB::raw("DATE_FORMAT(modify_date, '%Y-%m-%d')"), '=', $now);
    }

    public function calendarUserNows()
    {
        return $this->hasMany(CalendarUser::class, 'calendar_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;
use Illuminate\Support\Facades\DB;

class SubCalendar extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $guarded = ['id'];

    public function documents()
    {
        return $this->hasMany(SubCalendarFile::class, 'sub_calendar_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'calendar_users', 'sub_calendar_id', 'user_id')
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
            ->leftJoin('master_notify_calendars', 'master_notify_calendars.id', 'calendar_users.notify_id');
    }

    public function subUsers()
    {
        return $this->hasMany(SubCalendarUser::class, 'sub_calendar_id', 'id');
    }
}

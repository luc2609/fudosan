<?php

namespace App\Repositories\CalendarUser;

use App\Models\CalendarUser;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class CalendarUserEloquentRepository extends BaseEloquentRepository implements CalendarUserRepositoryInterface
{
    public function getModel()
    {
        return CalendarUser::class;
    }

    public function calendar($calendarId)
    {
        return $this->_model
            ->where('calendar_id', $calendarId)
            ->first();
    }

    public function findUser($userId, $calendarId)
    {
        return $this->_model
            ->where('user_id', $userId)
            ->where('calendar_id', $calendarId)
            ->get();
    }

    public function findCalendar($modifyDateCalendar, $userId, $calendarId)
    {
        return $this->_model
            ->leftJoin('sub_calendars', 'sub_calendars.id', 'calendar_users.sub_calendar_id')
            ->whereDate('sub_calendars.meeting_start_time', $modifyDateCalendar)
            ->where('sub_calendars.is_deleted', false)
            ->where('calendar_users.user_id', $userId)
            ->where('calendar_users.calendar_id', $calendarId)
            ->select('calendar_users.id', 'calendar_users.is_accept')
            ->first();
    }
    public function findUserHost($userId, $calendarId)
    {
        return $this->_model
            ->where(
                [
                    'user_id' => $userId,
                    'calendar_id' => $calendarId,
                    'sub_calendar_id' => null
                ]
            )
            ->select('is_host')
            ->first();
    }

    public function countCalendarNotifies($userId)
    {
        return $this->_model
            ->distinct('calendar_id')
            ->where([
                ['user_id', $userId],
                ['is_accept', '<>', REJECT]
            ])
            ->count();
    }

    public function getUserOfCalendar($calendarIds)
    {
        return $this->_model->whereIn('calendar_id', $calendarIds)->get();
    }

    public function getUserOfSubCalendar($subCalendarIds)
    {
        return $this->_model->where('sub_calendar_id', $subCalendarIds)->get();
    }

    public function changeNotiCalendar($request, $calendarId, $userId, $startTimeMeeting)
    {
        if ($request->type == MASTER_CALENDAR) {
            return $this->_model->where('calendar_id', $calendarId)->where('user_id', $userId)->update(['notify_id' => $request->notify_id, 'start_time_meeting' => $startTimeMeeting]);
        } else {
            return $this->_model->where('sub_calendar_id', $calendarId)->where('user_id', $userId)->update(['notify_id' => $request->notify_id, 'start_time_meeting' => $startTimeMeeting]);
        }
    }

    public function reminderCalendarNotRepeats($dateNow)
    {
        return $this->_model
            ->join('calendars', 'calendars.id', 'calendar_users.calendar_id')
            ->select(
                'calendar_users.id',
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i') as time"),
                DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%Y-%m-%d') as date"),
                DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%Y-%m-%d') as date_end"),
                'calendar_users.notify_id',
                'calendar_users.user_id',
                'calendar_users.calendar_id',
                'calendar_users.sub_calendar_id',
                'calendars.repeat_id',
            )
            ->where(DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i')"), '=', $dateNow)
            ->where('calendar_users.is_accept','!=', REJECT)
            ->where('calendars.repeat_id', NOT_REPEAT)
            ->whereIn('calendar_users.notify_id', [3, 4, 5, 6, 7])
            ->get();
    }

    public function reminderCalendarRepeatDay($timeNow, $dateNow)
    {
        return $this->_model
            ->join('calendars', 'calendars.id', 'calendar_users.calendar_id')
            ->leftJoin('sub_calendars', 'sub_calendars.id', 'calendar_users.sub_calendar_id')
            ->select(
                'calendar_users.id',
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i') as time_user"),
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%Y-%m-%d') as date_user"),
                DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%Y-%m-%d') as start_date"),
                DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%Y-%m-%d') as end_date"),
                'calendar_users.notify_id',
                'calendar_users.user_id',
                'calendar_users.calendar_id',
                'calendar_users.sub_calendar_id',
                'calendars.repeat_id',
                DB::raw("CASE WHEN sub_calendars.modify_date IS NOT NULL 
                THEN CASE WHEN DATE_FORMAT(sub_calendars.modify_date, '%Y-%m-%d') = '" . $dateNow . "'
                THEN sub_calendars.modify_date ELSE NULL END END as modify_date"),
                DB::raw("CASE WHEN sub_calendars.modify_date IS NOT NULL 
                THEN CASE WHEN DATE_FORMAT(sub_calendars.modify_date, '%Y-%m-%d') = '" . $dateNow . "'
                THEN DATE_FORMAT(sub_calendars.meeting_start_time, '%Y-%m-%d') ELSE NULL END END as meeting_sub_calendar")
            )
            ->where(DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i')"), '=', $timeNow)
            ->where('calendar_users.is_accept', IS_ACCEPT)
            ->where('calendars.repeat_id', REPEAT_DAY)
            ->whereIn('calendar_users.notify_id', [3, 4, 5, 6, 7])
            ->get();
    }

    public function reminderCalendarRepeatWEEK($timeNow, $dateNow)
    {
        return $this->_model
            ->join('calendars', 'calendars.id', 'calendar_users.calendar_id')
            ->leftJoin('sub_calendars', 'sub_calendars.id', 'calendar_users.sub_calendar_id')
            ->select(
                'calendar_users.id',
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i') as time_user"),
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%Y-%m-%d') as date_user"),
                DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%H:%i') as start_time"),
                DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%H:%i') as end_time"),
                'calendar_users.notify_id',
                'calendar_users.user_id',
                'calendar_users.calendar_id',
                'calendar_users.sub_calendar_id',
                'calendars.repeat_id',
            )
            ->where(DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i')"), '=', $timeNow)
            ->where(DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%Y-%m-%d')"), '<=', $dateNow)
            ->where(DB::raw("DAYOFWEEK(DATE_FORMAT(calendar_users.start_time_meeting, '%Y-%m-%d'))"), '=', DB::raw("DAYOFWEEK('" . $dateNow . "')"))
            ->where('calendar_users.is_accept', IS_ACCEPT)
            ->where('calendars.repeat_id', REPEAT_WEEK)
            ->whereIn('calendar_users.notify_id', [3, 4, 5, 6, 7])
            ->get();
    }
    public function reminderCalendarRepeatMonth($dateNow)
    {
        return $this->_model
            ->join('calendars', 'calendars.id', 'calendar_users.calendar_id')
            ->leftJoin('sub_calendars', 'sub_calendars.id', 'calendar_users.sub_calendar_id')
            ->select(
                'calendar_users.id',
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%H:%i') as time_user"),
                DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%Y-%m-%d') as date_user"),
                DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%H:%i') as start_time"),
                DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%H:%i') as end_time"),
                'calendar_users.notify_id',
                'calendar_users.user_id',
                'calendar_users.calendar_id',
                'calendar_users.sub_calendar_id',
                'calendars.repeat_id',
            )
            ->where(DB::raw("DATE_FORMAT(calendar_users.start_time_meeting, '%Y-%m-%d %H:%i')"), '=', $dateNow)
            ->where('calendar_users.is_accept', IS_ACCEPT)
            ->where('calendars.repeat_id', REPEAT_MONTH)
            ->whereNotNull('calendar_users.sub_calendar_id')
            ->whereIn('calendar_users.notify_id', [3, 4, 5, 6, 7])
            ->get();
    }

    public function checkUserOfCalendar($calendarIds, $userIds)
    {
        return $this->_model->whereIn('calendar_id', $calendarIds)->whereIn('user_id', $userIds)->get();
    }
}

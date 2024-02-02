<?php

namespace App\Repositories\Calendar;

use App\Models\Calendar;
use App\Models\User;
use App\Repositories\Base\BaseEloquentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarEloquentRepository extends BaseEloquentRepository implements CalendarRepositoryInterface
{
    public function getModel()
    {
        return Calendar::class;
    }

    // Show detail calendar
    public function show($id)
    {
        return $this->_model
            ->leftJoin('divisions', 'divisions.id', 'calendars.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'calendars.project_phase_id')
            ->leftJoin('master_schedule_repeats', 'calendars.repeat_id', 'master_schedule_repeats.id')
            ->leftJoin('master_notify_calendars', 'calendars.notify_id', 'master_notify_calendars.id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->leftJoin('projects', 'projects.id', 'calendars.project_id')
            ->leftJoin('properties', 'properties.id', 'projects.property_id')
            ->select(
                'calendars.*',
                'master_schedule_repeats.repeat as repeat',
                'divisions.name as division_name',
                'master_phase_projects.name as project_phase_name',
                'projects.title as project_name',
                'properties.name as property_name',
                'master_notify_calendars.notify'
            )
            ->where('calendars.id', $id)
            ->with(['documents', 'users', 'subCalendars.users', 'subCalendars.documents'])
            ->with('subCalendars', function ($q) {
                $q->leftJoin('master_notify_calendars', 'sub_calendars.notify_id', 'master_notify_calendars.id')
                    ->select(
                        'sub_calendars.*',
                        'master_notify_calendars.notify as notify'
                    );
            })
            ->first()->append(['auth_user_accept', 'customers']);
    }

    // list calendar in company
    public function index($request)
    {
        $user = User::find(auth()->user()->id);
        $companyId = $user->company;
        if ($user->hasRole(MANAGER_ROLE)) {
            $divisionIds = $user->divisions->pluck('id');
        }

        $query = $this->_model
            ->leftJoin('divisions', 'divisions.id', 'calendars.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'calendars.project_phase_id')
            ->leftJoin('master_schedule_repeats', 'calendars.repeat_id', 'master_schedule_repeats.id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->leftJoin('projects', 'projects.id', 'calendars.project_id')
            ->leftJoin('properties', 'properties.id', 'projects.property_id')
            ->leftJoin('calendar_users', 'calendar_users.calendar_id', 'calendars.id')
            ->leftJoin('sub_calendars', 'sub_calendars.calendar_id', 'calendars.id')
            ->leftJoin('sub_calendar_users', 'sub_calendar_users.sub_calendar_id', 'sub_calendars.id')
            ->leftJoin('users', 'users.id', 'calendar_users.user_id')
            ->leftJoin('master_notify_calendars', 'calendars.notify_id', 'master_notify_calendars.id')
            ->select(
                'calendars.*',
                'master_schedule_repeats.repeat as repeat',
                'divisions.name as division_name',
                'master_phase_projects.name as project_phase_name',
                'projects.title as project_name',
                'properties.name as property_name',
                'master_notify_calendars.notify as notify'
            )
            ->where('calendars.company_id', $companyId)
            ->distinct()
            ->with(['documents', 'users', 'subCalendars.users', 'subCalendars.documents'])
            ->with('subCalendars', function ($q) {
                $q->leftJoin('master_notify_calendars', 'sub_calendars.notify_id', 'master_notify_calendars.id')
                    ->select(
                        'sub_calendars.*',
                        'master_notify_calendars.notify as notify'
                    );
            });

        if ($request->action == CALENDAR_ME) {
            $query->where('calendar_users.user_id', $user['id']);
        }

        if ($request->action == CALENDAR_ALL) {
            if ($user->hasRole(MANAGER_ROLE)) {
                $query->where(function ($q) use ($user, $divisionIds) {
                    $q->whereIn('users.division', $divisionIds)
                        ->orWhere('is_public', PUBLIC_STATUS)
                        ->orWhere('calendar_users.user_id', $user['id']);
                });
            }
            if ($user->hasRole(USER_ROLE)) {
                $query->where(function ($q) use ($user) {
                    $q->where('calendar_users.user_id', $user['id'])
                        ->orWhere('calendars.is_public', PUBLIC_STATUS);
                });
            }
        }

        if ($request->action == CALENDAR_DIVISION) {
            if ($user->hasRole(MANAGER_ROLE)) {
                $query->where(function ($q) use ($user, $divisionIds) {
                    $q->whereIn('users.division', $divisionIds)
                        ->orWhere('calendar_users.user_id', $user['id']);
                });
            }
            if ($user->hasRole(USER_ROLE)) {
                $query->where(function ($q) use ($user) {
                    $q->where('users.division', $user['division'])
                        ->where('is_public', PUBLIC_STATUS)
                        ->orWhere('calendar_users.user_id', $user['id']);
                });
            }
        }
        if ($request->title) {
            $title = $request->title;
            $query->where(function ($q) use ($title) {
                $q->where('calendars.title',  'like BINARY', '%' . $title . '%')
                    ->orWhere('sub_calendars.title',  'like BINARY', '%' . $title . '%');
            });
        }

        if ($request->project_phase_id) {
            $projectPhase = $request->project_phase_id;
            $query->where('calendars.project_phase_id',  $projectPhase);
        }

        if ($request->user_id) {
            $userId = $request->user_id;
            $query->where('calendar_users.user_id', $userId);
        }

        if ($request->meeting_type) {
            $meetingType = $request->meeting_type;
            $query->where('calendars.meeting_type', $meetingType);
        }

        if ($request->division_id) {
            $divisionId = $request->division_id;
            $query->where('calendars.division_id',   $divisionId);
        }

        $startDateLimit = $request->start_date_limit;
        $endDateLimit = $request->end_date_limit . ' ' . '23:59:59';
        $query->where(function ($q) use ($startDateLimit, $endDateLimit) {
            $q->where('calendars.repeat_id', NOT_REPEAT)
                ->whereBetween('calendars.meeting_start_time', [$startDateLimit, $endDateLimit])
                ->orWhere([
                    ['calendars.repeat_id', '<>',  NOT_REPEAT],
                    ['calendars.start_date', '>', $startDateLimit],
                    ['calendars.start_date', '<', $endDateLimit],
                ])
                ->orWhere([
                    ['calendars.repeat_id', '<>',  NOT_REPEAT],
                    ['calendars.start_date', '<=', $startDateLimit],
                    ['calendars.end_date', '>=', $startDateLimit],
                ]);
        });

        return $query->orderBy('calendars.id', 'DESC');
    }

    // Get data by attributes
    public function getByAttributes($attributes)
    {
        $query = $this->_model->select('*');

        if (empty($attributes)) {
            return $query->all();
        }

        if (isset($attributes['project_id'])) {
            $query->where('project_id', $attributes['project_id']);
        }

        if (isset($attributes['project_phase_id'])) {
            $query->where('project_phase_id', $attributes['project_phase_id']);
        }

        if (isset($attributes['company_id'])) {
            $query->where('company_id', $attributes['company_id']);
        }

        return $query->get();
    }

    // Show calendar conflict
    public function findCalendarConflict($request, $id, $userId)
    {
        $startDateCalendar =  Carbon::parse($request->meeting_start_time);
        $endDateCalendar =  Carbon::parse($request->meeting_end_time);
        $startTime = date('H:i:s', strtotime($request->meeting_start_time));
        $endTime = date('H:i:s', strtotime($request->meeting_end_time));
        // calendar not repeat conflict
        $calendarNotRepeat = $this->_model
            ->where('calendars.id', '!=', $id)
            ->leftJoin('calendar_users', 'calendars.id', 'calendar_users.calendar_id')
            ->where('calendar_users.user_id', $userId)
            ->where('calendars.repeat_id', NOT_REPEAT)
            ->where(function ($q) use ($startDateCalendar, $endDateCalendar) {
                $q->whereBetween('calendars.meeting_start_time', [$startDateCalendar, $endDateCalendar])
                    ->orWhere([
                        ['calendars.meeting_end_time', '>', $startDateCalendar],
                        ['calendars.meeting_end_time', '<', $endDateCalendar]
                    ])->orWhere([
                        ['calendars.meeting_start_time', '<', $startDateCalendar],
                        ['calendars.meeting_end_time', '>', $endDateCalendar]
                    ]);
            })->select('calendars.id', 'calendars.title')->distinct()->get();

        // sub calendar conflict
        $subCalendar = $this->subCalendarConflict($id, $startDateCalendar, $endDateCalendar, $userId);

        //calendar loop day
        $calendarDays = $this->calendarLoopDay($startDateCalendar, $startTime, $endTime, $id, $userId);

        //calendar loop week
        $calendarWeeks = $this->calendarLoopWeek($startDateCalendar, $endDateCalendar, $startTime, $endTime, $id, $userId);
        return $calendarDays->merge($calendarNotRepeat)->merge($subCalendar)->merge($calendarWeeks);
    }

    // find host calendar
    public function findHostCalendar($id)
    {
        return $this->_model->where('calendars.id', $id)
            ->join('calendar_users', 'calendar_users.calendar_id', 'calendars.id')
            ->where('calendar_users.is_host', true)->first();
    }

    public function calendarLoopDay($startDateCalendar, $startTime, $endTime, $id, $userId)
    {
        return $this->_model
            ->where('calendars.id', '!=', $id)
            ->leftJoin('calendar_users', 'calendars.id', 'calendar_users.calendar_id')
            ->leftJoin('sub_calendars', 'calendars.id', 'sub_calendars.calendar_id')
            ->where([
                'calendar_users.user_id' => $userId,
                'calendars.repeat_id' => REPEAT_DAY
            ])
            ->where(DB::raw("DATE(sub_calendars.modify_date) != '" . $startDateCalendar . "'"))
            ->whereDate('calendars.start_date', '<=', $startDateCalendar)
            ->whereDate('calendars.end_date', '>=', $startDateCalendar)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereTime('calendars.meeting_start_time', '>=', $startTime)
                    ->whereTime('calendars.meeting_start_time', '<', $endTime)
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereTime('calendars.meeting_end_time', '>', $startTime)
                            ->whereTime('calendars.meeting_end_time', '<', $endTime);
                    })->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereTime('calendars.meeting_start_time', '<', $startTime)
                            ->whereTime('calendars.meeting_end_time', '>', $endTime);
                    });
            })->select('calendars.id', 'calendars.title')->distinct()->get();
    }

    public function calendarLoopWeek($startDateCalendar, $endDateCalendar, $startTime, $endTime, $id, $userId)
    {
        $calendarWeeks = $this->_model
            ->where('calendars.id', '!=', $id)
            ->leftJoin('calendar_users', 'calendars.id', 'calendar_users.calendar_id')
            ->leftJoin('sub_calendars', 'calendars.id', 'sub_calendars.calendar_id')
            ->where(DB::raw("DATE(sub_calendars.modify_date) != '" . $startDateCalendar . "'"))
            ->where('calendar_users.user_id', $userId)
            ->where('calendars.repeat_id', REPEAT_WEEK)
            ->whereDate('calendars.start_date', '<=', $startDateCalendar)
            ->whereDate('calendars.end_date', '>=', $startDateCalendar)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereTime('calendars.meeting_start_time', '>=', $startTime)
                    ->whereTime('calendars.meeting_start_time', '<', $endTime)
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereTime('calendars.meeting_end_time', '>', $startTime)
                            ->whereTime('calendars.meeting_end_time', '<', $endTime);
                    })->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereTime('calendars.meeting_start_time', '<', $startTime)
                            ->whereTime('calendars.meeting_end_time', '>', $endTime);
                    });
            })->distinct()->select('calendars.id', 'calendars.title', 'calendars.meeting_start_time')->get();
        $startDateCalendar = Carbon::parse($startDateCalendar)->toDateString();
        $calendars = array();
        for ($i = 0; $i < $calendarWeeks->count(); $i++) {
            $meetingStart = Carbon::parse($calendarWeeks[$i]->meeting_start_time)->toDateString();
            $dayDiff = Carbon::parse($meetingStart)->diff(Carbon::parse($startDateCalendar))->days;
            if (($dayDiff % 7) == 0) {
                $item = $this->_model->where('calendars.id', $calendarWeeks[$i]->id)->select('calendars.id', 'calendars.title')->first();
                array_push($calendars, $item);
            }
        }
        return (object)$calendars;
    }

    // show calendar create new & update
    public function showCalendar($id)
    {
        return collect($this->show($id))->except(['sub_calendars', 'sub_calendars.users', 'sub_calendars.documents']);
    }

    // find Sub Calendar Conflict
    public function subCalendarConflict($id, $startDateCalendar, $endDateCalendar, $userId)
    {
        return $this->_model
            ->leftJoin('sub_calendars', 'sub_calendars.calendar_id', 'calendars.id')
            ->where('sub_calendars.calendar_id', '!=', $id)
            ->leftJoin('calendar_users', 'calendars.id', 'calendar_users.calendar_id')
            ->where('calendar_users.user_id', $userId)
            ->where('sub_calendars.is_deleted', IS_DELETED)
            ->where('sub_calendars.deleted_at', null)
            ->where(function ($q) use ($startDateCalendar, $endDateCalendar) {
                $q->whereBetween('sub_calendars.meeting_start_time', [$startDateCalendar, $endDateCalendar])
                    ->orWhere([
                        ['sub_calendars.meeting_end_time', '>', $startDateCalendar],
                        ['sub_calendars.meeting_end_time', '<', $endDateCalendar]
                    ])->orWhere([
                        ['sub_calendars.meeting_start_time', '<', $startDateCalendar],
                        ['sub_calendars.meeting_end_time', '>', $endDateCalendar]
                    ]);
            })->select('sub_calendars.id', 'sub_calendars.title', 'sub_calendars.modify_date')->distinct()->get();
    }

    // find calendar project
    public function findCalendarProject($projectId, $projectPhaseId)
    {
        return $this->_model->where([
            'calendars.project_id' => $projectId,
            'calendars.project_phase_id' => $projectPhaseId
        ])->first();
    }

    public function findProject($projectId, $calendarId)
    {
        return $this->_model->where('calendars.project_id', $projectId)
            ->where('calendars.id', '<>', $calendarId)->get();
    }

    public function findFileCalendar($id)
    {
        return $this->_model->leftJoin('calendar_files', 'calendars.id', 'calendar_files.calendar_id')
            ->where('calendar_files.calendar_id', $id)
            ->select('calendar_files.id', 'calendar_files.name', 'calendar_files.url')
            ->get();
    }

    // find host
    public function findUser($projectId, $projectPhaseId)
    {
        $calendarId = $this->findCalendarProject($projectId, $projectPhaseId)->id;
        return $this->_model->leftJoin('calendar_users', 'calendar_users.calendar_id', 'calendars.id')
            ->where('calendars.id', $calendarId)
            ->where('calendar_users.is_host', IS_HOST)
            ->select('calendar_users.user_id')
            ->first();
    }

    // find all calendar in company
    public function findCalendarInCompany($companyId)
    {
        return $this->_model->where('company_id', $companyId)->get();
    }

    public function reminderCalendarNotRepeats($dateNow)
    {
        return $this->_model
            ->select(
                'id',
                DB::raw("DATE_FORMAT(meeting_start_time, '%H:%i') as date"),
                'notify_id'
            )
            ->where(DB::raw("DATE_FORMAT(meeting_start_time, '%H:%i')"), '=', $dateNow)
            ->where('repeat_id', NOT_REPEAT)
            ->whereIn('notify_id', [3, 4, 5, 6, 7])
            ->get();
    }

    public function reminderCalendarRepeatDays($dateNow, $dateCheck)
    {
        return $this->_model
            ->select(
                'id',
                DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%Y-%m-%d %H:%i') as date"),
                'notify_id'
            )
            ->where(DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%H:%i')"), '>=', $dateNow)
            ->where(DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%H:%i')"), '<=', $dateCheck)
            ->with(['subCalendarNows' => function ($q) {
                return $q->with('subUsers');
            }])
            ->where('repeat_id', REPEAT_DAY)
            ->whereIn('notify_id', [3, 4, 5, 6])
            ->get();
    }

    public function checkExistCalendar($request)
    {
        $startDateCalendar = $request->meeting_start_time;
        $endDateCalendar = $request->meeting_end_time;
        return $this->_model
            ->where(function ($q) use ($startDateCalendar, $endDateCalendar) {
                $q->orWhere([
                    [DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%Y-%m-%d %H:%i')"), '>=', $startDateCalendar],
                    [DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%Y-%m-%d %H:%i')"), '<=', $endDateCalendar]
                ])->orWhere([
                    [DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%Y-%m-%d %H:%i')"), '<=', $startDateCalendar],
                    [DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%Y-%m-%d %H:%i')"), '>=', $endDateCalendar]
                ]);
            })->pluck('calendars.id');
    }

    public function getCalendarStartTimeNow($startDateCalendar)
    {
        return $this->_model
            ->select(
                'calendars.id',
                'calendars.title',
                DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%Y-%m-%d %H:%i') as start_date"),
                'calendars.meeting_start_time',
                'calendars.meeting_end_time',
            )
            ->where(DB::raw("DATE_FORMAT(calendars.meeting_start_time, '%Y-%m-%d %H:%i')"), '=', $startDateCalendar)
            ->where('repeat_id', NOT_REPEAT)
            ->where('notify_id', 2)
            ->with(['calendarUserNows' => function ($q) {
                return $q
                    ->select(
                        'calendar_users.id',
                        'calendar_users.user_id',
                        'calendar_users.calendar_id'
                    )
                    ->whereIn('calendar_users.is_accept', [0, 1]);
            }])
            ->get();
    }
}

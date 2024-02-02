<?php

namespace App\Repositories\SubCalendar;

use App\Models\SubCalendar;
use App\Repositories\Base\BaseEloquentRepository;

class SubCalendarEloquentRepository extends BaseEloquentRepository implements SubCalendarRepositoryInterface
{
    public function getModel()
    {
        return SubCalendar::class;
    }

    // find a sub calendar
    public function findSubCalendar($modifyDateCalendar, $id)
    {
        return $this->_model->where('calendar_id', $id)
            ->whereDate('meeting_start_time', $modifyDateCalendar)
            ->where('deleted_at', null)
            ->where('is_deleted', false)
            ->first();
    }

    // find all calendar repeat month
    public function findAllCalendarMonth($id)
    {
        return $this->_model->where('calendar_id', $id)->get();
    }

    // show sub calendar
    public function show($id)
    {
        return $this->_model->leftJoin('calendars', 'sub_calendars.calendar_id', 'calendars.id')
            ->leftJoin('divisions', 'divisions.id', 'calendars.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'calendars.project_phase_id')
            ->leftJoin('master_schedule_repeats', 'calendars.repeat_id', 'master_schedule_repeats.id')
            ->leftJoin('master_notify_calendars', 'calendars.notify_id', 'master_notify_calendars.id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->leftJoin('projects', 'projects.id', 'calendars.project_id')
            ->select(
                'sub_calendars.*',
                'calendars.meeting_type',
                'calendars.is_public',
                'master_schedule_repeats.repeat as repeat',
                'divisions.name as division_name',
                'master_phase_projects.name as project_phase_name',
                'projects.title as project_name',
                'master_notify_calendars.notify'
            )
            ->where('sub_calendars.id', $id)
            ->with(['users', 'documents'])->first();
    }
}

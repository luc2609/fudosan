<?php

namespace App\Repositories\Calendar;

use App\Repositories\Base\BaseRepositoryInterface;

interface CalendarRepositoryInterface extends BaseRepositoryInterface
{

    public function getModel();

    // Show detail calendar
    public function show($id);

    // list calendar in company
    public function index($request);

    // Get data by attributes
    public function getByAttributes($attributes);

    // Show calendar conflict
    public function findCalendarConflict($request, $id, $userId);

    // find host calendar
    public function findHostCalendar($id);

    public function calendarLoopDay($startDateCalendar, $startTime, $endTime, $id, $userId);

    public function calendarLoopWeek($startDateCalendar, $endDateCalendar, $startTime, $endTime, $id, $userId);

    // show calendar create new & update
    public function showCalendar($id);

    // find Sub Calendar Conflict
    public function subCalendarConflict($id, $startDateCalendar, $endDateCalendar, $userId);

    // find calendar project
    public function findCalendarProject($projectId, $projectPhaseId);

    public function findProject($projectId, $calendarId);

    public function findFileCalendar($id);

    // find host
    public function findUser($projectId, $projectPhaseId);

    // get calendars in company
    public function findCalendarInCompany($companyId);

    public function reminderCalendarNotRepeats($dateNow);

    public function reminderCalendarRepeatDays($dateNow, $dateCheck);

    public function checkExistCalendar($request);

    public function getCalendarStartTimeNow($startDateCalendar);
}

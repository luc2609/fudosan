<?php

namespace App\Repositories\CalendarUser;

use App\Repositories\Base\BaseRepositoryInterface;

interface CalendarUserRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function calendar($calendarId);

    public function findUser($userId, $calendarId);

    public function findCalendar($modifyDateCalendar, $userId, $calendarId);

    public function findUserHost($userId, $calendarId);

    public function countCalendarNotifies($userId);

    public function getUserOfCalendar($calendarIds);

    public function changeNotiCalendar($request, $calendarId, $userId, $startTimeMeeting);

    public function reminderCalendarNotRepeats($dateNow);

    public function reminderCalendarRepeatDay($timeNow, $dateNow);

    public function reminderCalendarRepeatWEEK($timeNow, $dateNow);

    public function reminderCalendarRepeatMonth($dateNow);

    public function getUserOfSubCalendar($subCalendarIds);

    public function checkUserOfCalendar($calendarIds, $userIds);
}

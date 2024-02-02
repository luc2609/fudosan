<?php

namespace App\Repositories\SubCalendar;

use App\Repositories\Base\BaseRepositoryInterface;

interface SubCalendarRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    // find a sub calendar
    public function findSubCalendar($modifyDateCalendar, $id);

    // find all calendar repeat month
    public function findAllCalendarMonth($id);

    // show sub calendar
    public function show($id);
}

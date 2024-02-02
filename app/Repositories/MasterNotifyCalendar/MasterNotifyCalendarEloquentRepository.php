<?php

namespace App\Repositories\MasterNotifyCalendar;

use App\Models\MasterNotifyCalendar;
use App\Repositories\Base\BaseEloquentRepository;

class MasterNotifyCalendarEloquentRepository extends BaseEloquentRepository implements MasterNotifyCalendarRepositoryInterface
{
    public function getModel()
    {
        return MasterNotifyCalendar::class;
    }
}

<?php

namespace App\Repositories\SubCalendarFile;

use App\Models\SubCalendarFile;
use App\Repositories\Base\BaseEloquentRepository;

class SubCalendarFileEloquentRepository extends BaseEloquentRepository implements SubCalendarFileRepositoryInterface
{
    public function getModel()
    {
        return SubCalendarFile::class;
    }

    public function findFile($calendarFileId, $subCalendarId)
    {
        return $this->_model->where('calendar_file_id', $calendarFileId)
            ->where('sub_calendar_id', $subCalendarId)->first();
    }
}

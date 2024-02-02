<?php

namespace App\Repositories\CalendarFile;

use App\Models\CalendarFile;
use App\Repositories\Base\BaseEloquentRepository;

class CalendarFileEloquentRepository extends BaseEloquentRepository implements CalendarFileRepositoryInterface
{
    public function getModel()
    {
        return CalendarFile::class;
    }
}

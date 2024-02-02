<?php

namespace App\Repositories\SubCalendarUser;

use App\Models\SubCalendarUser;
use App\Repositories\Base\BaseEloquentRepository;

class SubCalendarUserEloquentRepository extends BaseEloquentRepository implements SubCalendarUserRepositoryInterface
{
    public function getModel()
    {
        return SubCalendarUser::class;
    }
}

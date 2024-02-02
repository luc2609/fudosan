<?php

namespace App\Repositories\SubCalendarFile;

use App\Repositories\Base\BaseRepositoryInterface;

interface SubCalendarFileRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function findFile($calendarFileId, $subCalendarId);
}

<?php

namespace App\Repositories\MasterRail;

use App\Repositories\Base\BaseRepositoryInterface;

interface MasterRailRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    public function list($params);
}

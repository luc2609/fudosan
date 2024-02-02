<?php

namespace App\Repositories\File;

use App\Repositories\Base\BaseRepositoryInterface;

interface FileRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();
}

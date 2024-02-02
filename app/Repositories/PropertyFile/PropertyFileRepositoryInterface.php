<?php

namespace App\Repositories\PropertyFile;

use App\Repositories\Base\BaseRepositoryInterface;

interface PropertyFileRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();
}

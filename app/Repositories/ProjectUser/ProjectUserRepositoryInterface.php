<?php

namespace App\Repositories\ProjectUser;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProjectUserRepositoryInterface extends BaseRepositoryInterface
{
    public function getModel();

    // list project in progress relate user
    public function getProjectInProgressUser($id);

    public function countProjectRequestClose($userId);
}

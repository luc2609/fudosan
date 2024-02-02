<?php

namespace App\Repositories\ProjectUser;

use App\Models\ProjectUser;
use App\Repositories\Base\BaseEloquentRepository;

class ProjectUserEloquentRepository extends BaseEloquentRepository implements ProjectUserRepositoryInterface
{
    public function getModel()
    {
        return ProjectUser::class;
    }

    // list project in progress relate user
    public function getProjectInProgressUser($id)
    {
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE];
        return $this->_model->leftJoin('projects', 'project_users.project_id', 'projects.id')
            ->where('user_id', $id)
            ->whereIn('projects.close_status',  $paramInProgress);
    }

    public function countProjectRequestClose($userId)
    {
        return $this->_model
            ->join('projects', 'projects.id', 'project_users.project_id')
            ->where('project_users.user_id', $userId)
            ->where('projects.close_status', REQUEST_CLOSE)
            ->where('project_users.user_type', IS_HOST)->count();
    }
}

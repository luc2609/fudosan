<?php

namespace App\Repositories\ProjectHistory;

use App\Models\ProjectHistory;
use App\Repositories\Base\BaseEloquentRepository;

class ProjectHistoryEloquentRepository extends BaseEloquentRepository implements ProjectHistoryRepositoryInterface
{
    public function getModel()
    {
        return ProjectHistory::class;
    }
}

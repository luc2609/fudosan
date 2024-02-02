<?php

namespace App\Repositories\ProjectFile;

use App\Models\ProjectFile;
use App\Repositories\Base\BaseEloquentRepository;

class ProjectFileEloquentRepository extends BaseEloquentRepository implements ProjectFileRepositoryInterface
{
    public function getModel()
    {
        return ProjectFile::class;
    }
}

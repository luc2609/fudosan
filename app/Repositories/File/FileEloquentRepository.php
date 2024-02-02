<?php

namespace App\Repositories\File;

use App\Models\File;
use App\Repositories\Base\BaseEloquentRepository;

class FileEloquentRepository extends BaseEloquentRepository implements FileRepositoryInterface
{
    public function getModel()
    {
        return File::class;
    }
}

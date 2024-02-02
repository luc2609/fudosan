<?php

namespace App\Repositories\MasterPhaseProject;

use App\Models\MasterPhaseProject;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPhaseProjectEloquentRepository extends BaseEloquentRepository implements MasterPhaseProjectRepositoryInterface
{
    /**
     * @return string
     *  Return the model
     */
    public function getModel()
    {
        return MasterPhaseProject::class;
    }

    public function list()
    {
        return $this->_model->where('id', '<>', NO_PHASE)->where('status', '<>', INACTIVE)->get();
    }
}

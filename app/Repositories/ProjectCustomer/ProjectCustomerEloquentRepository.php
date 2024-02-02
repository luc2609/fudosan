<?php

namespace App\Repositories\ProjectCustomer;

use App\Models\ProjectCustomer;
use App\Repositories\Base\BaseEloquentRepository;

class ProjectCustomerEloquentRepository extends BaseEloquentRepository implements ProjectCustomerRepositoryInterface
{
    public function getModel()
    {
        return ProjectCustomer::class;
    }

    public function getList()
    {
        return $this->_model;
    }

    public function delete($id)
    {
        $projectCustomerIds =  $this->_model->where('project_id', $id)->pluck('id');
        foreach ($this->_model->whereIn('id',  $projectCustomerIds)->get() as $model) {
            $model->delete();
        }
    }
}

<?php

namespace App\Repositories\MasterContactMethod;

use App\Models\MasterContactMethod;
use App\Repositories\Base\BaseEloquentRepository;

class MasterContactMethodEloquentRepository extends BaseEloquentRepository implements MasterContactMethodRepositoryInterface
{
    public function getModel()
    {
        return MasterContactMethod::class;
    }
    public function checkExistMasterData($name, $id)
    {
        if ($id) {
            return $this->_model->where(
                [
                    ['contact_method', 'like BINARY', $name],
                    ['status', ACTIVE],
                    ['id', '!=', $id],
                ]
            )->first();
        } else {
            return $this->_model->where([
                ['contact_method', 'like BINARY', $name],
                ['status', ACTIVE],
            ])->exists();
        }
    }
}

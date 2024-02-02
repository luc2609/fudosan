<?php

namespace App\Repositories\MasterSalePurpose;

use App\Models\MasterSalePurpose;
use App\Repositories\Base\BaseEloquentRepository;

class MasterSalePurposeEloquentRepository extends BaseEloquentRepository implements MasterSalePurposeRepositoryInterface
{
    public function getModel()
    {
        return MasterSalePurpose::class;
    }
    public function checkExistMasterData($name, $id)
    {
        if ($id) {
            return $this->_model->where(
                [
                    ['sale_purpose', 'like BINARY', $name],
                    ['status', ACTIVE],
                    ['id', '!=', $id],
                ]
            )->first();
        } else {
            return $this->_model->where([
                ['sale_purpose', 'like BINARY', $name],
                ['status', ACTIVE]
            ])->exists();
        }
    }
}

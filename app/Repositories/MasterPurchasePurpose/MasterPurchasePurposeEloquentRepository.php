<?php

namespace App\Repositories\MasterPurchasePurpose;

use App\Models\MasterPurchasePurpose;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPurchasePurposeEloquentRepository extends BaseEloquentRepository implements MasterPurchasePurposeRepositoryInterface
{
    public function getModel()
    {
        return MasterPurchasePurpose::class;
    }
    public function checkExistMasterData($name, $id)
    {
        if ($id) {
            return $this->_model->where(
                [
                    ['purchase_purpose', 'like BINARY', $name],
                    ['status', ACTIVE],
                    ['id', '!=', $id],
                ]
            )->first();
        } else {
            return $this->_model->where([
                ['purchase_purpose', 'like BINARY', $name],
                ['status', ACTIVE]
            ])->exists();
        }
    }
}

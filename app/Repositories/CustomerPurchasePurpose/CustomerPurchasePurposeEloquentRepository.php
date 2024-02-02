<?php

namespace App\Repositories\CustomerPurchasePurpose;

use App\Models\CustomerPurchasePurpose;
use App\Repositories\Base\BaseEloquentRepository;

class CustomerPurchasePurposeEloquentRepository extends BaseEloquentRepository implements CustomerPurchasePurposeRepositoryInterface
{
    public function getModel()
    {
        return CustomerPurchasePurpose::class;
    }

    public function findCustomerPurchasePurposeId($customerId, $purchasePurposeId)
    {
        return $this->_model->where([
            'customer_id' => $customerId,
            'purchase_purpose_id' => $purchasePurposeId

        ])->first();
    }
}

<?php

namespace App\Repositories\CustomerAdvertisingForm;

use App\Models\CustomerAdvertisingForm;
use App\Repositories\Base\BaseEloquentRepository;

class CustomerAdvertisingFormEloquentRepository extends BaseEloquentRepository implements CustomerAdvertisingFormRepositoryInterface
{
    public function getModel()
    {
        return CustomerAdvertisingForm::class;
    }

    public function findCustomerAdvertisingForm($customerId, $advertisingFormId)
    {
        return $this->_model->where([
            'customer_id' => $customerId,
            'advertising_form_id' => $advertisingFormId
        ])->first();
    }
}

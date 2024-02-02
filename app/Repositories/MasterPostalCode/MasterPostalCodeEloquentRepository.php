<?php

namespace App\Repositories\MasterPostalCode;

use App\Models\MasterPostalCode;
use App\Models\MasterProvince;
use App\Repositories\Base\BaseEloquentRepository;

class MasterPostalCodeEloquentRepository extends BaseEloquentRepository implements MasterPostalCodeRepositoryInterface
{
    public function getModel()
    {
        return MasterPostalCode::class;
    }

    public function findByPostalCode($postalCode)
    {
        return $this->_model->where('postal_code', $postalCode)->first();
    }

    public function listDistrict($params)
    {
        $query = $this->_model->select('district')->groupBy('district');

        if (isset($params['province_cd'])) {
            $province = MasterProvince::where('cd', $params['province_cd'])->first();
            $provinceName = $province ? $province->name : null;
            $query->where('province', $provinceName);
        }

        return $query->get();
    }

    public function indexPostalCode($request)
    {
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $postalCode = $this->_model->where('status', ACTIVE)->paginate($pageSize);
        return [
            'postal_codes' => $postalCode->items(),
            'total_items' => $postalCode->total()
        ];
    }
}

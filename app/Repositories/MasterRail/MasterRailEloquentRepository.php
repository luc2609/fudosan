<?php

namespace App\Repositories\MasterRail;

use App\Models\MasterRail;
use App\Models\MasterStation;
use App\Repositories\Base\BaseEloquentRepository;

class MasterRailEloquentRepository extends BaseEloquentRepository implements MasterRailRepositoryInterface
{
    public function getModel()
    {
        return MasterRail::class;
    }

    public function list($params)
    {
        $query =  $this->_model->select(['cd', 'name'])->where('status', ACTIVE)->groupBy('cd', 'name');

        if (isset($params['province_cd'])) {
            $provinceCd = $params['province_cd'];

            $query->where('province_cd', $provinceCd)
                ->with(['stations' => function ($q) use ($provinceCd) {
                    $q->where('master_stations.province_cd', $provinceCd);
                    $q->select(['province_cd', 'rail_cd', 'cd', 'name']);
                }]);
        } else {
            $query->with(['stations' => function ($q) {
                $q->select(['rail_cd', 'cd', 'name']);
                $q->groupBy(['rail_cd', 'cd', 'name']);
            }]);
        }

        if (isset($params['name'])) {
            $name = $params['name'];

            $query->where(function ($q) use ($name) {
                $q->whereHas('stations', function ($q1) use ($name) {
                    $q1->where('master_stations.name', 'like', '%' . $name  . '%');
                });
                $q->orWhere('name', 'like', '%' . $name . '%');
            });
        }

        return $query->get();
    }

    public function getMasterStation($railCd, $stationCd)
    {
        return MasterStation::where('rail_cd', $railCd)->where('cd', $stationCd)->first();
    }
}

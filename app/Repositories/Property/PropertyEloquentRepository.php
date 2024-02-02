<?php

namespace App\Repositories\Property;

use App\Models\MasterPrice;
use App\Models\MasterProvince;
use App\Models\Property;
use App\Repositories\Base\BaseEloquentRepository;

class PropertyEloquentRepository extends BaseEloquentRepository implements PropertyRepositoryInterface
{
    public function getModel()
    {
        return Property::class;
    }

    // List property in company
    public function listInCompany($companyId, $params)
    {
        $listProperty = $this->_model->where('properties.company_id', $companyId)
            ->select([
                'properties.*',
                'master_property_contract_types.name as contract_type',
                'master_property_types.name as properties_type',
                'master_property_building_structures.name as building_structure',
            ])
            ->leftJoin(
                'master_property_contract_types',
                'properties.contract_type_id',
                'master_property_contract_types.id'
            )
            ->leftJoin(
                'master_property_types',
                'properties.properties_type_id',
                'master_property_types.id'
            )
            ->leftJoin(
                'master_property_building_structures',
                'properties.building_structure_id',
                'master_property_building_structures.id'
            )->with('projects', 'customFields');

        if (isset($params['properties_type_id'])) {
            $listProperty->where('properties.properties_type_id', $params['properties_type_id']);
        }

        if (isset($params['status'])) {
            $listProperty->where('properties.status', $params['status']);
        }

        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $listProperty->where('properties.name', 'like BINARY', '%' . $name . '%');
        }

        if (isset($params['contract_type_id'])) {
            $listProperty->where('properties.contract_type_id', $params['contract_type_id']);
        }

        if (isset($params['building_structure_id'])) {
            $listProperty->where('properties.building_structure_id', $params['building_structure_id']);
        }

        if (isset($params['min_price_id'])) {
            $masterPrice = MasterPrice::find($params['min_price_id']);

            if ($masterPrice) {
                $minPrice = $masterPrice->price;
                $listProperty->where('price', '>=', $minPrice);
            }
        }

        if (isset($params['max_price_id'])) {
            $masterPrice = MasterPrice::find($params['max_price_id']);

            if ($masterPrice) {
                $maxPrice = $masterPrice->price;
                $listProperty->where('price', '<=', $maxPrice);
            }
        }

        if (isset($params['province_cd'])) {
            $province = MasterProvince::where('cd', $params['province_cd'])->first();

            if ($province) {
                $listProperty->where('province', $province->name);
            }
        }

        if (isset($params['district'])) {
            $listProperty->where('district', $params['district']);
        }

        if (isset($params['rail_stations'])) {
            $railStations = $params['rail_stations'];

            $listProperty->whereHas('propertyStations', function ($query) use ($railStations) {
                foreach ($railStations as $key => $railStation) {
                    if (is_string($railStation)) {
                        $railStation = json_decode($railStation, true);
                    }

                    if (isset($railStation['rail_cd']) && isset($railStation['station_cd'])) {
                        $railCd = $railStation['rail_cd'];
                        $stationCd = $railStation['station_cd'];

                        if ($key == 0) {
                            $query->where('property_stations.rail_cd', $railCd)
                                ->where('property_stations.station_cd', $stationCd);
                        } else {
                            $query->orWhere(function ($q) use ($railCd, $stationCd) {
                                $q->where('property_stations.rail_cd', $railCd)
                                    ->where('property_stations.station_cd', $stationCd);
                            });
                        }
                    } else if (isset($railStation['rail_cd'])) {
                        $railCd = $railStation['rail_cd'];

                        if ($key == 0) {
                            $query->where('property_stations.rail_cd', $railCd);
                        } else {
                            $query->orWhere('property_stations.rail_cd', $railCd);
                        }
                    }
                }
            });
        }

        if (isset($params['sort_by'])) {
            $sortBy = $params['sort_by'];
            $sortType = $params['sort_type'] ?? 'ASC';

            if ($sortBy == 'construction_date') {
                switch ($sortType) {
                    case 'DESC':
                        $sortType = 'ASC';
                        break;
                    case 'ASC':
                    default:
                        $sortType = 'DESC';
                        break;
                }
            }

            switch ($sortBy) {
                case 'land_area':
                    $listProperty->orderBy('land_area', $sortType);
                    break;
                case 'total_floor_area':
                    $listProperty->orderBy('total_floor_area', $sortType);
                    break;
                case 'usage_ratio':
                    $listProperty->orderBy('usage_ratio', $sortType);
                    break;
                case 'empty_ratio':
                    $listProperty->orderBy('empty_ratio', $sortType);
                    break;
                case 'price':
                    $listProperty->orderBy('price', $sortType);
                    break;
                case 'construction_date':
                    $listProperty->orderBy('construction_date', $sortType);
                    break;
                default:
                    $listProperty->orderBy('id', 'DESC');
                    break;
            }
        } else {
            $listProperty->orderBy('id', 'DESC');
        }

        return $listProperty;
    }

    // Get data by attributes
    public function getByAttributes($attributes)
    {
        $query = $this->_model->select('*');

        if (empty($attributes)) {
            return $query->all();
        }

        if (isset($attributes['name'])) {
            $query->where('name', $attributes['name']);
        }

        if (isset($attributes['address'])) {
            $query->where('address', $attributes['address']);
        }

        if (isset($attributes['postal_code'])) {
            $query->where('postal_code', $attributes['postal_code']);
        }

        if (isset($attributes['properties_type_id'])) {
            $query->where('properties_type_id', $attributes['properties_type_id']);
        }

        if (isset($attributes['status'])) {
            $query->where('status', $attributes['status']);
        }

        if (isset($attributes['company_id'])) {
            $query->where('company_id', $attributes['company_id']);
        }

        return $query->get();
    }

    // Get one property
    public function get($params, $id, $companyId)
    {
        $property = $this->_model
            ->select([
                'properties.*',
                'master_property_contract_types.name as contract_type',
                'master_property_types.name as properties_type',
                'master_property_building_structures.name as building_structure'
            ])
            ->leftJoin(
                'master_property_contract_types',
                'properties.contract_type_id',
                'master_property_contract_types.id'
            )
            ->leftJoin(
                'master_property_types',
                'properties.properties_type_id',
                'master_property_types.id'
            )
            ->leftJoin(
                'master_property_building_structures',
                'properties.building_structure_id',
                'master_property_building_structures.id'
            )
            ->where('properties.id', $id)
            ->with(['propertyStations', 'images', 'documents', 'projects', 'customFields'])
            ->first()->toArray();

        $nextBackProperty = $this->nextBackProperty($params, $id, $companyId);
        return array_merge($property, $nextBackProperty);
    }

    public function nextBackProperty($params, $id, $companyId)
    {
        $properties = $this->listInCompany($companyId, $params)->pluck('id');
        $arrayProperty = $properties->toArray();
        $backProperty = null;
        $nextProperty = null;
        if (in_array($id, $arrayProperty)) {
            $propertyIndex =  $properties->search($id);
            if ($propertyIndex == 0) {
                $backProperty = null;
            } else {
                $key =  $propertyIndex - POSITION;
                $backProperty = $arrayProperty[$key];
            }

            if ((array_key_last($arrayProperty)) == $propertyIndex) {
                $nextProperty = null;
            } else {
                $key =  $propertyIndex + POSITION;
                $nextProperty = $arrayProperty[$key];
            }
        }
        return [
            'next_id' => $nextProperty,
            'back_id' => $backProperty
        ];
    }
}

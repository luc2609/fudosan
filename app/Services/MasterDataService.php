<?php

namespace App\Services;

use App\Repositories\MasterAdvertisingWeb\MasterAdvertisingWebRepositoryInterface;
use App\Repositories\MasterBrokerageFee\MasterBrokerageFeeRepositoryInterface;
use App\Repositories\MasterPostalCode\MasterPostalCodeRepositoryInterface;
use App\Repositories\MasterPrice\MasterPriceRepositoryInterface;
use App\Repositories\MasterPropertyBuildingStructure\MasterPropertyBuildingStructureRepositoryInterface;
use App\Repositories\MasterPropertyContractType\MasterPropertyContractTypeRepositoryInterface;
use App\Repositories\MasterPropertyCurrentSituation\MasterPropertyCurrentSituationRepositoryInterface;
use App\Repositories\MasterPropertyType\MasterPropertyTypeRepositoryInterface;
use App\Repositories\MasterProvince\MasterProvinceRepositoryInterface;
use App\Repositories\MasterRail\MasterRailRepositoryInterface;
use App\Repositories\MasterResidenceYear\MasterResidenceYearRepositoryInterface;
use App\Repositories\MasterContactMethod\MasterContactMethodRepositoryInterface;
use App\Repositories\MasterPurchasePurpose\MasterPurchasePurposeRepositoryInterface;
use App\Repositories\MasterScheduleRepeat\MasterScheduleRepeatRepositoryInterface;
use App\Repositories\MasterAdvertisingForm\MasterAdvertisingFormRepositoryInterface;
use App\Repositories\MasterContactReason\MasterContactReasonRepositoryInterface;
use App\Repositories\MasterField\MasterFieldRepositoryInterface;
use App\Repositories\MasterPhaseProject\MasterPhaseProjectRepositoryInterface;
use App\Repositories\MasterSalePurpose\MasterSalePurposeRepositoryInterface;
use App\Repositories\MasterNotifyCalendar\MasterNotifyCalendarRepositoryInterface;
use App\Repositories\MasterPosition\MasterPositionRepositoryInterface;
use App\Repositories\MasterStation\MasterStationRepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;


class MasterDataService
{
    protected $masterPostalCodeInterface;
    protected $masterPropertyContractTypeInterface;
    protected $masterPropertyCurrentSituationInterface;
    protected $masterPropertyTypeInterface;
    protected $masterPropertyBuildingStructureInterface;
    protected $masterAdvertisingWebInterface;
    protected $masterProvinceInterface;
    protected $masterRailInterface;
    protected $masterPriceInterface;
    protected $masterBrokerageFeeInterface;
    protected $masterResidenceYearInterface;
    protected $masterContactMethodInterface;
    protected $masterPurchasePurposesInterface;
    protected $masterScheduleRepeatInterface;
    protected $masterPhaseProjectInterface;
    protected $masterAdvertisingFormInterface;
    protected $masterSalePurposeInterface;
    protected $masterNotifyCalendarInterface;
    protected $masterContactReasonInterface;
    protected $masterFieldInterface;
    protected $masterPositionInterface;
    protected $masterStationInterface;
    protected $roleRepositoryInterface;

    public function __construct(
        MasterPostalCodeRepositoryInterface $masterPostalCodeInterface,
        MasterPropertyContractTypeRepositoryInterface $masterPropertyContractTypeInterface,
        MasterPropertyCurrentSituationRepositoryInterface $masterPropertyCurrentSituationInterface,
        MasterPropertyTypeRepositoryInterface $masterPropertyTypeInterface,
        MasterPropertyBuildingStructureRepositoryInterface $masterPropertyBuildingStructureInterface,
        MasterAdvertisingWebRepositoryInterface $masterAdvertisingWebInterface,
        MasterProvinceRepositoryInterface $masterProvinceInterface,
        MasterRailRepositoryInterface $masterRailInterface,
        MasterPriceRepositoryInterface $masterPriceInterface,
        MasterBrokerageFeeRepositoryInterface $masterBrokerageFeeInterface,
        MasterResidenceYearRepositoryInterface $masterResidenceYearInterface,
        MasterContactMethodRepositoryInterface $masterContactMethodInterface,
        MasterPurchasePurposeRepositoryInterface $masterPurchasePurposesInterface,
        MasterScheduleRepeatRepositoryInterface $masterScheduleRepeatInterface,
        MasterPhaseProjectRepositoryInterface $masterPhaseProjectInterface,
        MasterAdvertisingFormRepositoryInterface $masterAdvertisingFormInterface,
        MasterSalePurposeRepositoryInterface $masterSalePurposeInterface,
        MasterNotifyCalendarRepositoryInterface $masterNotifyCalendarInterface,
        MasterContactReasonRepositoryInterface $masterContactReasonInterface,
        MasterFieldRepositoryInterface $masterFieldInterface,
        MasterPositionRepositoryInterface $masterPositionInterface,
        MasterStationRepositoryInterface $masterStationInterface,
        RoleRepositoryInterface $roleRepositoryInterface
    ) {
        $this->masterPostalCodeInterface = $masterPostalCodeInterface;
        $this->masterPropertyContractTypeInterface = $masterPropertyContractTypeInterface;
        $this->masterPropertyCurrentSituationInterface = $masterPropertyCurrentSituationInterface;
        $this->masterPropertyTypeInterface = $masterPropertyTypeInterface;
        $this->masterPropertyBuildingStructureInterface = $masterPropertyBuildingStructureInterface;
        $this->masterAdvertisingWebInterface = $masterAdvertisingWebInterface;
        $this->masterProvinceInterface = $masterProvinceInterface;
        $this->masterRailInterface = $masterRailInterface;
        $this->masterPriceInterface = $masterPriceInterface;
        $this->masterBrokerageFeeInterface = $masterBrokerageFeeInterface;
        $this->masterResidenceYearInterface = $masterResidenceYearInterface;
        $this->masterContactMethodInterface = $masterContactMethodInterface;
        $this->masterPurchasePurposesInterface = $masterPurchasePurposesInterface;
        $this->masterScheduleRepeatInterface = $masterScheduleRepeatInterface;
        $this->masterPhaseProjectInterface = $masterPhaseProjectInterface;
        $this->masterAdvertisingFormInterface = $masterAdvertisingFormInterface;
        $this->masterSalePurposeInterface = $masterSalePurposeInterface;
        $this->masterNotifyCalendarInterface = $masterNotifyCalendarInterface;
        $this->masterContactReasonInterface = $masterContactReasonInterface;
        $this->masterFieldInterface = $masterFieldInterface;
        $this->masterStationInterface = $masterStationInterface;
        $this->masterPositionInterface = $masterPositionInterface;
        $this->roleRepositoryInterface = $roleRepositoryInterface;
    }

    /**
     * @param $postalCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function address($postalCode)
    {
        $masterPostalCode = $this->masterPostalCodeInterface->findByPostalCode($postalCode);

        if (!$masterPostalCode) {
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        }

        $provinceCd = $this->masterProvinceInterface->findByName($masterPostalCode->province)->cd;

        $data = [
            'postal_code' => $masterPostalCode->postal_code,
            'province' => $masterPostalCode->province,
            'district' => $masterPostalCode->district,
            'street' => $masterPostalCode->street,
            'province_cd' => $provinceCd
        ];

        return _success($data, __('message.get_master_postal_code_success'), HTTP_SUCCESS);
    }

    public function indexPropertyContractType()
    {
        $listPropertyContractType = $this->masterPropertyContractTypeInterface->listMasterData();

        return _success($listPropertyContractType, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPropertyCurrentSituation()
    {
        $listPropertyCurrentSituation = $this->masterPropertyCurrentSituationInterface->listMasterData();
        return _success($listPropertyCurrentSituation, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPropertyType()
    {
        $listPropertyType = $this->masterPropertyTypeInterface->listMasterData();
        return _success($listPropertyType, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPropertyBuildingStructure()
    {
        $listPropertyBuildingStructure = $this->masterPropertyBuildingStructureInterface->listMasterData();
        return _success($listPropertyBuildingStructure, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexAdvertisingWeb()
    {
        $listAdvertisingWeb = $this->masterAdvertisingWebInterface->listMasterData();
        return _success($listAdvertisingWeb, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexProvince()
    {
        $listProvince = $this->masterProvinceInterface->listMasterData();

        return _success($listProvince, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexDistrict($params)
    {
        $listDistrict = $this->masterPostalCodeInterface->listDistrict($params);
        return _success($listDistrict, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexRail($params)
    {
        if (!isset($params['province_cd'])) {
            $listRailCache = Redis::get('rails.all');

            if (isset($listRailCache)) {
                $rails = json_decode($listRailCache, false);
            } else {
                $rails = $this->masterRailInterface->list($params);
                Redis::set('rails.all', $rails);
            }
        } else {
            $provinceCd = $params['province_cd'];
            $listRailCache = Redis::get('rails.province' . $provinceCd);

            if (isset($listRailCache)) {
                $rails = json_decode($listRailCache, false);
            } else {
                $rails = $this->masterRailInterface->list($params);
                Redis::set('rails.province' . $provinceCd, $rails);
            }
        }

        $data = [
            'rails' => $rails,
            'items_total' => count($rails)
        ];

        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPrice()
    {
        $listPrice = $this->masterPriceInterface->listMasterData();

        return _success($listPrice, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexBrokerageFee()
    {
        $listBrokerageFee = $this->masterBrokerageFeeInterface->listMasterData();

        return _success($listBrokerageFee, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexResidenceYears()
    {
        $listResidenceYear = $this->masterResidenceYearInterface->listMasterData();

        return _success($listResidenceYear, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexContactMethods()
    {
        $listContactMethod = $this->masterContactMethodInterface->listMasterData();

        return _success($listContactMethod, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPurchasePurposes()
    {
        $listPurchasePurposes = $this->masterPurchasePurposesInterface->listMasterData();

        return _success($listPurchasePurposes, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexScheduleRepeats()
    {
        $listScheduleRepeats = $this->masterScheduleRepeatInterface->listMasterData();

        return _success($listScheduleRepeats, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPhaseProject()
    {
        $listPhaseProject = $this->masterPhaseProjectInterface->list();

        return _success($listPhaseProject, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexAdvertisingForms()
    {
        $listAdvertisingForms = $this->masterAdvertisingFormInterface->listMasterData();

        return _success($listAdvertisingForms, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function  indexSalePurposes()
    {
        $listSalePurposes = $this->masterSalePurposeInterface->listMasterData();

        return _success($listSalePurposes, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function  indexNotifyCalendars()
    {
        $listSalePurposes = $this->masterNotifyCalendarInterface->listMasterData();

        return _success($listSalePurposes, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexContactReason()
    {
        $listContactReason = $this->masterContactReasonInterface->listMasterData();
        return _success($listContactReason, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexMasterField()
    {
        $listContactReason = $this->masterFieldInterface->listMasterData();
        return _success($listContactReason, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexPostalCode($request)
    {
        $listPostalCode = $this->masterPostalCodeInterface->indexPostalCode($request);
        return _success($listPostalCode, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function indexRole()
    {
        $listPostalCode = $this->roleRepositoryInterface->all();
        return _success($listPostalCode, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function curdMasterData($request, $typeData, $id = null, $action = null)
    {
        if ($action == DELETE_MASTER_DATA) {
            $attributes['status'] = INACTIVE;
        } else {
            switch ($typeData) {
                case MASTER_CONTACT_METHOD: {
                        $attributes['contact_method'] = $request->name;
                        break;
                    }
                case MASTER_PURCHASE_PURPOSE: {
                        $attributes['purchase_purpose'] = $request->name;
                        break;
                    }
                case MASTER_SALE_PURPOSE: {
                        $attributes['sale_purpose'] = $request->name;
                        break;
                    }
                default:
                    $attributes = $request->all();
                    break;
            }
        }
        if ($id) {
            $name = $request->name;
            $resultFail = _error(null, __('message.data_already_exists'), HTTP_SUCCESS);
            $resultSuccess = _success(null, __('message.updated_success'), HTTP_SUCCESS);
            switch ($typeData) {
                case MASTER_ADVERTISING_FORM: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterAdvertisingFormInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterAdvertisingFormInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_ADVERTISING_WEB: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterAdvertisingWebInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterAdvertisingWebInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_BROKERAGE_FEE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterAdvertisingWebInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterBrokerageFeeInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_CONTACT_METHOD: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterContactMethodInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterContactMethodInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_CONTACT_REASON: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterContactReasonInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterContactReasonInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_CONTACT_TYPE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPropertyContractTypeInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyContractTypeInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_FIELD: {
                        $this->masterFieldInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_NOTIFY_CALENDAR: {
                        $this->masterNotifyCalendarInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_PHASE_PROJECT: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPhaseProjectInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPhaseProjectInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_POSITION: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPositionInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPositionInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_POSTAL_CODE: {
                        $this->masterPostalCodeInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_PRICE: {
                        $this->masterPriceInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_PROPERTY_BUILDING_STRUCTURE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPropertyBuildingStructureInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyBuildingStructureInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_CONTRACT_TYPE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPropertyContractTypeInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyContractTypeInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_CURRENT_SITUATION: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPropertyCurrentSituationInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyCurrentSituationInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_TYPE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPropertyTypeInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyTypeInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROVINCE: {
                        $this->masterProvinceInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_PURCHASE_PURPOSE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterPurchasePurposesInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPurchasePurposesInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_RAIL: {
                        $this->masterRailInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_RESIDENCE_YEAR: {
                        $this->masterResidenceYearInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_SALE_PURPOSE: {
                        $existsData = $action != DELETE_MASTER_DATA ? $this->masterSalePurposeInterface->checkExistMasterData($name, $id) : null;
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterSalePurposeInterface->update($id, $attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_SCHEDULE_REPEAT: {
                        $this->masterScheduleRepeatInterface->update($id, $attributes);
                        break;
                    }
                case MASTER_STATION: {
                        $this->masterStationInterface->update($id, $attributes);
                        break;
                    }
                default:
                    break;
            }
            return $result;
        } else {
            $name = $request->name;
            $resultFail = _error(null, __('message.data_already_exists'), HTTP_SUCCESS);
            $resultSuccess = _success(null, __('message.created_success'), HTTP_SUCCESS);
            switch ($typeData) {
                case MASTER_ADVERTISING_FORM: {
                        $existsData = $this->masterAdvertisingFormInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterAdvertisingFormInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_ADVERTISING_WEB: {
                        $existsData = $this->masterAdvertisingWebInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterAdvertisingWebInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_CONTACT_METHOD: {
                        $existsData = $this->masterContactMethodInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterContactMethodInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_CONTACT_REASON: {
                        $existsData = $this->masterContactReasonInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterContactReasonInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_CONTACT_TYPE: {
                        $existsData = $this->masterPropertyContractTypeInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyContractTypeInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PHASE_PROJECT: {
                        $existsData = $this->masterPhaseProjectInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPhaseProjectInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_POSITION: {
                        $existsData = $this->masterPositionInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPositionInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_BUILDING_STRUCTURE: {
                        $existsData = $this->masterPropertyBuildingStructureInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyBuildingStructureInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_CONTRACT_TYPE: {
                        $existsData = $this->masterPropertyContractTypeInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyContractTypeInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_CURRENT_SITUATION: {
                        $existsData = $this->masterPropertyCurrentSituationInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyCurrentSituationInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_PROPERTY_TYPE: {
                        $existsData = $this->masterPropertyTypeInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPropertyTypeInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }

                case MASTER_PURCHASE_PURPOSE: {
                        $existsData = $this->masterPurchasePurposesInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterPurchasePurposesInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                case MASTER_SALE_PURPOSE: {
                        $existsData = $this->masterSalePurposeInterface->checkExistMasterData($name, null);
                        if ($existsData) {
                            $result = $resultFail;
                        } else {
                            $this->masterSalePurposeInterface->create($attributes);
                            $result = $resultSuccess;
                        }
                        break;
                    }
                default:
                    break;
            }
            return $result;
        }
    }


    public function showMasterData($typeData, $id)
    {
        switch ($typeData) {
            case MASTER_ADVERTISING_FORM: {
                    $data = $this->masterAdvertisingFormInterface->find($id);
                    break;
                }
            case MASTER_ADVERTISING_WEB: {
                    $data = $this->masterAdvertisingWebInterface->find($id);
                    break;
                }
            case MASTER_BROKERAGE_FEE: {
                    $data = $this->masterBrokerageFeeInterface->find($id);
                    break;
                }
            case MASTER_CONTACT_METHOD: {
                    $data = $this->masterContactMethodInterface->find($id);
                    break;
                }
            case MASTER_CONTACT_REASON: {
                    $data = $this->masterContactReasonInterface->find($id);
                    break;
                }
            case MASTER_CONTACT_TYPE: {
                    $data = $this->masterPropertyContractTypeInterface->find($id);
                    break;
                }
            case MASTER_FIELD: {
                    $data = $this->masterFieldInterface->find($id);
                    break;
                }
            case MASTER_NOTIFY_CALENDAR: {
                    $data = $this->masterNotifyCalendarInterface->find($id);
                    break;
                }
            case MASTER_PHASE_PROJECT: {
                    $data = $this->masterPhaseProjectInterface->find($id);
                    break;
                }
            case MASTER_POSITION: {
                    $data = $this->masterPositionInterface->find($id);
                    break;
                }
            case MASTER_POSTAL_CODE: {
                    $data = $this->masterPostalCodeInterface->find($id);
                    break;
                }
            case MASTER_PRICE: {
                    $data = $this->masterPriceInterface->find($id);
                    break;
                }
            case MASTER_PROPERTY_BUILDING_STRUCTURE: {
                    $data = $this->masterPropertyBuildingStructureInterface->find($id);
                    break;
                }
            case MASTER_PROPERTY_CONTRACT_TYPE: {
                    $data = $this->masterPropertyContractTypeInterface->find($id);
                    break;
                }
            case MASTER_PROPERTY_CURRENT_SITUATION: {
                    $data = $this->masterPropertyCurrentSituationInterface->find($id);
                    break;
                }
            case MASTER_PROPERTY_TYPE: {
                    $data = $this->masterPropertyTypeInterface->find($id);
                    break;
                }
            case MASTER_PROVINCE: {
                    $data = $this->masterProvinceInterface->find($id);
                    break;
                }
            case MASTER_PURCHASE_PURPOSE: {
                    $data = $this->masterPurchasePurposesInterface->find($id);
                    break;
                }
            case MASTER_RAIL: {
                    $data =  $this->masterRailInterface->find($id);
                    break;
                }
            case MASTER_RESIDENCE_YEAR: {
                    $data =  $this->masterResidenceYearInterface->find($id);
                    break;
                }
            case MASTER_SALE_PURPOSE: {
                    $data =  $this->masterSalePurposeInterface->find($id);
                    break;
                }
            case MASTER_SCHEDULE_REPEAT: {
                    $data = $this->masterScheduleRepeatInterface->find($id);
                    break;
                }
            case MASTER_STATION: {
                    $data = $this->masterStationInterface->find($id);
                    break;
                }
            default:
                break;
        }
        return _success($data, __('message.show_success'), HTTP_SUCCESS);
    }
}

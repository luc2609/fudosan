<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRailRequest;
use App\Services\MasterDataService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    protected $masterDataService;

    public function __construct(MasterDataService $masterDataService)
    {
        $this->masterDataService = $masterDataService;
    }

    // Get address from postal code
    public function address($postalCode)
    {
        try {
            return $this->masterDataService->address($postalCode);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master property contract type
    public function indexPropertyContractType()
    {
        try {
            return $this->masterDataService->indexPropertyContractType();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master property current situation
    public function indexPropertyCurrentSituation()
    {
        try {
            return $this->masterDataService->indexPropertyCurrentSituation();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master property type
    public function indexPropertyType()
    {
        try {
            return $this->masterDataService->indexPropertyType();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master property building structure
    public function indexPropertyBuildingStructure()
    {
        try {
            return $this->masterDataService->indexPropertyBuildingStructure();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master advertising webs
    public function indexAdvertisingWeb()
    {
        try {
            return $this->masterDataService->indexAdvertisingWeb();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master province
    public function indexProvince()
    {
        try {
            return $this->masterDataService->indexProvince();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master district
    public function indexDistrict(Request $request)
    {
        try {
            $params = $request->all();

            return $this->masterDataService->indexDistrict($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master rail
    public function indexRail(IndexRailRequest $request)
    {
        try {
            $params = $request->all();

            return $this->masterDataService->indexRail($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master price
    public function indexPrice()
    {
        try {
            return $this->masterDataService->indexPrice();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master brokerage fee
    public function indexBrokerageFee()
    {
        try {
            return $this->masterDataService->indexBrokerageFee();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }


    // Get list master residence years
    public function indexResidenceYears()
    {
        try {
            return $this->masterDataService->indexResidenceYears();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master contact method
    public function indexContactMethods()
    {
        try {
            return $this->masterDataService->indexContactMethods();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master contact method
    public function indexPurchasePurposes()
    {
        try {
            return $this->masterDataService->indexPurchasePurposes();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master contact method
    public function indexScheduleRepeats()
    {
        try {
            return $this->masterDataService->indexScheduleRepeats();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master phase project
    public function indexPhaseProject()
    {
        try {
            return $this->masterDataService->indexPhaseProject();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get list master advertising forms
    public function indexAdvertisingForms()
    {
        try {
            return $this->masterDataService->indexAdvertisingForms();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get List Master Sale Purposes
    public function indexSalePurposes()
    {
        try {
            return $this->masterDataService->indexSalePurposes();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get List Master Notify Calendar
    public function indexNotifyCalendars()
    {
        try {
            return $this->masterDataService->indexNotifyCalendars();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get List Master Contact Reason
    public function indexContactReason()
    {
        try {
            return $this->masterDataService->indexContactReason();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Get List Master Field
    public function indexMasterField()
    {
        try {
            return $this->masterDataService->indexMasterField();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // delete Masterdata
    public function deleteMasterData(Request $request, $typeData, $id)
    {
        try {
            return $this->masterDataService->curdMasterData($request, $typeData, $id, DELETE_MASTER_DATA);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // edit MasterData
    public function editMasterData(Request $request, $typeData, $id)
    {
        try {
            return $this->masterDataService->curdMasterData($request, $typeData, $id, UPDATE_MASTER_DATA);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
    public function createMasterData(Request $request, $typeData)
    {
        try {
            return $this->masterDataService->curdMasterData($request, $typeData);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // showMasterData
    public function showMasterData($typeData, $id)
    {
        try {
            return $this->masterDataService->showMasterData($typeData, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // PostalCode
    public function indexPostalCode(Request $request)
    {
        try {
            return $this->masterDataService->indexPostalCode($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    // Master role
    public function indexRole()
    {
        try {
            return $this->masterDataService->indexRole();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

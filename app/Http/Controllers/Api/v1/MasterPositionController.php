<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\MasterPosition\MasterPositionEloquentRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterPositionController extends Controller
{
    protected $masterPositionService;

    public function __construct(MasterPositionEloquentRepository $masterPositionService)
    {
        $this->masterPositionService = $masterPositionService;
    }

    // List master position
    public function index()
    {
        try {
            $masterPositions = $this->masterPositionService->listMasterData();
            return _success($masterPositions, 'messsage.master_position_listed_succes', HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

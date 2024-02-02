<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DivisionRankingRequest;
use App\Http\Requests\RankingRequest;
use App\Http\Requests\UserRankingRequest;
use App\Services\RankingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RankingController extends Controller
{
    protected $rankingService;

    public function __construct(
        RankingService $rankingService
    ) {
        $this->rankingService = $rankingService;
    }

    public function index(RankingRequest $request, $typeRanking)
    {
        try {
            $params = $request->all();

            $data = $this->rankingService->index($params, $typeRanking);
            return _success($data, __('message.success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function indexDivision(DivisionRankingRequest $request)
    {
        try {
            $params = $request->all();

            return $this->rankingService->indexDivision($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function indexUser(UserRankingRequest $request)
    {
        try {
            $params = $request->all();

            return $this->rankingService->indexUser($params);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

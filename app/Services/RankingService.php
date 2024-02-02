<?php

namespace App\Services;

use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class RankingService
{
    protected $divisionInterface;
    protected $userInterface;
    protected $projectInterface;
    protected $notifyService;

    public function __construct(
        DivisionRepositoryInterface $divisionInterface,
        UserRepositoryInterface $userInterface,
        ProjectRepositoryInterface $projectInterface,
        NotifyService $notifyService
    ) {
        $this->divisionInterface = $divisionInterface;
        $this->userInterface = $userInterface;
        $this->projectInterface = $projectInterface;
        $this->notifyService = $notifyService;
    }

    /**
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexDivision($params)
    {
        $type = $params['type'] ?? '';
        $startDate = $params['start_date'] ?? '';
        $endDate = $params['end_date'] ?? '';
        $limit = $params['limit'] ?? '';
        $user = auth()->user();
        $companyId = $user->company;
        $checkCompany = $this->checkCompany();
        if ($checkCompany) {
            return $checkCompany;
        }

        if ($type == CONTRACT_RANKING_TYPE) {
            $divisions = $this->divisionInterface->indexContractRanking($params, $companyId, $startDate, $endDate, $limit, $user);
        } else if ($type == REVENUE_RANKING_TYPE) {
            $divisions = $this->divisionInterface->indexRevenueRanking($params, $companyId, $startDate, $endDate, $limit, $user);
        } else if ($type == BROKERAGE_RAKING_TYPE) {
            $divisions = $this->divisionInterface->indexBrokeRageRanking($params, $companyId, $startDate, $endDate, $limit, $user);
        }
        $overview = ["overview" => $this->index($params, TOTAL_RANKING_DIVISION)];
        $data = array_merge($divisions, $overview);
        return _success($data, __('message.show_success'), HTTP_SUCCESS);
    }

    /**
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUser($params)
    {
        $type = $params['type'] ?? '';
        $startDate = $params['start_date'] ?? '';
        $endDate = $params['end_date'] ?? '';
        $limit = $params['limit'] ?? '';
        $user = auth()->user();
        $companyId = $user->company;

        $checkCompany = $this->checkCompany();
        if ($checkCompany) {
            return $checkCompany;
        }

        if ($type == CONTRACT_RANKING_TYPE) {
            $users = $this->userInterface->indexContractRanking($params, $companyId, $startDate, $endDate, $limit, $user);
        } else if ($type == REVENUE_RANKING_TYPE) {
            $users = $this->userInterface->indexRevenueRanking($params, $companyId, $startDate, $endDate, $limit, $user);
        } else if ($type == BROKERAGE_RAKING_TYPE) {
            $users = $this->userInterface->indexBrokeRageRanking($params, $companyId, $startDate, $endDate, $limit, $user);
        }
        $overview = ["overview" => $this->index($params, TOTAL_RANKING_USER)];
        $data = array_merge($users, $overview);
        return _success($data, __('message.show_success'), HTTP_SUCCESS);
    }

    /**
     * @return false|\Illuminate\Http\JsonResponse
     */
    public function checkCompany()
    {
        $userId = auth()->user()->id;
        $user = $this->userInterface->find($userId);
        $company = $user->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }

        return false;
    }

    /**
     * @param $params
     * @param $typeRanking
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index($params, $typeRanking)
    {
        $user = auth()->user();
        $companyId = $user->company;

        $checkCompany = $this->checkCompany();
        if ($checkCompany) {
            return $checkCompany;
        }

        $rankingTotal = $this->projectInterface->rankingTotal($companyId, $params, $typeRanking);
        $contract_count = count($rankingTotal);
        $revenue_sum = 0;
        $brokerage_fee_sum = 0;
        if ($typeRanking == TOTAL_RANKING_USER) {
            if ($contract_count > 0) {
                foreach ($rankingTotal as $ranking) {
                    if (isset($ranking->projectUsers)) {
                        foreach ($ranking->projectUsers as $user) {
                            $revenue_sum += ($ranking->price * $user->commission_rate) / 100;
                            $brokerage_fee_sum += ($ranking->revenue * $user->commission_rate) / 100;
                        }
                    }
                }
            }
        } else if ($typeRanking == TOTAL_RANKING_DIVISION) {
            $contract_count = $rankingTotal[0]->total_project;
            $revenue_sum = $rankingTotal[0]->total_revenue;
            $brokerage_fee_sum = $rankingTotal[0]->total_brokerage_fee;
        }
        return [
            'contract_count' => $contract_count,
            'revenue_sum' => $revenue_sum,
            'brokerage_fee_sum' => $brokerage_fee_sum
        ];
    }
}

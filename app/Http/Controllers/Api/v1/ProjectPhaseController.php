<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProjectPhaseRequest;
use App\Services\ProjectPhaseService;
use App\Services\ProjectService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectPhaseController extends Controller
{
    protected $projectPhaseService;
    protected $projectService;

    public function __construct(
        ProjectPhaseService $projectPhaseService,
        ProjectService $projectService
    ) {
        $this->projectPhaseService = $projectPhaseService;
        $this->projectService = $projectService;
    }

    public function updatePhaseProject($projectId, $id)
    {
        $user = auth()->user();
        $userId = $user->id;
        $username = $user->username;
        DB::beginTransaction();
        try {
            $checkProject = $this->projectService->checkProject($projectId);
            $checkRoles = $this->projectService->checkRoleUpdate($projectId, $userId);
            if ($checkProject) {
                return $checkProject;
            } else if ($checkRoles) {
                return $checkRoles;
            }
            $projectPhase = $this->projectPhaseService->updatePhaseProject($projectId, $id, $userId, $username);
            DB::commit();
            return $projectPhase;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function showPhaseProject($id)
    {
        try {
            $projectPhase = $this->projectPhaseService->showPhaseProject($id);
            return $projectPhase;
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function getCountPhase()
    {
        try {
            return $this->projectPhaseService->countProjectPhase();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

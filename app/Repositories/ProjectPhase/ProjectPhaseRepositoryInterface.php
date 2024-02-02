<?php

namespace App\Repositories\ProjectPhase;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProjectPhaseRepositoryInterface extends BaseRepositoryInterface
{
    public function findByProjectId($projectId, $mPhaseProject);

    public function updateHistory($project, $currentPhaseId, $id, $projectPhaseId, $username, $userId, $isActionNoti, $createdAt);

    public function showPhaseProject($id);

    public function detailPhase($id, $projectId);

    public function mPhaseProject($id);

    public function countPhase($phase);
}

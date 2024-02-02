<?php

namespace App\Repositories\ProjectPhase;

use App\Models\Calendar;
use App\Models\MasterPhaseProject;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\Property;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class ProjectPhaseEloquentRepository extends BaseEloquentRepository implements ProjectPhaseRepositoryInterface
{
    public function getModel()
    {
        return ProjectPhase::class;
    }

    public function findByProjectId($projectId, $mPhaseProject)
    {
        return $this->_model
            ->where('project_id', $projectId)
            ->where('m_phase_project_id', $mPhaseProject)
            ->first();
    }

    public function updateHistory($project,  $currentPhaseId, $id, $projectPhaseId, $username, $userId, $isActionNoti, $createdAt)
    {

        $mPhaseId = $this->find($currentPhaseId)->m_phase_project_id;
        $oldLabel = $this->mPhaseProject($mPhaseId)->name;
        $newLabel = $this->mPhaseProject($id)->name;
        $data = [
            'old_phase' =>   $mPhaseId,
            'old_phase_name' => $oldLabel,
            'new_phase' => (int)$id,
            'new_phase_name' =>  $newLabel,
            'user_updated' => $username,
            'user_updated_id' => $userId,
            'created_at' => $createdAt
        ];
        if ($project->history) {
            $arrayJsonHistory = $project->history;
            $arrayJsonHistoryEncode = json_encode($data);
            $arrayJsonHistory[] = json_decode($arrayJsonHistoryEncode);
            $history = json_encode($arrayJsonHistory);
        } else {
            $arrayJsonHistory = [$data];
            $history =  json_encode($arrayJsonHistory);
        }
        $project->update([
            'current_phase_id' => $projectPhaseId,
            'history' => $history,
            'is_action_noti' => $isActionNoti
        ]);
    }

    public function showPhaseProject($id)
    {
        return Calendar::where('calendars.project_id', $id)
            ->leftJoin('project_phases', 'project_phases.id', 'calendars.project_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->leftJoin('calendar_users', function ($join) {
                $join->on('calendar_users.calendar_id', 'calendars.id')
                    ->where('calendar_users.is_host', '=', IS_HOST);
            })->leftJoin('users', 'users.id', 'calendar_users.user_id')
            ->select(
                'calendars.id AS calendar_id',
                'calendars.meeting_start_time',
                'calendars.meeting_end_time',
                'calendars.title',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as creator'),
                'project_phases.m_phase_project_id',
                'master_phase_projects.name as master_phase_projects'
            )
            ->orderBy('m_phase_project_id')->get();
    }

    public function detailPhase($id, $projectId)
    {
        return Calendar::where([
            'calendars.project_id' =>  $projectId,
            'calendars.project_phase_id' => $id
        ])
            ->leftJoin('project_phases', 'project_phases.id', 'calendars.project_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->leftJoin('calendar_users', 'calendar_users.calendar_id', 'calendars.id')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'calendar_users.user_id')
                    ->where('calendar_users.is_host', '=', IS_HOST);
            })
            ->select(
                'calendars.id AS calendar_id',
                'calendars.meeting_start_time',
                'calendars.meeting_end_time',
                'calendars.title',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as creator'),
                'project_phases.m_phase_project_id',
                'master_phase_projects.name as master_phase_projects'
            )
            ->first();
    }

    public function mPhaseProject($id)
    {
        return $this->_model->join('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->where('master_phase_projects.id', $id)->first();
    }

    public function countPhase($phase)
    {
        if (!$phase) {
            return Project::count();
        }

        return Project::join('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->where('project_phases.m_phase_project_id', $phase)
            ->count();
    }
}

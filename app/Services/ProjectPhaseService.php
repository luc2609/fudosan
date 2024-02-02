<?php

namespace App\Services;

use App\Repositories\Calendar\CalendarRepositoryInterface;
use App\Repositories\CalendarUser\CalendarUserRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectPhase\ProjectPhaseRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProjectPhaseService
{
    protected $projectPhaseInterface;
    protected $calendarInterface;
    protected $projectInterface;
    protected $calendarService;
    protected $userInterface;
    protected $calendarUserInterface;
    protected $notifyService;
    protected $mailService;

    public function __construct(
        ProjectPhaseRepositoryInterface $projectPhaseInterface,
        CalendarRepositoryInterface $calendarInterface,
        ProjectRepositoryInterface $projectInterface,
        CalendarUserRepositoryInterface $calendarUserInterface,
        CalendarService $calendarService,
        UserRepositoryInterface $userInterface,
        NotifyService $notifyService,
        MailService $mailService
    ) {
        $this->projectPhaseInterface = $projectPhaseInterface;
        $this->calendarInterface  = $calendarInterface;
        $this->projectInterface = $projectInterface;
        $this->calendarUserInterface = $calendarUserInterface;
        $this->calendarService = $calendarService;
        $this->userInterface = $userInterface;
        $this->notifyService = $notifyService;
        $this->mailService = $mailService;
    }

    public function showPhaseProject($id)
    {
        $currentPhaseId = $this->projectInterface->find($id)->current_phase_id;

        $data = [
            'project_phases' => $this->projectPhaseInterface->showPhaseProject($id),
            'current_phase' => $this->projectPhaseInterface->find($currentPhaseId)->m_phase_project_id
        ];
        return _success($data, __('message.get_success'), HTTP_SUCCESS);
    }

    public function updatePhaseProject($projectId, $id, $userId, $username)
    {
        $newTime = now();
        // $startTime = $newTime->toDateTimeString();
        $calendar = $this->calendarInterface->findCalendarProject($projectId, $id);
        $projectPhase = $this->projectPhaseInterface->findByProjectId($projectId, $id);
        $projectPhaseId = $projectPhase->id;
        $this->projectInterface->createTitleProject($projectId, $projectPhaseId);
        $project = $this->projectInterface->find($projectId);
        if ($project->close_status == REQUEST_CLOSE || $project->close_status == SUCCESS_CLOSE) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        $currentPhaseId = $project->current_phase_id;
        $this->projectPhaseInterface->updateHistory($project, $currentPhaseId, $id, $projectPhaseId, $username, $userId, IS_ACTION_NOTI, $newTime);
        $title = $project->title;
        $content = $this->createContentNoti($title, $currentPhaseId, $id);
        $checkDuplicateTime = $this->calendarService->checkDuplicateTime(null, null, $projectId);
        if (!$calendar) {
            $attributes = [
                'title' => $title,
                'meeting_type' => CALENDAR_PROJECT,
                'is_public' => PUBLIC_STATUS,
                'meeting_start_time' => $newTime,
                'meeting_end_time' => $newTime,
                'project_id' => $projectId,
                'project_phase_id' => $id,
                'repeat_id' => NOT_REPEAT,
                'company_id' => $project->company_id,
                'division_id' => $project->division_id,
                'notify_id' => NO_NOTI,
                'start_date' => $newTime,
                'end_date' => $newTime,
            ];
            if ($checkDuplicateTime) {
                return $checkDuplicateTime;
            }
            $calendarProject = $this->calendarInterface->create($attributes);
            $calendarId = $calendarProject->id;
            $params = [
                'user_id' => $userId,
                'calendar_id' => $calendarId,
                'is_accept' => IS_ACCEPT,
                'is_host' => true,
                'notify_id' => NO_NOTI
            ];
            $this->calendarUserInterface->create($params);
        } else {
            // $calendar->meeting_start_time = $startTime;
            // $calendar->start_date = $startTime;
            $calendar->meeting_end_time = $newTime;
            $calendar->end_date = $newTime;
            if ($checkDuplicateTime) {
                return $checkDuplicateTime;
            }
            $calendar->save();
        }
        $data = $this->projectPhaseInterface->detailPhase($id, $projectId);

        // send mail only phase 5
        if ($id == PHASE_FIVE) {
            $this->mailService->sendEmail(
                env('SYSTEM_MAIL'),
                ['title' => $data->title],
                __('text.transfer_phase_5_project'),
                'mail.transfer_phase_5_project'
            );
        }
        // Push notification
        if ($userId) {
            $deviceTokenUsers = $this->userInterface->listDeviceToken(array($userId));
            try {
                $label = __('message.label');
                $this->notifyService->pushNotify($deviceTokenUsers, $label, $content);
            } catch (\Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            }
        }
        return _success($data, __('message.get_success'), HTTP_SUCCESS);
    }

    public function createContentNoti($title, $currentPhaseId, $id)
    {
        $mPhaseId = $this->projectPhaseInterface->find($currentPhaseId)->m_phase_project_id;
        $oldLabel = $this->projectPhaseInterface->mPhaseProject($mPhaseId)->name;
        $currentLabel = $this->projectPhaseInterface->mPhaseProject($id)->name;
        return  $title . ': ' . $oldLabel . 'から' . $currentLabel . ' を変更しました';
    }

    // Count number of project's phases
    public function countProjectPhase()
    {
        $data = [
            'project_total' => $this->projectPhaseInterface->countPhase(null)
        ];
        foreach (range(1, 9) as $phase) {
            $data['phase_' . $phase] = $this->projectPhaseInterface->countPhase($phase);
        }
        return _success($data, __('message.show_info_success'), HTTP_SUCCESS);
    }
}

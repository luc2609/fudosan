<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\MasterPhaseProject;
use App\Models\ProjectCustomer;
use App\Repositories\Calendar\CalendarRepositoryInterface;
use App\Repositories\CalendarFile\CalendarFileRepositoryInterface;
use App\Repositories\CalendarUser\CalendarUserRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectPhase\ProjectPhaseRepositoryInterface;
use App\Repositories\Property\PropertyRepositoryInterface;
use App\Repositories\SubCalendar\SubCalendarRepositoryInterface;
use App\Repositories\SubCalendarFile\SubCalendarFileRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CalendarService
{
    protected $calendarInterface;
    protected $calendarFileInterface;
    protected $fileService;
    protected $userInterface;
    protected $propertyInterface;
    protected $projectInterface;
    protected $customerInterface;
    protected $divisionInterface;
    protected $projectPhaseInterface;
    protected $calendarUserInterface;
    protected $subCalendarInterface;
    protected $notifyService;
    protected $subCalendarFileInterface;

    public function __construct(
        CalendarRepositoryInterface $calendarInterface,
        CalendarFileRepositoryInterface $calendarFileInterface,
        FileService $fileService,
        UserRepositoryInterface $userInterface,
        PropertyRepositoryInterface $propertyInterface,
        ProjectRepositoryInterface $projectInterface,
        CustomerRepositoryInterface $customerInterface,
        DivisionRepositoryInterface $divisionInterface,
        ProjectPhaseRepositoryInterface $projectPhaseInterface,
        CalendarUserRepositoryInterface $calendarUserInterface,
        SubCalendarRepositoryInterface $subCalendarInterface,
        NotifyService $notifyService,
        SubCalendarFileRepositoryInterface $subCalendarFileInterface
    ) {
        $this->calendarInterface = $calendarInterface;
        $this->fileService = $fileService;
        $this->calendarFileInterface = $calendarFileInterface;
        $this->userInterface = $userInterface;
        $this->propertyInterface = $propertyInterface;
        $this->projectInterface = $projectInterface;
        $this->customerInterface = $customerInterface;
        $this->divisionInterface = $divisionInterface;
        $this->projectPhaseInterface = $projectPhaseInterface;
        $this->calendarUserInterface = $calendarUserInterface;
        $this->subCalendarInterface = $subCalendarInterface;
        $this->notifyService = $notifyService;
        $this->subCalendarFileInterface = $subCalendarFileInterface;
    }

    // Create calendar
    public function create($request)
    {
        $authUser = auth()->user();
        $companyId = $authUser->company;
        $startDateTime = date('H:i:s', strtotime($request->meeting_start_time));
        $startDate = Carbon::parse($request->meeting_start_time)->format('Y-m-d') . ' ' . $startDateTime;
        $endDate =  Carbon::parse($request->meeting_end_time)->addYear(1)->format('Y-m-d') . ' ' . $startDateTime;

        if ($request->project_id && $request->project_phase_id) {
            $projectId =  $request->project_id;
            $mProjectPhaseId =  $request->project_phase_id;
            $project = $this->projectInterface->find($projectId);
            $divisionId = $project->division_id;
            $phaseName = MasterPhaseProject::find($mProjectPhaseId)->name;
            $customerIds = ProjectCustomer::where('project_id', $projectId)->first();
            $customer =  Customer::find($customerIds['customer_id']);
            $title = '【' . $phaseName . '】' . ' ' . $customer->last_name . ' ' . $customer->first_name . 'さま ';
        } else {
            $divisionId = $request->division_id;
            $title =  $request->title;
        }
        $attributes = [
            'title' =>   $title,
            'meeting_start_time' =>  $request->meeting_start_time,
            'meeting_end_time' =>  $request->meeting_end_time,
            'project_id' => $request->project_id,
            'meeting_url' => $request->meeting_url,
            'project_phase_id' => $request->project_phase_id,
            'note' => $request->note,
            'repeat_id' => $request->repeat_id,
            'meeting_type' => $request->meeting_type,
            'is_public' => $request->is_public,
            'company_id' => $companyId,
            'division_id' =>  $divisionId,
            'repeat_day' => $request->repeat_day,
            'notify_id' => $request->notify_id
        ];
        if ($request->repeat_id != NOT_REPEAT) {
            $attributes['start_date'] = $startDate;
            $attributes['end_date'] = $endDate;
        } else {
            $attributes['start_date'] = $request->meeting_start_time;
            $attributes['end_date'] = $request->meeting_end_time;
        }
        $calendar = $this->calendarInterface->create($attributes);

        $meetingStartTime = Carbon::parse($request->meeting_start_time);
        $meetingEndTime = Carbon::parse($request->meeting_end_time);
        $diffSeconds = $meetingStartTime->diffInSeconds($meetingEndTime);
        if ($request->repeat_id == REPEAT_MONTH) {
            $startDateTime = date('H:i:s', strtotime($request->meeting_start_time));
            $repeatDayCalendars = [
                'meeting_start_time' => $request->meeting_start_time,
                'meeting_end_time' => $request->meeting_end_time
            ];
            $repeatDayCalendars1 = [];
            for ($i = 1; $i < 12; $i++) {
                $startDateOfMonth = $meetingStartTime->copy()->addMonthNoOverflow($i)->format('Y-m-d') . ' ' . $startDateTime;
                $repeatDayCalendars1[] = [
                    'meeting_start_time' => $startDateOfMonth,
                    'meeting_end_time' => Carbon::parse($startDateOfMonth)->addSeconds($diffSeconds)->format('Y-m-d H:i:s'),
                ];
            }
            // array calendar repeat
            $this->paramCalendars($repeatDayCalendars, $repeatDayCalendars1, $request, $calendar);
        }
        $this->createRelateUser($calendar->id, $request, $authUser, null, $request->meeting_start_time);


        $calendarId = $calendar->id;

        // documents
        if (isset($request->documents)) {
            $files = $request->documents;
            $filePath = 'calendar/' . $calendarId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->calendarFileInterface->create([
                    'calendar_id' => $calendarId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                ]);
            }
        }
        // Push noti
        if (isset($request->relate_user_ids)) {
            $userIds = $request->relate_user_ids;
            $deviceTokenUsers = $this->userInterface->listDeviceToken($userIds);
            try {
                $this->notifyService->pushNotify($deviceTokenUsers, $request->title, $request->meeting_start_time . $request->meeting_end_time);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify create calendar: ' . $e->getMessage());
            }
        }
        return
            $this->calendarInterface->showCalendar($calendarId);
    }

    // Update Calendar
    public function update($request, $id, $calendar, $isHost)
    {
        $startDateTime = date('H:i:s', strtotime($request->meeting_start_time));
        $startDate = Carbon::parse($request->meeting_start_time)->format('Y-m-d') . ' ' . $startDateTime;
        $endDate =  Carbon::parse($startDate)->addYear(1)->format('Y-m-d') . ' ' . $startDateTime;
        if ($calendar->project_id) {
            $title = $calendar->title;
        } else {
            $title = $request->title;
        }
        $attributes = [
            'title' => $title,
            'meeting_start_time' => $request->meeting_start_time,
            'meeting_end_time' =>  $request->meeting_end_time,
            'project_id' => $calendar->project_id,
            'meeting_url' => $request->meeting_url,
            'project_phase_id' => $calendar->project_phase_id,
            'note' => $request->note,
            'meeting_type' => $calendar->meeting_type,
            'is_public' => $calendar->is_public,
            'division_id' => $calendar->division_id,
            'notify_id' => $request->notify_id
        ];
        // delete documents
        if (isset($request->delete_document_ids)) {
            $deleteDocumentIds = $request->delete_document_ids;
            foreach ($deleteDocumentIds as $fileId) {
                $file = $this->calendarFileInterface->find($fileId);

                if ($file) {
                    $this->fileService->deleteFileS3($file->url);
                    $this->calendarFileInterface->delete($fileId);
                }
            }
        }

        // documents
        if (isset($request->documents)) {
            $files = $request->documents;
            $filePath = 'calendar/' . $id . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->calendarFileInterface->create([
                    'calendar_id' => $id,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                ]);
            }
        }

        $notifyId = $request->notify_id;
        $time = new Carbon($request->meeting_start_time);
        switch ($notifyId) {
            case NOTI_CALENDAR_5P:
                $startTimeMeeting = $time->addMinutes(-5);
                break;
            case NOTI_CALENDAR_15P:
                $startTimeMeeting =  $time->addMinutes(-15);
                break;
            case NOTI_CALENDAR_30P:
                $startTimeMeeting =  $time->addMinutes(-30);
                break;
            case NOTI_CALENDAR_60p:
                $startTimeMeeting =  $time->addMinutes(-60);
                break;
            case NOTI_CALENDAR_1_DATE:
                $startTimeMeeting = $time->addDays(-1);
                break;
            default:
                $startTimeMeeting = null;
                break;
        }

        if ($startTimeMeeting) {
            $startTimeMeeting = $startTimeMeeting->format('Y-m-d H:i:s');
        }
        // relate user
        if (isset($request->relate_user_ids)) {
            $currentRelateUserIds =  $calendar->users()->pluck('users.id')->toArray();
            $newRelateUserIds =  $request->relate_user_ids;

            $addRelateUserIds = array_diff($newRelateUserIds, $currentRelateUserIds);
            $deleteRelateUserIds = array_diff($currentRelateUserIds, $newRelateUserIds);

            if (count($deleteRelateUserIds)) {
                $calendar->users()->detach($deleteRelateUserIds);
            }

            if (is_array($request->relate_user_ids)) {
                $calendar->users()->attach(
                    $addRelateUserIds,
                    [
                        'notify_id' => $notifyId,
                        'start_time_meeting' => $startTimeMeeting,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        } else {
            $calendar->users()->detach();
        }
        $calendar->users()->attach(
            $isHost,
            [
                'notify_id' => $notifyId,
                'is_host' => true,
                'is_accept' => IS_ACCEPT,
                'start_time_meeting' => $startTimeMeeting
            ]
        );
        foreach ($calendar->calendarUserNows as $userCalendar) {
            $this->calendarUserInterface->update($userCalendar['id'], [
                'notify_id' => $notifyId,
                'start_time_meeting' => $startTimeMeeting
            ]);
        }

        if ($calendar->repeat_id != NOT_REPEAT) {
            $attributes['start_date'] = $startDate;
            $attributes['end_date'] = $endDate;
        } else {
            $attributes['start_date'] = $request->meeting_start_time;
            $attributes['end_date'] = $request->meeting_end_time;
        }
        $this->calendarInterface->update($id, $attributes);

        // Push noti
        if (isset($request->relate_user_ids)) {
            $calendarUsers = $this->calendarUserInterface->getUserOfCalendar([$id]);
            $calendarUserIds = [];
            foreach ($calendarUsers as $calendarUser) {
                if ($calendarUser['is_accept'] != REJECT && $calendarUser['is_host'] != IS_HOST) {
                    $calendarUserIds[] = $calendarUser['user_id'];
                }
            }
            $deviceTokenUsers = $this->userInterface->listDeviceToken($calendarUserIds);
            try {
                $this->notifyService->pushNotify($deviceTokenUsers, $request->title, $request->meeting_start_time . $request->meeting_end_time);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update calendar: ' . $e->getMessage());
            }
        }
    }

    // Update Calendar
    public function updateSubCalendar($request, $subCalendar, $subCalendarId, $modifyDateCalendar, $id)
    {

        $attributes = [
            'title' => $request->title,
            'meeting_start_time' => $request->meeting_start_time,
            'meeting_end_time' =>  $request->meeting_end_time,
            'modify_date' => $modifyDateCalendar,
            'meeting_url' => $request->meeting_url,
            'calendar_id' => $id,
            'note' => $request->note,
            'notify_id' => $request->notify_id
        ];
        // delete documents
        if (isset($request->delete_document_ids)) {
            $deleteDocumentIds = $request->delete_document_ids;
            foreach ($deleteDocumentIds as $fileId) {
                $file = $this->subCalendarFileInterface->find($fileId);

                if ($file) {
                    $this->fileService->deleteFileS3($file->url);
                    $this->subCalendarFileInterface->delete($fileId);
                }
            }
        }

        // documents
        if (isset($request->documents)) {
            $files = $request->documents;
            $filePath = 'subCalendar/' . $subCalendarId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->subCalendarFileInterface->create([
                    'sub_calendar_id' => $subCalendarId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                ]);
            }
        }
        $notifyId = $request->notify_id;
        $time = new Carbon($request->meeting_start_time);
        switch ($notifyId) {
            case NOTI_CALENDAR_5P:
                $startTimeMeeting = $time->addMinutes(-5);
                break;
            case NOTI_CALENDAR_15P:
                $startTimeMeeting =  $time->addMinutes(-15);
                break;
            case NOTI_CALENDAR_30P:
                $startTimeMeeting =  $time->addMinutes(-30);
                break;
            case NOTI_CALENDAR_60p:
                $startTimeMeeting =  $time->addMinutes(-60);
                break;
            case NOTI_CALENDAR_1_DATE:
                $startTimeMeeting = $time->addDays(-1);
                break;
            default:
                $startTimeMeeting = null;
                break;
        }

        if ($startTimeMeeting) {
            $startTimeMeeting = $startTimeMeeting->format('Y-m-d H:i:s');
        }
        // relate user
        if (isset($request->relate_user_ids)) {
            $currentRelateUserIds =  $subCalendar->users()->pluck('users.id')->toArray();
            $newRelateUserIds =  $request->relate_user_ids;

            $addRelateUserIds = array_diff($newRelateUserIds, $currentRelateUserIds);
            $deleteRelateUserIds = array_diff($currentRelateUserIds, $newRelateUserIds);

            if (count($deleteRelateUserIds)) {
                $subCalendar->users()->detach($deleteRelateUserIds);
            }
            if (is_array($request->relate_user_ids)) {
                $subCalendar->users()->attach(
                    $addRelateUserIds,
                    [
                        'calendar_id' => $id,
                        'notify_id' => $notifyId,
                        'start_time_meeting' => $startTimeMeeting,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        } else {
            $subCalendar->users()->detach();
        }
        $subCalendar->users()->attach(
            auth()->user()->id,
            [
                'calendar_id' => $id,
                'notify_id' => $notifyId,
                'is_host' => true,
                'is_accept' => IS_ACCEPT,
                'start_time_meeting' => $startTimeMeeting
            ]
        );
        $this->subCalendarInterface->update($subCalendarId, $attributes);

        // Push noti
        if (isset($request->relate_user_ids)) {
            $calendarUsers = $this->calendarUserInterface->getUserOfSubCalendar($subCalendarId);
            $calendarUserIds = [];
            foreach ($calendarUsers as $calendarUser) {
                if ($calendarUser['is_accept'] != REJECT && $calendarUser['is_host'] != IS_HOST) {
                    $calendarUserIds[] = $calendarUser['user_id'];
                }
            }
            $deviceTokenUsers = $this->userInterface->listDeviceToken($calendarUserIds);
            try {
                $this->notifyService->pushNotify($deviceTokenUsers, $request->title, $request->meeting_start_time . $request->meeting_end_time);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
            }
        }
    }

    public function checkParamsCalendar($request, $id, $userId)
    {
        $user = $this->userInterface->find($userId);
        $company = $user->company()->first();

        if (!$company) {
            Log::warning(__METHOD__ . ' - ' . __LINE__ . ' : Check params create calendar. Error because ' . $company . ' not exists!');
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }
        $companyId = $company->id;

        // check project
        if (isset($request->project_id)) {
            $projectId = $request->project_id;
            $project = $this->projectInterface->find($projectId);

            if ($project->company_id != $companyId) {
                Log::warning(__METHOD__ . ' - ' . __LINE__ . ' : Check params create calendar. Error because project_id= ' . $projectId . ' not belongs to' . $company);
                return _error(null, __('message.project_not_correct'), HTTP_BAD_REQUEST);
            }
        }

        // check division
        if (isset($request->division_id)) {
            $divisionId = $request->division_id;
            $division = $this->divisionInterface->find($divisionId);
            if ($division->company_id != $companyId) {
                Log::warning(__METHOD__ . ' - ' . __LINE__ . ' : Check params create calendar. Error because division_id= ' . $divisionId . ' not belongs to ' . $company);
                return _error(null, __('message.division_not_correct'), HTTP_BAD_REQUEST);
            }
        }

        // check relate_user_ids
        $relateUserIds = $request->relate_user_ids ?? [];
        foreach ($relateUserIds as $relateUserId) {
            $relateUser = $this->userInterface->find($relateUserId);
            if ($relateUser->company != $companyId) {
                Log::warning(__METHOD__ . ' - ' . __LINE__ . ' : Check params create calendar. Error because user_id= ' . $relateUser . ' not belongs to ' . $company);
                return _error(null, __('message.relate_user_id_not_correct'), HTTP_BAD_REQUEST);
            }
        }

        if ($id) {
            $calendar = $this->calendarInterface->find($id);
            // Check document count
            $documentsCurrentCount = count($calendar->documents);
            $documentsDeleteCount = 0;

            if (isset($request->delete_document_ids)) {
                $deleteDocumentIds = $request->delete_document_ids;

                foreach ($deleteDocumentIds as $deleteDocumentId) {
                    if (!$calendar->documents->contains('id', $deleteDocumentId)) {
                        Log::warning(__METHOD__ . ' - ' . __LINE__ . ' Error because document_Id= ' . $deleteDocumentId . ' not exists!');
                        return _error(null, __('message.delete_document_id_not_correct'), HTTP_BAD_REQUEST);
                    }
                }

                $documentsDeleteCount = count($deleteDocumentIds);
            }

            if (isset($request->documents)) {
                $documentsAddCount = count($request->documents);

                if ($documentsCurrentCount - $documentsDeleteCount + $documentsAddCount > 5) {
                    Log::warning(__METHOD__ . ' - ' . __LINE__ . ' Error because document quality > 5 file!');
                    return _error('false', __('message.document_over_limited'), HTTP_BAD_REQUEST);
                }
            }
        }

        return false;
    }

    public function checkCalendar($calendar, $userId)
    {

        $user = $this->userInterface->find($userId);

        $company = $user->company()->first();
        if (!$calendar) {
            Log::warning(__METHOD__ . ' - ' . __LINE__ . ' Error because not found ' . $calendar->id);
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        }
        if ($calendar->company_id != $company->id) {
            Log::warning(__METHOD__ . ' - ' . __LINE__ . ' Error because calendar_id ' . $calendar->id . ' not belongs to company_id' . $company->name);
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        return false;
    }

    public function checkRoles($id, $userId)
    {
        $user = $this->userInterface->find($userId);
        $calendarUser = $this->userInterface->find($id);
        if ($user->hasRole(MANAGER_ROLE)) {
            if (!($calendarUser->hasRole(USER_ROLE))) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }
            if (!($user->divisions->contains('id', $calendarUser->division))) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }
        }
        if ($user->hasRole(USER_ROLE)) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        return false;
    }

    public function checkRoleShow($calendar, $user)
    {
        $userId = $user->id;
        if (
            $user->hasRole(ADMIN_CMS_COMPANY_ROLE) ||
            $calendar->is_public == PUBLIC_STATUS ||
            $calendar->users->contains('id', $userId)
        ) {
            $check = false;
        } else if ($user->hasRole(MANAGER_ROLE)) {
            $check = _error(null, __('message.no_permission'), HTTP_FORBIDDEN);

            foreach ($calendar->users as $calendarUser) {
                if (!$calendarUser->hasRole(USER_ROLE)) {
                    continue;
                } else if ($user->divisions->contains('id', $calendarUser->division)) {
                    $check = false;
                    break;
                }
            }
        } else {
            $check = _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }

        return $check;
    }
    // check role sub calendar
    public function checkRoleShowSubCalendar($calendar, $subCalendar, $user)
    {
        $userId = $user->id;
        if (
            $user->hasRole(ADMIN_CMS_COMPANY_ROLE) ||
            $calendar->is_public == PUBLIC_STATUS ||
            $subCalendar->users->contains('id', $userId)
        ) {
            $check = false;
        } else if ($user->hasRole(MANAGER_ROLE)) {
            $check = _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            foreach ($subCalendar->users as $calendarUser) {
                if (!$calendarUser->hasRole(USER_ROLE)) {
                    continue;
                } else if ($user->divisions->contains('id', $calendarUser->division)) {
                    $check = false;
                    break;
                }
            }
        } else {
            $check = _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }

        return $check;
    }

    // Check exist calendar to phase project
    public function checkDuplicate($request, $calendarId)
    {
        $user = auth()->user();
        $companyId = $user->company;
        if (isset($request->project_id) && isset($request->project_phase_id)) {
            $attributesCheckDuplicate = [
                'project_id' => $request->project_id,
                'project_phase_id' => $request->project_phase_id,
                'company_id' => $companyId,
            ];
            $listCalendarProjectCheckDuplicate =  $this->calendarInterface->getByAttributes($attributesCheckDuplicate);

            if ($listCalendarProjectCheckDuplicate->count() > 0) {
                if ($listCalendarProjectCheckDuplicate[0]->id == $calendarId) {
                    return false;
                }

                $data = [
                    'id' =>  $listCalendarProjectCheckDuplicate[0]->id
                ];
                Log::warning(__METHOD__ . ' - ' . __LINE__ . ' Error because The calendar phase_id= ' . $request->project_phase_id . 'of project_id= ' . $request->project_id . 'already exist!');
                return _error($data, __('message.project_phase_of_calendar_exist'), HTTP_SUCCESS);
            }
        }

        return false;
    }

    // Check duplicate time calendar of project
    public function checkDuplicateTime($request, $calendarId, $projectId)
    {
        if (isset($request->project_id)) {
            $projectId = $request->project_id;
        }

        if ($calendarId) {
            $projectId = $this->calendarInterface->find($calendarId)->project_id;
        }

        if (isset($request->meeting_start_time)) {
            $meetingStartTime = $request->meeting_start_time;
        } else {
            $meetingStartTime = Carbon::now()->toDateTimeString();
        }

        if ($projectId) {
            $calendars = $this->calendarInterface->findProject($projectId, $calendarId);
            for ($i = 0; $i < $calendars->count(); $i++) {
                if ($calendars[$i]->meeting_start_time ==  $meetingStartTime) {
                    return _error(null, __('message.duplicate_time_other_phase'), HTTP_SUCCESS);
                }
            }
        }
        return false;
    }
    // delete calendar
    public function delete($request, $id)
    {
        $userId = auth()->user()->id;
        $calendar = $this->calendarInterface->find($id);
        $isHost = $this->calendarInterface->findHostCalendar($id)->user_id;
        $checkCalendar = $this->checkCalendar($calendar, $userId);
        $modifyDateCalendar =  Carbon::parse($request->modify_date);

        if ($checkCalendar) {
            return $checkCalendar;
        }

        if ($isHost != $userId) {
            Log::warning(__METHOD__ . ' - ' . __LINE__ . ' Error because user_id= ' . $userId .  'are not the creator of the calendar!');
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        if ($calendar->repeat_id == NOT_REPEAT) {
            foreach ($calendar->documents as $file) {
                if ($file) {
                    $this->fileService->deleteFileS3($file->url);
                }
            }
            $calendar->users()->detach();
            $calendar->documents()->delete();
            $calendar->delete();
        } else {
            if ($request->choice == CALENDAR_ONE) {
                if ($calendar->repeat_id == REPEAT_MONTH) {
                    $subCalendar =  $this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id);
                    $subCalendar->users()->detach();
                    $subCalendar->delete();
                } else {
                    if ($this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id)) {
                        $subCalendar =  $this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id);
                        $subCalendarId = $subCalendar->id;
                        $attributes['is_deleted'] = true;
                        $this->subCalendarInterface->update($subCalendarId, $attributes);
                        $subCalendar->users()->detach();
                        $subCalendar->documents()->delete();
                    } else {
                        $params = [
                            'title' => $calendar->title,
                            'meeting_start_time' => $calendar->meeting_start_time,
                            'meeting_end_time' =>  $calendar->meeting_end_time,
                            'modify_date' => $modifyDateCalendar,
                            'meeting_url' => $request->meeting_url,
                            'calendar_id' => $id,
                            'is_deleted' => true,
                        ];
                        $this->subCalendarInterface->insert($params);
                    }
                }
            }

            if ($request->choice == CALENDAR_ALL) {
                foreach ($calendar->documents as $file) {
                    if ($file) {
                        $this->fileService->deleteFileS3($file->url);
                    }
                }
                $deleteCalendar = $this->subCalendarInterface->findAllCalendarMonth($id);
                $deleteCalendar->each->delete();
                $calendar->users()->detach();
                $calendar->documents()->delete();
                $calendar->delete();
            }
        }
        return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
    }

    // update calendar
    public function updateCalendar($request, $id)
    {
        $user = auth()->user();
        $userId = $user->id;
        $calendar = $this->calendarInterface->find($id);
        $isHost = $this->calendarInterface->findHostCalendar($id)->user_id;
        $checkCalendar = $this->checkCalendar($calendar, $userId);
        $modifyDateCalendar =  Carbon::parse($request->modify_date);
        if ($checkCalendar) {
            return $checkCalendar;
        }
        if ($isHost != $userId) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }
        if ($calendar->repeat_id == NOT_REPEAT) {
            $this->update($request, $id, $calendar, $isHost);
            $showCalendar = $this->calendarInterface->showCalendar($id);
        } else {
            if ($request->choice == CALENDAR_ONE) {
                if (
                    ($calendar->repeat_id == REPEAT_MONTH) || (($calendar->repeat_id == REPEAT_DAY || $calendar->repeat_id == REPEAT_WEEK) && ($this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id)))
                ) {
                    $subCalendar =  $this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id);
                    $subCalendarId = $subCalendar->id;
                    $this->updateSubCalendar($request, $subCalendar, $subCalendarId, $modifyDateCalendar, $id);
                    $showCalendar = $this->subCalendarInterface->show($subCalendarId);
                } else {
                    $newSubCalendarId = $this->createSubCalendars($request, $modifyDateCalendar, $calendar);
                    $showCalendar = $this->subCalendarInterface->show($newSubCalendarId);
                }
            }
            if ($request->choice == CALENDAR_ALL) {
                $meetingStartTime = Carbon::parse($request->meeting_start_time);
                $meetingEndTime = Carbon::parse($request->meeting_end_time);
                $diffSeconds = $meetingStartTime->diffInSeconds($meetingEndTime);
                $subCalendars = $this->subCalendarInterface->findAllCalendarMonth($id);

                if ($calendar->repeat_id == REPEAT_MONTH) {
                    $this->update($request, $id, $calendar, $isHost);
                    for ($i = 0; $i < $subCalendars->count(); $i++) {
                        $subCalendarId = $subCalendars[$i]->id;
                        $startDateTime = date('H:i:s', strtotime($request->start_date));
                        $startDate =  Carbon::parse($meetingStartTime)->copy()->addMonthsNoOverflow($i)->format('Y-m-d') . ' ' . $startDateTime;
                        $endDate = Carbon::parse($startDate)->addSeconds($diffSeconds)->format('Y-m-d H:i:s');
                        $this->paramSubCalendars($request, $startDate, $endDate, $subCalendarId, $id);
                    }
                } else {
                    foreach ($subCalendars as $subCalendar) {
                        $subCalendarId = $subCalendar->id;
                        $subCalendar = $this->subCalendarInterface->find($subCalendarId);
                        $subCalendar->delete();
                        $subCalendar->users()->detach();
                        $subCalendar->documents()->delete();
                    }
                    $this->update($request, $id, $calendar, $isHost);
                }
                $showCalendar = $this->calendarInterface->showCalendar($id);
            }
        }
        $data = [
            'calendar' => $showCalendar,
            'calendar_conflict' => $this->calendarInterface->findCalendarConflict($request, $id,  $userId),
        ];
        return _success($data, __('message.updated_success'), HTTP_CREATED);
    }

    // show calendar
    public function show($request, $id)
    {
        $auth = auth()->user();
        $authId = $auth->id;
        $calendar = $this->calendarInterface->find($id);
        $isHost = $this->calendarInterface->findHostCalendar($id)->user_id;
        $modifyDateCalendar =  Carbon::parse($request->meeting_start_time)->toDateString();

        $checkShowCalendar = $this->checkCalendar($calendar, $authId);
        if ($checkShowCalendar) {
            return $checkShowCalendar;
        }

        $subCalendar = $this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id);
        if ($calendar->repeat_id == NOT_REPEAT ||  !$subCalendar) {
            $checkRoleShow = $this->checkRoleShow($calendar, $auth);
        } else {
            $checkRoleShow = $this->checkRoleShowSubCalendar($calendar, $subCalendar, $auth);
        }
        if ($checkRoleShow) {
            return $checkRoleShow;
        }

        $user = $this->userInterface->find($isHost);
        $detailCalendar = $this->calendarInterface->show($id);
        $divisionCreatedCalendar = $this->divisionInterface->listDivisionCreatedCalendar($user, null);

        $calendarConflict = $this->calendarInterface->findCalendarConflict($request, $id, $authId);
        if ($calendar->users->contains('id', $authId)) {
            $data = [
                'detail_calendar' =>  $detailCalendar,
                'division_created' => $divisionCreatedCalendar,
                'calendar_conflict' => $calendarConflict,
                'total_calendar_conflict' => $calendarConflict->count(),
            ];
        } else {
            $data = [
                'detail_calendar' =>  $detailCalendar,
                'division_created' => $divisionCreatedCalendar,
            ];
        }
        return _success($data, __('message.show_success'), HTTP_SUCCESS);
    }

    // approve or reject calendar
    public function approvedCalendar($request, $id)
    {
        $user = auth()->user();
        $userId = $user->id;
        $calendar = $this->calendarInterface->find($id);
        $modifyDateCalendar =  Carbon::parse($request->modify_date);
        $startTime = date('H:i:s', strtotime($calendar->meeting_start_time));
        $endTime = date('H:i:s', strtotime($calendar->meeting_end_time));
        $userIds = $calendar->users->pluck('id')->toArray();
        $checkCalendar = $this->checkCalendar($calendar, $userId);
        if ($checkCalendar) {
            return $checkCalendar;
        }
        $attributes = ['is_accept' => $request->is_accept];

        if ($calendar->calendarUsers->contains('id', $userId)) {
            if (($calendar->repeat_id == NOT_REPEAT) || (($calendar->repeat_id != NOT_REPEAT) && ($request->choice == CALENDAR_ALL))) {
                $calendarUsers = $this->calendarUserInterface->findUser($userId, $id);
                foreach ($calendarUsers as $calendarUser) {
                    $calendarUserId = $calendarUser->id;
                    if ($calendarUser->is_accept != UNKNOWN) {
                        continue;
                    }

                    $this->calendarUserInterface->update($calendarUserId, $attributes);
                }
            } else if ($request->choice == CALENDAR_ONE) {
                if (($calendar->repeat_id == REPEAT_MONTH) ||
                    (($calendar->repeat_id == REPEAT_DAY || $calendar->repeat_id == REPEAT_WEEK) && ($this->calendarUserInterface->findCalendar($modifyDateCalendar, $userId, $id)))
                ) {
                    $calendarUser = $this->calendarUserInterface->findCalendar($modifyDateCalendar, $userId, $id);
                    $calendarUserId =  $calendarUser->id;
                    if ($calendarUser->is_accept == UNKNOWN) {
                        $this->calendarUserInterface->update($calendarUserId, $attributes);
                    }
                } else {
                    $params = [
                        'title' => $calendar->title,
                        'meeting_start_time' =>  $request->modify_date . ' ' . $startTime,
                        'meeting_end_time' =>  $request->modify_date . ' ' . $endTime,
                        'modify_date' => $modifyDateCalendar,
                        'meeting_url' => $calendar->meeting_url,
                        'note' => $calendar->note,
                        'calendar_id' => $calendar->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $this->subCalendarInterface->insert($params);
                    $subCalendar = $this->subCalendarInterface->findSubCalendar($modifyDateCalendar, $id);
                    $calendarUser = $this->calendarUserInterface->calendar($id);
                    for ($i = 0; $i < count($userIds); $i++) {
                        $isHost = $this->calendarUserInterface->findUserHost($userIds[$i], $id)->is_host;
                        $params = [
                            'calendar_id' => $id,
                            'notify_id' =>  $calendarUser->notify_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'sub_calendar_id' =>  $subCalendar->id,
                            'is_host' => $isHost,
                        ];
                        if ($userIds[$i] == $userId) {
                            $params['user_id'] = $userId;
                            $params['is_accept'] = $request->is_accept;
                        } else {
                            $params['user_id'] = $userIds[$i];
                            $params['is_accept'] = $calendarUser->is_accept;
                        }
                        $this->calendarUserInterface->insert($params);
                    }
                }
            }
            return _success(null, __('message.updated_success'), HTTP_CREATED);
        }
        return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
    }


    public function index($request)
    {
        $calendars = $this->calendarInterface->index($request)->get()->each->append(['auth_user_accept', 'customers']);
        return _success($calendars, __('message.get_list_success'), HTTP_SUCCESS);
    }


    public function paramCalendars($repeatDayCalendars, $repeatDayCalendars1, $request, $calendar)
    {
        $params = [
            'title' => $request->title,
            'meeting_start_time' =>  $repeatDayCalendars['meeting_start_time'],
            'meeting_end_time' =>  $repeatDayCalendars['meeting_end_time'],
            'modify_date' => $repeatDayCalendars['meeting_start_time'],
            'meeting_url' => $request->meeting_url,
            'note' => $request->note,
            'calendar_id' => $calendar->id,
            'created_at' => now(),
            'updated_at' => now(),
            'notify_id' => $request->notify_id
        ];
        $params1 = [];
        foreach ($repeatDayCalendars1 as $repeatDayCalendar) {
            $params1[] = [
                'title' => $request->title,
                'meeting_start_time' =>  $repeatDayCalendar['meeting_start_time'],
                'meeting_end_time' =>  $repeatDayCalendar['meeting_end_time'],
                'modify_date' =>  $repeatDayCalendar['meeting_start_time'],
                'meeting_url' => $request->meeting_url,
                'note' => $request->note,
                'calendar_id' => $calendar->id,
                'created_at' => now(),
                'updated_at' => now(),
                'notify_id' => $request->notify_id
            ];
        }
        $this->subCalendarInterface->insert($params);
        $this->subCalendarInterface->insert($params1);
        $subCalendars = $calendar->subCalendars->pluck('id', 'modify_date');
        foreach ($subCalendars as $index => $subCalendar) {
            // relate user
            $this->createRelateUserRepeatMonth($calendar->id, $request, auth()->user(), $subCalendar, $index);

            // documents
            if (isset($request->documents)) {
                $files = $request->documents;
                $filePath = 'subCalendar/' . $subCalendar . '/documents';

                foreach ($files as $file) {
                    $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                    $this->subCalendarFileInterface->create([
                        'sub_calendar_id' => $subCalendar,
                        'name' => $file->getClientOriginalName(),
                        'url'  => $fileUrl,
                    ]);
                }
            }
        }
    }

    public function createRelateUser($calendarId, $request, $authUser, $subCalendarId, $meetingStartTime)
    {
        $notifyId = $request->notify_id;
        $time = new Carbon($meetingStartTime);
        switch ($notifyId) {
            case NOTI_CALENDAR_5P:
                $startTimeMeeting = $time->addMinutes(-5);
                break;
            case NOTI_CALENDAR_15P:
                $startTimeMeeting =  $time->addMinutes(-15);
                break;
            case NOTI_CALENDAR_30P:
                $startTimeMeeting =  $time->addMinutes(-30);
                break;
            case NOTI_CALENDAR_60p:
                $startTimeMeeting =  $time->addMinutes(-60);
                break;
            case NOTI_CALENDAR_1_DATE:
                $startTimeMeeting = $time->addDays(-1);
                break;
            default:
                $startTimeMeeting = null;
                break;
        }
        if ($startTimeMeeting) {
            $startTimeMeeting = $startTimeMeeting->format('Y-m-d H:i:s');
        }
        $paramRelateUser1 = [
            'user_id' => $authUser->id,
            'calendar_id' => $calendarId,
            'notify_id' => $request->notify_id,
            'is_accept' => IS_ACCEPT,
            'created_at' => now(),
            'updated_at' => now(),
            'is_host' => true,
            'sub_calendar_id' => $subCalendarId,
            'start_time_meeting' => $startTimeMeeting
        ];

        $this->calendarUserInterface->insert($paramRelateUser1);
        if (isset($request->relate_user_ids)) {
            $paramRelateUser2 = [];
            $relateUserIds = $request->relate_user_ids;
            for ($i = 0; $i < count($request->relate_user_ids); $i++) {
                $paramRelateUser2[] = [
                    'user_id' => $relateUserIds[$i],
                    'calendar_id' => $calendarId,
                    'notify_id' => $request->notify_id,
                    'is_accept' => UNKNOWN,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'sub_calendar_id' => $subCalendarId,
                    'start_time_meeting' => $startTimeMeeting
                ];
            }
            $this->calendarUserInterface->insert($paramRelateUser2);
        }
    }

    // Update Sub calendar
    public function paramSubCalendars($request, $meetingStartTime, $meetingEndTime, $subCalendarId, $id)
    {
        $attributes = [
            'title' => $request->title,
            'meeting_start_time' => $meetingStartTime,
            'meeting_end_time' => $meetingEndTime,
            'modify_date' => $meetingStartTime,
            'meeting_url' => $request->meeting_url,
            'calendar_id' => $id,
            'note' => $request->note,
            'notify_id' => $request->notify_id
        ];
        $this->subCalendarInterface->update($subCalendarId, $attributes);

        // documents
        if (isset($request->documents)) {
            $files = $request->documents;
            $filePath = 'subCalendar/' . $subCalendarId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->subCalendarFileInterface->create([
                    'sub_calendar_id' => $id,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                ]);
            }
        }
        $subCalendar = $this->subCalendarInterface->find($subCalendarId);
        $notifyId = $request->notify_id;
        $time = new Carbon($request->meeting_start_time);
        switch ($notifyId) {
            case NOTI_CALENDAR_5P:
                $startTimeMeeting = $time->addMinutes(-5);
                break;
            case NOTI_CALENDAR_15P:
                $startTimeMeeting =  $time->addMinutes(-15);
                break;
            case NOTI_CALENDAR_30P:
                $startTimeMeeting =  $time->addMinutes(-30);
                break;
            case NOTI_CALENDAR_60p:
                $startTimeMeeting =  $time->addMinutes(-60);
                break;
            case NOTI_CALENDAR_1_DATE:
                $startTimeMeeting = $time->addDays(-1);
                break;
            default:
                $startTimeMeeting = null;
                break;
        }

        if ($startTimeMeeting) {
            $startTimeMeeting = $startTimeMeeting->format('Y-m-d H:i:s');
        }
        // relate user
        if (isset($request->relate_user_ids)) {
            $currentRelateUserIds =   $subCalendar->users()->pluck('users.id')->toArray();
            $newRelateUserIds =  $request->relate_user_ids;

            $addRelateUserIds = array_diff($newRelateUserIds, $currentRelateUserIds);
            $deleteRelateUserIds = array_diff($currentRelateUserIds, $newRelateUserIds);

            if (count($deleteRelateUserIds)) {
                $subCalendar->users()->detach($deleteRelateUserIds);
            }

            if (is_array($request->relate_user_ids)) {
                $subCalendar->users()->attach(
                    $addRelateUserIds,
                    [
                        'notify_id' => $notifyId,
                        'calendar_id' => $id,
                        'start_time_meeting' => $startTimeMeeting,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        } else {
            $subCalendar->users()->detach();
        }
        $subCalendar->users()->attach(
            auth()->user()->id,
            [
                'calendar_id' => $id,
                'notify_id' => $notifyId,
                'is_host' => true,
                'is_accept' => IS_ACCEPT,
                'start_time_meeting' => $startTimeMeeting
            ]
        );
    }

    // Create sub calendar
    public function createSubCalendars($request, $modifyDateCalendar, $calendar)
    {
        $attributes = [
            'title' => $request->title,
            'meeting_start_time' => $request->meeting_start_time,
            'meeting_end_time' => $request->meeting_end_time,
            'modify_date' => $modifyDateCalendar,
            'meeting_url' => $request->meeting_url,
            'calendar_id' => $calendar->id,
            'note' => $request->note,
            'notify_id' => $request->notify_id
        ];
        $subCalendar = $this->subCalendarInterface->create($attributes);
        $subCalendarId = $subCalendar->id;

        $this->createRelateUser($calendar->id, $request, auth()->user(), $subCalendarId, $request->meeting_start_time);

        $calendarFiles = $this->calendarInterface->findFileCalendar($calendar->id);
        if ($calendarFiles->count() > NO_FILE) {
            foreach ($calendarFiles as $calendarFile) {
                $params = [
                    'sub_calendar_id' => $subCalendarId,
                    'name' => $calendarFile->name,
                    'url' => $calendarFile->url,
                    'calendar_file_id' => $calendarFile->id
                ];
                $this->subCalendarFileInterface->create($params);
            }
        }
        // delete documents
        if (isset($request->delete_document_ids)) {
            $deleteDocumentIds = $request->delete_document_ids;
            foreach ($deleteDocumentIds as $deleteDocumentId) {
                $file = $this->subCalendarFileInterface->findFile($deleteDocumentId, $subCalendarId);
                $fileId = $file->id;
                if ($file) {
                    $this->subCalendarFileInterface->delete($fileId);
                }
            }
        }
        // documents
        if (isset($request->documents)) {
            $files = $request->documents;
            $filePath = 'subCalendar/' . $subCalendarId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->subCalendarFileInterface->create([
                    'sub_calendar_id' => $subCalendarId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                ]);
            }
        }
        // Push noti
        if (isset($request->relate_user_ids)) {
            $userIds = $request->relate_user_ids;
            $deviceTokenUsers = $this->userInterface->listDeviceToken($userIds);
            try {
                $this->notifyService->pushNotify($deviceTokenUsers, $request->title, $request->meeting_start_time . $request->meeting_end_time);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - Push noti when create sub calendar success');
            }
        }
        return $subCalendarId;
    }

    public function deleteCalendarCompany($companyId)
    {
        try {
            $calendars = $this->calendarInterface->findCalendarInCompany($companyId);
            foreach ($calendars as $calendar) {
                $calendar->users()->detach();
                $calendar->subCalendars()->delete();
                $calendar->documents()->delete();
                $calendar->delete();
            }
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - ' .  $e->getMessage());
            return _errorSystem($e);
        }
    }

    public function reminderCalendarNotRepeats()
    {
        try {
            $now = now();
            $dateNow = $now->format('H:i');
            $calendars = $this->calendarUserInterface->reminderCalendarNotRepeats($dateNow);
            $arrayCalendars = [];
            foreach ($calendars as $calendar) {
                if ($calendar['date'] == $now->format('Y-m-d')) {
                    $arrayCalendars[] = $calendar;
                }
            }
            $arrayCalendarId = array_column($arrayCalendars, 'calendar_id');
            $calendarIds = array_unique($arrayCalendarId);
            $array = [];
            if (count($calendarIds) > 0) {
                foreach ($calendarIds as $calendarId) {
                    foreach ($arrayCalendars as $arrayCalendar) {
                        if ($arrayCalendar['calendar_id'] == $calendarId) {
                            $notifyId = $arrayCalendar['notify_id'];
                            switch ($notifyId) {
                                case NOTI_CALENDAR_5P:
                                    $title = 'マインダー ５分';
                                    break;
                                case NOTI_CALENDAR_15P:
                                    $title = 'マインダー 1５分';
                                    break;
                                case NOTI_CALENDAR_30P:
                                    $title = 'マインダー 30分';
                                    break;
                                case NOTI_CALENDAR_60p:
                                    $title = 'マインダー 60分';
                                    break;
                                case NOTI_CALENDAR_1_DATE:
                                    $title = 'マインダー 1日';
                                    break;
                            }
                            $data = [
                                "calendar_id" => $calendarId,
                                "repeat_id" => NOT_REPEAT,
                                "start_date" => $arrayCalendar['date'],
                                "end_date" => $arrayCalendar['date_end'],
                                "type" => "calendar_reminder"
                            ];
                            $deviceTokenUsers = $this->userInterface->listDeviceToken([$arrayCalendar['user_id']]);
                            try {
                                $this->notifyService->pushNotify($deviceTokenUsers, $title, '', $data);
                            } catch (Exception $e) {
                                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            return $array;
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function reminderCalendarRepeatDays()
    {
        try {
            $now = now();
            $dateNow = $now->format('H:i');
            $calendars = $this->calendarUserInterface->reminderCalendarRepeatDay($dateNow, $now->format('Y-m-d'));
            $arrayCalendars = [];
            foreach ($calendars as $calendar) {
                if ($calendar['sub_calendar_id'] && $calendar['meeting_sub_calendar'] == $now->format('Y-m-d')) {
                    $arrayCalendars[] = $calendar;
                } elseif ($calendar['date_user'] == $now->format('Y-m-d')) {
                    $arrayCalendars[] = $calendar;
                }
            }
            $arrayCalendarId = array_column($arrayCalendars, 'calendar_id');
            $calendarIds = array_unique($arrayCalendarId);
            if (count($calendarIds) > 0) {
                foreach ($calendarIds as $calendarId) {
                    foreach ($arrayCalendars as $arrayCalendar) {
                        if ($arrayCalendar['calendar_id'] == $calendarId) {
                            $notifyId = $arrayCalendar['notify_id'];
                            switch ($notifyId) {
                                case NOTI_CALENDAR_5P:
                                    $title = 'マインダー ５分';
                                    break;
                                case NOTI_CALENDAR_15P:
                                    $title = 'マインダー 1５分';
                                    break;
                                case NOTI_CALENDAR_30P:
                                    $title = 'マインダー 30分';
                                    break;
                                case NOTI_CALENDAR_60p:
                                    $title = 'マインダー 60分';
                                    break;
                                case NOTI_CALENDAR_1_DATE:
                                    $title = 'マインダー 1日';
                                    break;
                                default:
                                    $title = '';
                                    break;
                            }
                            $data = [
                                'calendar_id' => $calendarId,
                                'start_date' => $arrayCalendar['start_date'],
                                'end_date' => $arrayCalendar['end_date'],
                                'repeat_id' => $arrayCalendar['repeat_id'],
                                'type' => 'calendar_reminder'
                            ];
                            $deviceTokenUsers = $this->userInterface->listDeviceToken([$arrayCalendar['user_id']]);
                            try {
                                $this->notifyService->pushNotify($deviceTokenUsers, $title, '', $data);
                            } catch (Exception $e) {
                                Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            return $arrayCalendar['start_date'] ?? null;
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function reminderCalendarRepeatWeek()
    {
        try {
            $now = now();
            $timeNow = $now->format('H:i');
            $calendars = $this->calendarUserInterface->reminderCalendarRepeatWeek($timeNow, $now->format('Y-m-d'));
            $arrayCalendar1 = [];
            $arrayCalendar2 = [];
            $arrayCalendarId = array_column($calendars->toArray(), 'calendar_id');
            $calendarIds = array_values(array_unique($arrayCalendarId));
            $calendarIdDeletes = [];
            for ($x = 0; $x < count($calendarIds); $x++) {
                for ($i = 0; $i < count($calendars); $i++) {
                    if ($calendars[$i]['calendar_id'] == $calendarIds[$x] && $calendars[$i]['date_user'] <= $now->format('Y-m-d')) {
                        $arrayCalendar1[$calendarIds[$x]][] = $calendars[$i];
                    }
                    if ($calendars[$i]['calendar_id'] == $calendarIds[$x] && $calendars[$i]['date_user'] == $now->format('Y-m-d') && $calendars[$i]['sub_calendar_id']) {
                        $arrayCalendar2[$calendarIds[$x]][] = $calendars[$i];
                        $calendarIdDeletes[] = $calendarIds[$x];
                        unset($calendarIds[$x]);
                        $calendarIds =  array_values($calendarIds);
                    }
                }
            }

            foreach ($calendarIdDeletes as $calendarIdDelete) {
                foreach ($arrayCalendar2[$calendarIdDelete] as $arrayCalendar) {
                    if ($arrayCalendar['sub_calendar_id']) {
                        $notifyId = $arrayCalendar['notify_id'];
                        switch ($notifyId) {
                            case NOTI_CALENDAR_5P:
                                $title = 'マインダー ５分';
                                break;
                            case NOTI_CALENDAR_15P:
                                $title = 'マインダー 1５分';
                                break;
                            case NOTI_CALENDAR_30P:
                                $title = 'マインダー 30分';
                                break;
                            case NOTI_CALENDAR_60p:
                                $title = 'マインダー 60分';
                                break;
                            case NOTI_CALENDAR_1_DATE:
                                $title = 'マインダー 1日';
                                break;
                        }
                        $data = [
                            'calendar_id' => $calendarIdDelete,
                            'start_date' => $arrayCalendar['start_time'],
                            'end_date' => $arrayCalendar['end_time'],
                            'repeat_id' => $arrayCalendar['repeat_id'],
                            'type' => 'calendar_reminder'
                        ];
                        $deviceTokenUsers = $this->userInterface->listDeviceToken([$arrayCalendar['user_id']]);
                        try {
                            $this->notifyService->pushNotify($deviceTokenUsers, $title, '', $data);
                        } catch (Exception $e) {
                            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
                        }
                    }
                }
            }


            foreach ($calendarIds as $calendarId) {
                foreach ($arrayCalendar1[$calendarId] as $arrayCalendar) {
                    if ($arrayCalendar['sub_calendar_id'] && $arrayCalendar['date_user'] == $now->format('Y-m-d')) {
                        $notifyId = $arrayCalendar['notify_id'];
                        switch ($notifyId) {
                            case NOTI_CALENDAR_5P:
                                $title = 'マインダー ５分';
                                break;
                            case NOTI_CALENDAR_15P:
                                $title = 'マインダー 1５分';
                                break;
                            case NOTI_CALENDAR_30P:
                                $title = 'マインダー 30分';
                                break;
                            case NOTI_CALENDAR_60p:
                                $title = 'マインダー 60分';
                                break;
                            case NOTI_CALENDAR_1_DATE:
                                $title = 'マインダー 1日';
                                break;
                        }
                        $data = [
                            'calendar_id' => $calendarId,
                            'start_date' => $arrayCalendar['start_time'],
                            'end_date' => $arrayCalendar['end_time'],
                            'repeat_id' => $arrayCalendar['repeat_id'],
                            'type' => 'calendar_reminder'
                        ];
                        $deviceTokenUsers = $this->userInterface->listDeviceToken([$arrayCalendar['user_id']]);
                        try {
                            $this->notifyService->pushNotify($deviceTokenUsers, $title, '', $data);
                        } catch (Exception $e) {
                            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - ' . $e->getMessage());
            return _errorSystem($e);
        }
    }

    public function reminderCalendarRepeatMonth()
    {
        try {
            $now = now();
            $timeNow = $now->format('Y-m-d H:i');
            $calendars = $this->calendarUserInterface->reminderCalendarRepeatMonth($timeNow);
            if (count($calendars) > 0) {
                foreach ($calendars as $calendar) {
                    $notifyId = $calendar['notify_id'];
                    switch ($notifyId) {
                        case NOTI_CALENDAR_5P:
                            $title = 'マインダー ５分';
                            break;
                        case NOTI_CALENDAR_15P:
                            $title = 'マインダー 1５分';
                            break;
                        case NOTI_CALENDAR_30P:
                            $title = 'マインダー 30分';
                            break;
                        case NOTI_CALENDAR_60p:
                            $title = 'マインダー 60分';
                            break;
                        case NOTI_CALENDAR_1_DATE:
                            $title = 'マインダー 1日';
                            break;
                    }
                    $data = [
                        'calendar_id' => $calendar['id'],
                        'start_date' => $calendar['start_time'],
                        'end_date' => $calendar['end_time'],
                        'repeat_id' => $calendar['repeat_id'],
                        'type' => 'calendar_reminder'
                    ];
                    $deviceTokenUsers = $this->userInterface->listDeviceToken([$calendar['user_id']]);
                    try {
                        $this->notifyService->pushNotify($deviceTokenUsers, $title, '', $data);
                    } catch (Exception $e) {
                        Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function changeNotiCalendar($request, $id)
    {
        try {
            $user = Auth::user();
            $notifyId = $request->notify_id;
            $time = new Carbon($request->meeting_start_time);
            switch ($notifyId) {
                case NOTI_CALENDAR_5P:
                    $startTimeMeeting = $time->addMinutes(-5);
                    break;
                case NOTI_CALENDAR_15P:
                    $startTimeMeeting =  $time->addMinutes(-15);
                    break;
                case NOTI_CALENDAR_30P:
                    $startTimeMeeting =  $time->addMinutes(-30);
                    break;
                case NOTI_CALENDAR_60p:
                    $startTimeMeeting =  $time->addMinutes(-60);
                    break;
                case NOTI_CALENDAR_1_DATE:
                    $startTimeMeeting = $time->addDays(-1);
                    break;
                default:
                    $startTimeMeeting = null;
                    break;
            }
            return $this->calendarUserInterface->changeNotiCalendar($request, $id, $user->id, $startTimeMeeting);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Error - ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function createRelateUserRepeatMonth($calendarId, $request, $authUser, $subCalendarId, $meetingStartTime)
    {
        $notifyId = $request->notify_id;
        $time = new Carbon($meetingStartTime);
        switch ($notifyId) {
            case NOTI_CALENDAR_5P:
                $startTimeMeeting = $time->addMinutes(-5);
                break;
            case NOTI_CALENDAR_15P:
                $startTimeMeeting =  $time->addMinutes(-15);
                break;
            case NOTI_CALENDAR_30P:
                $startTimeMeeting =  $time->addMinutes(-30);
                break;
            case NOTI_CALENDAR_60p:
                $startTimeMeeting =  $time->addMinutes(-60);
                break;
            case NOTI_CALENDAR_1_DATE:
                $startTimeMeeting = $time->addDays(-1);
                break;
            default:
                $startTimeMeeting = null;
                break;
        }

        if ($startTimeMeeting) {
            $startTimeMeeting = $startTimeMeeting->format('Y-m-d H:i:s');
        }

        $paramRelateUser1 = [
            'user_id' => $authUser->id,
            'calendar_id' => $calendarId,
            'notify_id' => $request->notify_id,
            'is_accept' => IS_ACCEPT,
            'created_at' => now(),
            'updated_at' => now(),
            'is_host' => true,
            'sub_calendar_id' => $subCalendarId,
            'start_time_meeting' => $startTimeMeeting
        ];

        $this->calendarUserInterface->insert($paramRelateUser1);
        if (isset($request->relate_user_ids)) {
            $paramRelateUser2 = [];
            $relateUserIds = $request->relate_user_ids;
            for ($i = 0; $i < count($request->relate_user_ids); $i++) {
                $paramRelateUser2[] = [
                    'user_id' => $relateUserIds[$i],
                    'calendar_id' => $calendarId,
                    'notify_id' => $request->notify_id,
                    'is_accept' => UNKNOWN,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'sub_calendar_id' => $subCalendarId,
                    'start_time_meeting' => $startTimeMeeting
                ];
            }
            $this->calendarUserInterface->insert($paramRelateUser2);
        }
    }

    public function checkExistCalendar($request)
    {
        $calendar = $this->calendarInterface->checkExistCalendar($request);
        if (count($calendar) > 0) {
            if (isset($request->relate_user_ids)) {
                $userIds = $request->relate_user_ids;
                $calendarUser = $this->calendarUserInterface->checkUserOfCalendar($calendar, $userIds);
                if (count($calendarUser) > 0) {
                    return _error(null, __('message.calendar_exists'), HTTP_BAD_REQUEST);
                }
            }
        }
        return _success(null, __('message.created_success'), HTTP_CREATED);
    }

    public function reminderCalendarStartTime()
    {
        $now = now();
        $startDateCalendar = $now->format('Y-m-d H:i');
        $calendars = $this->calendarInterface->getCalendarStartTimeNow($startDateCalendar);
        if ($calendars) {
            foreach ($calendars as $calendar) {
                $listUserIds = [];
                foreach ($calendar->calendarUserNows as $user) {
                    $listUserIds[] = $user['user_id'];
                }
                $data = [
                    'calendar_id' => $calendar['id'],
                    'start_date' => $calendar['meeting_start_time'],
                    'end_date' => $calendar['meeting_end_time'],
                    'repeat_id' => 2,
                    'type' => 'calendar_reminder'
                ];
                $deviceTokenUsers = $this->userInterface->listDeviceToken($listUserIds);
                try {
                    $this->notifyService->pushNotify($deviceTokenUsers,  $calendar['title'] . 'を開始します。', $calendar['start_date'], $data);
                } catch (Exception $e) {
                    Log::error(__METHOD__ . ' - ' . __LINE__ . ' : Push notify update sub calendar: ' . $e->getMessage());
                }
            }
        }
    }
}

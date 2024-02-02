<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\CreateCalendarRequest;
use Illuminate\Support\Facades\DB;
use App\Services\CalendarService;
use App\Http\Requests\UpdateCalendarRequest;
use App\Http\Requests\ApprovedCalendarRequest;
use App\Http\Requests\ChangeNotiCalendarRequest;
use App\Http\Requests\CheckExitCalendarRequest;
use App\Http\Requests\DeleteCalendarRequest;
use App\Http\Requests\ListCalendarRequest;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * List calendar in company
     *
     * @param ListCalendarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListCalendarRequest $request)
    {
        try {
            return $this->calendarService->index($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Create calendar
     *
     * @param CreateCalendarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCalendarRequest $request)
    {
        DB::beginTransaction();

        try {
            $userId = auth()->user()->id;
            $checkParamsCalendar = $this->calendarService->checkParamsCalendar($request, null, $userId);
            $checkDuplicate = $this->calendarService->checkDuplicate($request, null);
            $checkDuplicateTime = $this->calendarService->checkDuplicateTime($request, null, null);
            if ($checkParamsCalendar) {
                return $checkParamsCalendar;
            }
            if ($checkDuplicate) {
                return $checkDuplicate;
            }
            if ($checkDuplicateTime) {
                return $checkDuplicateTime;
            }

            $data = $this->calendarService->create($request);
            DB::commit();
            return _success($data, __('message.created_success'), HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Show detail calendar
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            return $this->calendarService->show($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Update calendar
     *
     * @param UpdateCalendarRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCalendarRequest $request, $id)
    {
        try {
            $checkDuplicateTime = $this->calendarService->checkDuplicateTime($request, $id, null);
            if ($checkDuplicateTime) {
                return $checkDuplicateTime;
            }
            return $this->calendarService->updateCalendar($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Destroy calendar
     *
     * @param DeleteCalendarRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteCalendarRequest $request, $id)
    {
        try {
            return $this->calendarService->delete($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Approve calendar
     * @param ApprovedCalendarRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approvedCalendar(ApprovedCalendarRequest $request, $id)
    {
        try {
            return $this->calendarService->approvedCalendar($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function changeNotiCalendar(ChangeNotiCalendarRequest $request, $id)
    {
        try {
            $calendar = $this->calendarService->changeNotiCalendar($request, $id);
            return _success($calendar, __('message.created_success'), HTTP_CREATED);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function checkExistCalendar(CheckExitCalendarRequest $request)
    {
        try {
            return $this->calendarService->checkExistCalendar($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function test(Request $request)
    {
        try {
            return $this->calendarService->reminderCalendarStartTime($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}

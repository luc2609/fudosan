<?php

namespace App\Http\Requests;

class CreateCalendarRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string',
            'meeting_start_time'  => 'required|date',
            'meeting_end_time' => 'required|date|after_or_equal:meeting_start_time',
            'meeting_type' => 'required|integer',
            'meeting_url' => 'string',
            'is_public' => 'required|boolean',
            'repeat_id' => 'required|exists:master_schedule_repeats,id',
            'project_id' => 'nullable|exists:projects,id,deleted_at,NULL',
            'documents.*' => 'mimes:csv,txt,pdf,docx,jpeg,png,jpg|max:' . MAX_UPLOAD_FILE_SIZE,
            'documents' => 'array|max:5',
            'relate_user_ids.*' => 'exists:users,id|distinct',
            'relate_user_ids' => 'array',
            'division_id' => 'nullable|exists:divisions,id,deleted_at,NULL',
            'notify_id' => 'required|exists:master_notify_calendars,id',
            'repeat_day' => 'nullable|string'
        ];
    }
}

<?php

namespace App\Http\Requests;

class UpdateCalendarRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string',
            'meeting_start_time'  => 'required|date',
            'meeting_end_time' => 'required|date|after_or_equal:meeting_start_time',
            'meeting_url' => 'string',
            'division_id' => 'nullable|exists:divisions,id,deleted_at,NULL',
            'documents.*' => 'mimes:csv,txt,pdf,docx,jpeg,png,jpg|max:' . MAX_UPLOAD_FILE_SIZE,
            'documents' => 'array|max:5',
            'delete_document_ids.*' => 'distinct',
            'delete_document_ids' => 'array|max:5',
            'relate_user_ids.*' => 'exists:users,id|distinct',
            'relate_user_ids' => 'array',
            'is_public' => 'boolean',
            'meeting_type' => 'integer',
            'notify_id' => 'required|exists:master_notify_calendars,id',
            'choice' => 'nullable|integer'
        ];
    }
}

<?php

namespace App\Http\Requests;

class CheckExitCalendarRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'meeting_start_time' => 'required|string',
            'meeting_end_time' => 'required|string',
            'relate_user_ids'  => 'nullable|array'
        ];
    }
}

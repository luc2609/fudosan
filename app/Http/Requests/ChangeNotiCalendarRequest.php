<?php

namespace App\Http\Requests;

class ChangeNotiCalendarRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'notify_id' => 'required|integer',
            'type' => 'required|integer',
            'meeting_start_time' => 'required|string'
        ];
    }
}

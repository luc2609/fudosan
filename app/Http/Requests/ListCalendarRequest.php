<?php

namespace App\Http\Requests;

class ListCalendarRequest extends BaseRequest
{
    public function rules()
    {
        $intRule = 'nullable|integer';

        return [
            'title' => 'nullable|string',
            'action' => $intRule,
            'division_id' => 'nullable|integer|exists:divisions,id',
            'user_id' =>  'nullable|integer|exists:users,id',
            'meeting_type' =>  'nullable|integer',
            'start_date_limit' => 'required|date',
            'end_date_limit' => 'required|date|after_or_equal:start_date_limit',
        ];
    }
}

<?php

namespace App\Http\Requests;

class ApprovedCalendarRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'is_accept' => 'required|integer'
        ];
    }
}

<?php

namespace App\Http\Requests;

class DeleteCalendarRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'choice' => 'nullable|integer'
        ];
    }
}

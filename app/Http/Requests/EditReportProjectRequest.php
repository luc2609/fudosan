<?php

namespace App\Http\Requests;

class EditReportProjectRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
        ];
    }
}

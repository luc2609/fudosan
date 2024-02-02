<?php

namespace App\Http\Requests;

class CreateReportProjectRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
        ];
    }
}

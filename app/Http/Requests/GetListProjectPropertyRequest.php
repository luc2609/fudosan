<?php

namespace App\Http\Requests;

class GetListProjectPropertyRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'project_type' => 'nullable|integer',
            'page' => 'nullable|integer',
            'page_size' => 'nullable|integer',
            'title' => 'nullable|string',
            'project_phase_id' => 'nullable|integer',
        ];
    }
}

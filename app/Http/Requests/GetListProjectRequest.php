<?php

namespace App\Http\Requests;

class GetListProjectRequest extends BaseRequest
{
    public function rules()
    {
        $intRule = 'nullable|integer';

        return [
            'page' => $intRule,
            'page_size' => $intRule,
            'action' => $intRule,
            'division_id' => 'nullable|integer|exists:divisions,id',
            'type' => $intRule,
            'keyword' => 'nullable|string',
            'phase_id' => 'nullable|integer|exists:master_phase_projects,id',
        ];
    }
}

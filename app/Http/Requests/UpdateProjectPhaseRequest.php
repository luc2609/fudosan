<?php

namespace App\Http\Requests;

class UpdateProjectPhaseRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'preliminary_test_date' => 'nullable|date',
            'actual_test_date' => 'nullable|date',
        ];
    }
}

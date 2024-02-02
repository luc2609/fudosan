<?php

namespace App\Http\Requests;

class AddAvailableDivisionRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'divisions' => 'required|array',
            'divisions.*' => 'exists:divisions,id',
        ];
    }
}

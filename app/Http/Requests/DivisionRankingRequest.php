<?php

namespace App\Http\Requests;

class DivisionRankingRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'divisions' => 'array',
            'divisions.*' => 'exists:divisions,id',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date'
        ];
    }
}

<?php

namespace App\Http\Requests;

class RankingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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

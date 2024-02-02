<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetListProjectCustomerRequest extends FormRequest
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

<?php

namespace App\Http\Requests;

class GetListCompanyRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|integer',
            'page_size' => 'nullable|integer',
            'name' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

class GetListContactRequest extends BaseRequest
{
    public function rules()
    {
        $intRule = 'nullable|integer';

        return [
            'page' => $intRule,
            'page_size' => $intRule,
            'keyword' => 'nullable|string',
            'status' => $intRule,
        ];
    }
}

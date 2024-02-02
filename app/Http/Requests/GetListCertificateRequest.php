<?php

namespace App\Http\Requests;

class GetListCertificateRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'page' => 'nullable|integer',
            'page_size' => 'nullable|integer',
        ];
    }
}

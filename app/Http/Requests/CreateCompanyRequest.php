<?php

namespace App\Http\Requests;


class CreateCompanyRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'province' => 'nullable',
            'district' => 'nullable',
            'street' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable|max:50',
            'website' => 'nullable|max:50',
            'commission_rate' => 'nullable|integer',
        ];
    }
}

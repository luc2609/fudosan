<?php

namespace App\Http\Requests;

class CreateDeviceTokenRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'device_token' => 'nullable|string',
        ];
    }
}

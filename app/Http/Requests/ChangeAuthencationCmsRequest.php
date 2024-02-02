<?php

namespace App\Http\Requests;


class ChangeAuthencationCmsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'status' => 'required|integer'
        ];
    }
}

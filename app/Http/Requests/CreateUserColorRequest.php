<?php

namespace App\Http\Requests;

class CreateUserColorRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'color' => 'required',
            'type' => 'required|integer',
        ];
    }
}

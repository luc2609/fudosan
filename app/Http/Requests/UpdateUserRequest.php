<?php

namespace App\Http\Requests;

class UpdateUserRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'kana_first_name' => 'required|string',
            'kana_last_name' => 'required|string',
            'phone' => 'required',
            'divisions' => 'nullable|array',
            // 'role' => 'nullable|integer|min:3|max:4'
        ];
    }
}

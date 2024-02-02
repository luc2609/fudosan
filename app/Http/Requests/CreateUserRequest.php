<?php

namespace App\Http\Requests;

class CreateUserRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'kana_first_name' => 'required|string',
            'kana_last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'phone' => 'required',
            'password' => 'required|min:8|max:20',
            'position' => 'exists:master_positions,id',
            'divisions' => 'array',
            'divisions.*' => 'exists:divisions,id',
            'commission_rate' => 'integer',
            'role_id' => 'nullable|integer|min:3|max:4',
        ];
    }
}

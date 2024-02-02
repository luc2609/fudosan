<?php

namespace App\Http\Requests;

class CreateAccountAdminCompanyRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'kana_first_name' => 'required|string',
            'kana_last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string',
            'position' => 'nullable|integer|exists:master_positions,id',
            'company' => 'required|integer|exists:companies,id',
            'phone' => 'nullable'
        ];
    }
}

<?php

namespace App\Http\Requests;

class UpdateAccountAdminCompanyRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'kana_first_name' => 'nullable|string',
            'kana_last_name' => 'nullable|string',
            'email' => 'nullable|string|unique:users,email',
            'password' => 'nullable|string',
            'position' => 'nullable|integer|exists:master_positions,id',
            'company' => 'nullable|integer|exists:companies,id',
            'phone' => 'nullable'
        ];
    }
}

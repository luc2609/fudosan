<?php

namespace App\Http\Requests;

class CheckDuplicateCustomer extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'kana_last_name' => 'required|string',
            'kana_first_name' => 'required|string',
            'phone' => 'required',
            // 'birthday' => 'required|before:today',
        ];
    }
}

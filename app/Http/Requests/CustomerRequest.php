<?php

namespace App\Http\Requests;

class CustomerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required|exists:session_customers,token',
            'postal_code' => 'required|exists:master_postal_codes,postal_code',
            'province' => 'required',
            'district' => 'required',
            'email' => 'required|email',
            'address' => 'required|string',
            'advertising_form_ids.*' => 'required|exists:master_advertising_forms,id',
            'advertising_form_ids' => 'array',
            'residence_year_id' => 'nullable|exists:master_residence_years,id',
            'budget' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'purchase_time' => 'date_format:Y-m',
            'memo' =>  'nullable|string',
            'contact_method_id' => 'nullable|exists:master_contact_methods,id',
            'purchase_purpose_ids.*' => 'nullable|exists:master_purchase_purposes,id',
            'purchase_purpose_ids' => 'array',
        ];
    }
}

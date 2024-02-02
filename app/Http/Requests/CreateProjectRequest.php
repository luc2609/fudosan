<?php

namespace App\Http\Requests;

class CreateProjectRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $priceRule = 'nullable|numeric|min:0';

        return [
            'property_id' => 'array|nullable',
            'property_id.*' => 'distinct|exists:properties,id,deleted_at,NULL',
            'customer_id' => 'array|required',
            'customer_id.*' => 'distinct|exists:customers,id,deleted_at,NULL',
            'division_id' => 'required|exists:divisions,id,deleted_at,NULL',
            'user_in_charge_id' => 'required|exists:users,id,deleted_at,NULL',
            'sub_user_in_charge_id' => 'nullable|exists:users,id,deleted_at,NULL',
            'relate_user_ids' => 'array|max:10',
            'relate_user_ids.*' => 'distinct|exists:users,id,deleted_at,NULL',
            'price' => $priceRule,
            'deposit_price' => $priceRule,
            'monthly_price' => $priceRule,
            'transaction_time' => 'nullable|date_format:Y-m',
            'advertising_web_ids.*' => 'distinct|numeric|exists:master_advertising_webs,id',
            'advertising_web_ids' => 'array',
            'documents.*' => 'mimes:csv,txt,pdf,docx,jpeg,png,jpg|max:' . MAX_UPLOAD_FILE_SIZE,
            'documents' => 'array|max:5',
            'description' => 'nullable|max:500',
            'type' => 'required|integer',
            'sale_purpose_ids.*' => 'distinct|numeric|exists:master_sale_purposes,id',
            'sale_purpose_ids' => 'array',
            'purchase_purpose_ids.*' => 'distinct|numeric|exists:master_purchase_purposes,id',
            'purchase_purpose_ids' => 'array',
        ];
    }
}

<?php

namespace App\Http\Requests;

class CheckDuplicateProperty extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'postal_code' => 'required|exists:master_postal_codes,postal_code',
            'address' => 'required',
            'properties_type_id' => 'nullable|exists:master_property_types,id',
        ];
    }
}

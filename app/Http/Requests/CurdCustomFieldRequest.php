<?php

namespace App\Http\Requests;


class CurdCustomFieldRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'custom_fields' => 'required|array',
            'pattern_type' => 'required|integer|between:1,2',
            'custom_fields.*.name' => 'required|string',
            'custom_fields.*.master_field_id' => 'required|integer|between:1,3',
            'custom_fields.*.note' => 'nullable|string',
            'custom_fields.*.is_required' => 'integer'
        ];
    }
}

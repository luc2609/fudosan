<?php

namespace App\Http\Requests;

use App\Rules\RailStationRule;

class 
CreatePropertyRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:' . MAX_UPLOAD_FILE_SIZE,
            'images.*' => 'image|mimes:jpeg,png,jpg|max:' . MAX_UPLOAD_FILE_SIZE,
            'images' => 'array|max:10',
            'name' => 'required|max:50',
            'construction_date' => 'date_format:Y-m',
            'postal_code' => 'required|exists:master_postal_codes,postal_code',
            'province' => 'required',
            'district' => 'required',
            'address' => 'required|max:50',
            'price' => 'required|numeric|min:0',
            'contract_type_id' => 'nullable|exists:master_property_contract_types,id',
            'properties_type_id' => 'nullable|exists:master_property_types,id',
            'land_area' => 'nullable|numeric|min:0',
            'total_floor_area' => 'nullable|numeric|min:0',
            'usage_ratio' => 'nullable|numeric|min:0|max:100',
            'empty_ratio' => 'nullable|numeric|min:0|max:100',
            'floor' => 'nullable|numeric|min:0|digits_between: 1,3',
            'building_structure_id' => 'nullable|exists:master_property_building_structures,id',
            'design' => 'nullable|max:15',
            'description' => 'nullable|max:500',
            'advertising_web_ids.*' => 'distinct|numeric|exists:master_advertising_webs,id',
            'advertising_web_ids' => 'array',
            'documents.*' => 'mimes:csv,txt,pdf,docx,jpeg,png,jpg|max:' . MAX_UPLOAD_FILE_SIZE,
            'documents' => 'array|max:5',
            'rail_stations' => ['array', 'max:3', new RailStationRule($this->rail_stations)]
        ];
    }
}

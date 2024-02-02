<?php

namespace App\Http\Requests;

class IndexRailRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'province_cd' => 'exists:master_provinces,cd'
        ];
    }
}

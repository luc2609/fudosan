<?php

namespace App\Http\Requests;

class ShowBrokerageFeeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'price' => 'required|numeric|min:0'
        ];
    }
}

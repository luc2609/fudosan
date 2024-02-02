<?php

namespace App\Http\Requests;

class CertificateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'degree_date' => 'required|before:tomorrow',
        ];
    }
}

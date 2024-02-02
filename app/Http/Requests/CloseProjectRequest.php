<?php

namespace App\Http\Requests;

class CloseProjectRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'close_status' => 'required|integer|min:0|max:4'
        ];
    }
}

<?php

namespace App\Http\Requests;

class UpdateCommentRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'content' => 'required|string',
        ];
    }
}

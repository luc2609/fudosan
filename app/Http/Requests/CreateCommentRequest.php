<?php

namespace App\Http\Requests;

class CreateCommentRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'content' => 'required|string',
        ];
    }
}

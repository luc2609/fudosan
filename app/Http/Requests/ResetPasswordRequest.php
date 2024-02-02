<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ResetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'new_password' => PASSWORD_RULE,
            're_new_password' => 'required|same:new_password',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $message = __('validation.error');

        if (isset($errors['new_password'])) {
            $message = __('message.password_invalid');
        } else if (isset($errors['re_new_password'])) {
            $message = __('message.re_password_invalid');
        }

        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'message' => $message,
                'data' => $errors,
            ],
            HTTP_BAD_REQUEST
        ));
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ImportUserCsvRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:' . MAX_UPLOAD_FILE_IMPORT,
            'password' => 'required|min:8|max:20',
            'role_id' => 'required|integer|between:3,4'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $message = __('validation.error');

        if (isset($errors['file'])) {
            $message = __('message.file_invalid');
        } else if (isset($errors['password'])) {
            $message = __('message.password_invalid');
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

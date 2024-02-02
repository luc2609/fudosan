<?php

namespace App\Http\Requests;


class ImportPropertyCsvRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:' . MAX_UPLOAD_FILE_IMPORT,
        ];
    }

    public function messages()
    {
        return [
            'file.mimes' => __('message.file_invalid')
        ];
    }
}

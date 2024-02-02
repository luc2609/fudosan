<?php

namespace App\Imports;

use App\Services\FileService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ValidateEmployeeImport extends FileService implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowArray = array_map('trim', $row->toArray());
        $arr = [];
        foreach ($rowArray as $key => $value) {
            $arr['row' . $key] = $value;
        };
        $validator = Validator::make(
            $arr,
            [
                'row' . 1 => 'required|string',
                'row' . 2 => 'required|string',
                'row' . 3 => 'required|string',
                'row' . 4 => 'required|string',
                'row' . 5 => 'nullable|string',
                'row' . 6 => 'email|required',
                'row' . 7 => 'nullable|string|exists:roles,name',
            ],
            [
                'row' . 1 . '.required' => 'first_nameは必須であり、タイプはstringです。',
                'row' . 2 . '.required' => 'last_nameは必須であり、タイプはstringです。',
                'row' . 3 . '.required' => 'kana_first_nameは必須であり、タイプはstringです。',
                'row' . 4 . '.required' => 'kana_last_nameは必須であり、タイプはstringです。',
                'row' . 6 . '.email' => 'メールは一意であり、メール形式である必要があります。',
                'row' . 7 . '.exists' => '下記のロールとなります スーパーアドミン, アドミン, マネージャー, 営業担当者,"。'
            ]
        );

        if ($validator->fails()) {
            $this->errors[$row->getIndex()] = $validator->errors()->messages();
        }
    }
}

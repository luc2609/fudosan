<?php

namespace App\Imports;

use App\Services\FileService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ValidateCustomerImport extends FileService implements OnEachRow
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
                'row' . 5 => 'required|email',
                'row' . 6 => 'required|before:today',
                'row' . 7 => 'required|string',
                'row' . 8 => 'required|exists:master_postal_codes,postal_code',
                'row' . 9 => 'nullable|string',

            ],
            [
                'row' . 1 . '.required' => 'first_nameは必須であり、タイプはstringです。',
                'row' . 2  . '.required' => 'last_nameは必須であり、タイプはstringです。',
                'row' . 3 . '.required' => 'kana_first_nameは必須であり、タイプはstringです。',
                'row' . 4 . '.required' => 'kana_last_nameは必須であり、タイプはstringです。',
                'row' . 5 . '.email' => 'メールは一意であり、メール形式である必要があります。',
                'row' . 7 . '.required' => '生年月日は必須であり',
                'row' . 8 . '.exists' => 'postal_codeは存在しません。',
                'row' . 9 . '.string' => '住所のタイプはstringです。',

            ],
        );
        if ($validator->fails()) {
            $this->errors[$row->getIndex()] = $validator->errors()->messages();
        }
    }
}

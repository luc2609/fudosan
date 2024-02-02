<?php

namespace App\Imports;

use App\Services\FileService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ValidatePropertyImport extends FileService implements OnEachRow
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
                'row' . 1 => 'required|max:50',
                'row' . 2 => 'required|string',
                'row' . 4 => 'required|numeric|min:0',
                'row' . 5 => 'required|string',
            ],
            [
                'row' . 1 . '.required' => '名前は先頭で、タイプはstringです。',
                'row' . 2 . '.required' => 'postal_codeは存在しません。',
                'row' . 4 . '.numeric' => '価格は数値で、最小値は0である必要があります',
                'row' . 5 . '.required' => '住所のタイプはstringです。',
            ],
        );
        if ($validator->fails()) {
            $this->errors[$row->getIndex()] = $validator->errors()->messages();
        }
    }
}

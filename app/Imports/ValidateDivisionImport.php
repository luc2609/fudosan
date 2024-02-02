<?php

namespace App\Imports;

use App\Services\FileService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ValidateDivisionImport extends FileService implements OnEachRow
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
                'row' . 1 => 'required'
            ],
        );
        if ($validator->fails()) {
            $this->errors[$row->getIndex()] = $validator->errors()->messages();
        }
    }
}

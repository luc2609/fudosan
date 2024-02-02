<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportMasterProvince implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $masterPronvinceList = [];

        $currentTime = date('Y-m-d H:i:s');

        foreach ($collection as $row) {
            array_push($masterPronvinceList, [
                'cd' => $row[1],
                'name' => $row[2],
                'eng_name' => $row[4],
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        }

        DB::table('master_provinces')->insert($masterPronvinceList);
    }

    public function startRow(): int
    {
        return 2;
    }
}

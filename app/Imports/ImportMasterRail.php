<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportMasterRail implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $masterRailList = [];

        $currentTime = date('Y-m-d H:i:s');

        foreach ($collection as $row) {
            array_push($masterRailList, [
                'province_cd' => $row[2],
                'cd' => $row[3],
                'name' => $row[4],
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        }

        DB::table('master_rails')->insert($masterRailList);
    }

    public function startRow(): int
    {
        return 2;
    }
}

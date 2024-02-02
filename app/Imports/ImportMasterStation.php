<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportMasterStation  implements ToCollection, WithStartRow, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $masterStationList = [];

        $currentTime = date('Y-m-d H:i:s');

        foreach ($collection as $row) {
            array_push($masterStationList, [
                'province_cd' => $row[0],
                'rail_cd' => $row[1],
                'cd' => $row[2],
                'name' => $row[3],
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        }

        DB::table('master_stations')->insert($masterStationList);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}

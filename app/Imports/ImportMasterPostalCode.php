<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportMasterPostalCode implements ToCollection, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $masterPostalCodeList = [];

        $currentTime = date('Y-m-d H:i:s');

        foreach ($collection as $row) {
            array_push($masterPostalCodeList, [
                'code' => $row[0],
                'city_code' => $row[1],
                'postal_code' => $row[2],
                'kana_province' => $row[3],
                'kana_district' => $row[4],
                'kana_street' => $row[5],
                'province' => $row[6],
                'district' => $row[7],
                'street' => $row[8],
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        }

        DB::table('master_postal_codes')->insert($masterPostalCodeList);
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}

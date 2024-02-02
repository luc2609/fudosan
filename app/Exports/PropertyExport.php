<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PropertyExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    public $properties;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function collection()
    {
        return $this->properties;
    }

    public function map($property): array
    {
        $priceFormat = (string) number_format($property->price) . '円';
        $property->customFields->pluck('url')->all();
        $files = $property->customFields->pluck('name')->all();

        $filesName = '';
        foreach($files as $key => $file) {
            $filesName .= env('AWS_URL') . '/' . $file;

            if ($key !== array_key_last($files)) {
                $filesName .= '、';
            }
        }

        $customFields = $property->customFields->pluck('value', 'name')->all();
        $customFieldsName = '';
        foreach($customFields as $kField => $cField) {
            $customFieldsName .= $kField . ': ' . $cField;

            if ($kField !== array_key_last($customFields)) {
                $customFieldsName .= '、';
            }
        }

        return [
            $property->name,
            $property->postal_code,
            $property->province . $property->district . $property->address,
            $priceFormat,
            $property->properties_type,
            $property->contract_type,
            $property->floor,
            $property->construction_date,
            $property->land_area . $property->land_area_ja,
            $property->total_floor_area . $property->total_floor_area_ja,
            $property->design,
            $property->property_stations,
            '',
            $property->description,
            $filesName,
            $customFieldsName
        ];
    }

    public function headings(): array
    {
        return [
            '物件名',
            '郵便番号',
            '住所',
            '価格 (円)',
            '物件種類',
            '契約形態',
            '階建て',
            '建築日',
            '建物構造',
            '土地面積',
            '延床面積',
            '間取り',
            '徒歩',
            '備考',
            'ファイル',
            '動的な項目'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

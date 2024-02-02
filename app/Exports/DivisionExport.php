<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DivisionExport implements FromCollection, WithMapping, WithHeadings
{
    public $divisions;

    public function __construct($divisions)
    {
        $this->divisions = $divisions;
    }

    public function collection()
    {
        return $this->divisions;
    }

    public function map($division): array
    {
        return [
            $division->id,
            $division->name,
            (string) $division->manager_count,
            (string) $division->user_count,
            (string) $division->project_count,
            (string) $division->project_in_progress_count,
            (string) $division->project_success_count,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            '部署名',
            'マネージャー数',
            '営業担当者数',
            '案件',
            '進行中案件',
            '終了案件'
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanyExport implements FromCollection, WithMapping, WithHeadings
{
    public $companies;

    public function __construct($companies)
    {
        $this->companies = $companies;
    }

    public function collection()
    {
        return $this->companies;
    }

    public function map($companies): array
    {
        return [
            $companies->id,
            $companies->name,
            $companies->address,
            $companies->phone,
            $companies->total_managers,
            $companies->total_staff
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            '会社名',
            '住所',
            '電話番号',
            'マネージャー数',
            '社員数'
        ];
    }
}

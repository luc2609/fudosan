<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection, WithMapping, WithHeadings
{
    public $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->username,
            $user->furigana,
            $user->role_name,
            $user->email,
            $user->phone,
            $user->all_division_name,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            '氏名',
            '役職',
            'メール',
            'フリガナ',
            '電話番号',
            '管理部署'
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManagerExport implements FromCollection, WithMapping, WithHeadings
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
        $positionName = $user->position_name;
        $division =  $user->divisions->pluck('name')->all();
        $divisionName = implode('、', $division);
        $certificate =  $user->certificates->pluck('name')->all();
        $certificateName = implode('、', $certificate);
        return [
            $user->username,
            $user->furigana,
            $user->phone,
            $user->email,
            $divisionName,
            $positionName,
            $user->phone,
            $user->commission_rate,
            $certificateName,
            $user->role_name,
            $user->last_login
        ];
    }

    public function headings(): array
    {
        return [
            '氏名',
            'フリガナ',
            '電話番号',
            'Eメール',
            '部署',
            '役職',
            '歩合給',
            '歩合給',
            '資格',
            '権限',
            '最終ログイン'
        ];
    }
}

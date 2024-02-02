<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithMapping, WithHeadings
{
    public $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {
        return $this->customers;
    }

    public function map($customer): array
    {
        date_default_timezone_set('Asia/Tokyo');
        $date = $customer->birthday;
        $diff = date_diff(date_create(), date_create($date));
        $age = $diff->format('%Y');

        if ($customer->residence_years_min == null) {
            $residenceYear = '<' . $customer->residence_years_max;
        } else if ($customer->residence_years_max == null) {
            $residenceYear = '>' . $customer->residence_years_max;
        } else {
            $residenceYear = $customer->residence_years_min . ' - ' . $customer->residence_years_max;
        }
        $purchasePurposes = $customer->purchasePurposes->pluck('purchase_purpose')->all();
        $purchasePurposesName = implode('、', $purchasePurposes);
        $advertisingForms = $customer->advertisingForms->pluck('name')->all();
        $advertisingFormsName = implode('、', $advertisingForms);
        $customFields = $customer->customFields->pluck('value', 'name')->all();
        $customFieldsName = '';
        foreach($customFields as $kField => $cField) {
            $customFieldsName .= $kField . ': ' . $cField;

            if ($kField !== array_key_last($customFields)) {
                $customFieldsName .= '、';
            }
        }

        return [
            $customer->last_name . ' ' .  $customer->first_name,
            $customer->kana_first_name . $customer->kana_last_name,
            $customer->birthday . ' (' . $age . '歳)',
            $customer->phone,
            $customer->email,
            $customer->contact_methods,
            $customer->postal_code,
            $customer->province .  $customer->district .  $customer->address,
            $purchasePurposesName,
            $customer->deposit,
            $customer->budget,
            date_format($customer->created_at,"Y年m月d日"),
            $customer->purchase_time,
            $residenceYear,
            $advertisingFormsName,
            $customer->memo,
            $customFieldsName
        ];
    }

    public function headings(): array
    {
        return [
            'お名前',
            'フリガナ',
            '生年月日',
            '電話番号',
            'メール',
            '連絡方法',
            '郵便番号',
            '現住所 ',
            '購入目的',
            '頭金',
            'ご予算',
            '登録日',
            '購入時期',
            '現居住年数',
            '会社を知ったきっかけ',
            '備考',
            '動的な項目'
        ];
    }
}

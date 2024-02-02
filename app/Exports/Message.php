<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class Message implements FromView , WithColumnFormatting
{
     use Exportable;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function view(): View
    {
        $data = $this->data;
        return view('exports.ListFormResult', compact('data'));
    }

    public function columnFormats(): array
    {
        return [
            // 'M' => '#,##0',
            // 'I' => '#,##0'
        ];
    }
}

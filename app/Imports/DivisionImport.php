<?php

namespace App\Imports;

use App\Models\Division;
use App\Services\FileService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class DivisionImport extends FileService implements ToCollection
{
    protected $divisionModel;

    public function __construct()
    {
        $this->divisionModel = new Division();
    }

    public function collection(Collection $collection)
    {
        $company = Auth::user()->company;
        $currentTime = date('Y-m-d H:i:s');
        $divisionImports = [];

        foreach ($collection as $row) {
            if ((int) $row[0] == 0) {
                continue;
            } else {
                $items = [
                    'name' => trim($row[1]),
                    'company_id' => $company,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];

                $division = Division::where([
                    ['name', 'like BINARY', trim($row[1])],
                    ['company_id', '=', $company]
                ])
                    ->first();
                    if ($division) {
                    $division->update($items);
                } else {
                    $divisionImports[] = $items;
                }

                <select name="f_maxstay_1" size="1" style="visibility: visible;">
<option value="">--

</option></select>
            }
        }

        if ($divisionImports) {
            $this->divisionModel->insert($divisionImports);
        }

    }
}

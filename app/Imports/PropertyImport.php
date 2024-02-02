<?php

namespace App\Imports;

use App\Models\MasterPostalCode;
use App\Models\MasterPropertyType;
use App\Models\Property;
use App\Services\FileService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class PropertyImport extends FileService implements ToCollection
{
    protected $propertyModel;
    protected $masterPropertyTypeModel;

    public function __construct()
    {
        $this->propertyModel = new Property();
        $this->masterPropertyTypeModel = new MasterPropertyType();
    }

    public function collection(Collection $collection)
    {
        $user = Auth::user();
        $companyId = $user->company;
        $userId = $user->id;
        $currentTime = date('Y-m-d H:i:s');
        $propertyImports = [];
        foreach ($collection as $row) {
            if ((int) $row[0] == 0) {
                continue;
            } else {
                $propertiesTypeId = $this->masterPropertyTypeModel->where('name', 'like BINARY', trim($row[5]))->first()->id;
                $postalCode = MasterPostalCode::where('postal_code', (int)trim($row[2]))->first();
                $province = $postalCode->province;
                $district = $postalCode->district;
                if (trim($row[3]) != null) {
                    $address = trim($row[3]);
                } else {
                    $address = trim($row[2]) . ' ' . $district . ' ' . $province;
                }
                $propertyImports[] = [
                    'name' => trim($row[1]),
                    'postal_code' => trim($row[2]),
                    'province' => $province,
                    'district' => $district,
                    'address' => $address,
                    'price' => trim($row[4]),
                    'properties_type_id' => $propertiesTypeId,
                    'status' => APPROVED_PROPERTY,
                    'company_id' => $companyId,
                    'created_id' => $userId,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];
            }
        }

        if ($propertyImports) {
            $this->propertyModel->insert($propertyImports);
        }
    }
}

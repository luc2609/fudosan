<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\MasterPostalCode;
use App\Repositories\Customer\CustomerEloquentRepository;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Services\FileService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class CustomerImport extends FileService implements ToCollection
{
    protected $customerModel;
    protected $postalCodeModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
        $this->postalCodeModel = new MasterPostalCode();
    }

    public function collection(Collection $collection)
    {
        $user = Auth::user();
        $companyId = $user->company;
        $userId = $user->id;
        $currentTime = date('Y-m-d H:i:s');
        $customerImports = [];
        foreach ($collection as $row) {
            if ((int) $row[0] == 0) {
                continue;
            } else {
                $postalCode = MasterPostalCode::where('postal_code', (int)trim($row[8]))->first();
                $province = $postalCode->province;
                $district = $postalCode->district;
                if (trim($row[9]) != null) {
                    $address = trim($row[9]);
                } else {
                    $address = trim($row[8]) . ' ' . $district . ' ' . $province;
                }
                $items = [
                    'first_name' => trim($row[1]),
                    'last_name' => trim($row[2]),
                    'kana_first_name' => trim($row[3]),
                    'kana_last_name' => trim($row[4]),
                    'email' => trim($row[5]),
                    'birthday' => trim($row[6]),
                    'phone' => trim($row[7]),
                    'postal_code' => trim($row[8]),
                    'province' => $province,
                    'district' => $district,
                    'address' => $address,
                    'company_id' => $companyId,
                    'create_by_id' => $userId,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];

                $customerRepo = new CustomerEloquentRepository();
                $customer = $customerRepo->findCustomerExist($companyId, trim($row[7]));

                if ($customer) {
                    $customer->update($items);
                } else {
                    $customerImports[] = $items;
                }
            }
        }
        if ($customerImports) {
            $this->customerModel->insert($customerImports);
        }

    }
}

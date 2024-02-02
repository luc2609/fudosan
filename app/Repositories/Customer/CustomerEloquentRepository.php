<?php

namespace App\Repositories\Customer;

use App\Models\Customer;
use App\Models\SessionCustomer;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class CustomerEloquentRepository extends BaseEloquentRepository implements CustomerRepositoryInterface
{
    public function getModel()
    {
        return Customer::class;
    }

    // List customer in company
    public function listInCompany($companyId, $params)
    {
        $listCustomer = $this->_model->where('customers.company_id', $companyId)
            ->select([
                'customers.*',
                'master_residence_years.min as residence_years_min',
                'master_residence_years.max as residence_years_max',
                'master_contact_methods.contact_method as contact_methods',
            ])
            ->leftJoin(
                'master_residence_years',
                'customers.residence_year_id',
                'master_residence_years.id'
            )
            ->leftJoin(
                'master_contact_methods',
                'customers.contact_method_id',
                'master_contact_methods.id'
            )
            ->with('advertisingForms', 'purchasePurposes', 'projects', 'customFields');

        if (isset($params['username'])) {
            $name = _trimSpace($params['username']);
            $listCustomer->where(DB::raw('concat(last_name," ",first_name)'), 'like BINARY', "%" . $name . "%");
        }

        if (isset($params['sort_by'])) {
            $sortBy = $params['sort_by'];
            $sortType = strtoupper($params['sort_type']) ?? 'ASC';
            if ($sortBy == 'birthday') {
                switch ($sortType) {
                    case 'DESC':
                        $sortType = 'ASC';
                        break;
                    case 'ASC':
                    default:
                        $sortType = 'DESC';
                        break;
                }
            }

            switch ($sortBy) {
                case 'residence_year':
                    $listCustomer->orderBy('residence_year_id', $sortType);
                    break;
                case 'birthday':
                    $listCustomer->orderBy('birthday', $sortType);
                    break;
                case 'created_at':
                    $listCustomer->orderBy('created_at', $sortType);
                    break;
                case 'username':
                    $listCustomer->orderBy(DB::raw('concat(last_name," ",first_name)'), $sortType);
                    break;
                case 'address':
                    $listCustomer->orderBy(DB::raw('address'), $sortType);
                    break;
                case 'id':
                    $listCustomer->orderBy(DB::raw('id'), $sortType);
                    break;
                default:
                    $listCustomer->orderBy('id', 'DESC');
                    break;
            }
        } else {
            $listCustomer->orderBy('id', 'DESC');
        }
        return $listCustomer;
    }

    // Get data by attributes
    public function getByAttributes($attributes)
    {
        $query = $this->_model->select('*');

        if (empty($attributes)) {
            return $query->all();
        }

        if (isset($attributes['last_name'])) {
            $query->where('last_name', $attributes['last_name']);
        }

        if (isset($attributes['first_name'])) {
            $query->where('first_name', $attributes['first_name']);
        }

        // if (isset($attributes['birthday'])) {
        //     $query->where('birthday', $attributes['birthday']);
        // }

        if (isset($attributes['phone'])) {
            $query->where('phone', $attributes['phone']);
        }

        if (isset($attributes['status'])) {
            $query->where('status', $attributes['status']);
        }

        if (isset($attributes['company_id'])) {
            $query->where('company_id', $attributes['company_id']);
        }
        return $query->get();
    }

    // Show customer
    public function showCustomer($params, $companyId, $id)
    {
        $customer = $this->_model
            ->select([
                'customers.*',
                'master_residence_years.min as residence_years_min',
                'master_residence_years.max as residence_years_max',
                'master_contact_methods.contact_method as contact_methods',
            ])
            ->leftJoin(
                'master_residence_years',
                'customers.residence_year_id',
                'master_residence_years.id'
            )
            ->leftJoin(
                'master_contact_methods',
                'customers.contact_method_id',
                'master_contact_methods.id'
            )
            ->where('customers.id', $id)
            ->with('advertisingForms', 'purchasePurposes', 'projects', 'customFields')

            ->first()->toArray();
        $nextBackCustomer = $this->nextBackCustomer($params, $id, $companyId);
        return array_merge($customer, $nextBackCustomer);
    }

    public function getSessionCustomer($token)
    {
        return SessionCustomer::select(
            'session_customers.last_name',
            'session_customers.first_name',
            'session_customers.kana_last_name',
            'session_customers.kana_first_name',
            'session_customers.birthday',
            'session_customers.phone',
            'session_customers.gender',
            'session_customers.bearer_token'
        )
            ->where('token', $token)
            ->first();
    }

    public function deleteSessionCustomer($token)
    {
        $sessionCustomer = SessionCustomer::where('token', $token);
        if ($sessionCustomer) {
            $sessionCustomer->delete();
            return true;
        }
        return false;
    }

    public function nextBackCustomer($params, $id, $companyId)
    {
        $customers = $this->listInCompany($companyId, $params)->pluck('id');
        $arrayCustomer = $customers->toArray();
        $nextCustomer = null;
        $backCustomer = null;
        if (in_array($id, $arrayCustomer)) {
            $customerIndex =  $customers->search($id);
            if ($customerIndex != 0) {
                $key = $customerIndex - POSITION;
                $backCustomer = $arrayCustomer[$key];
            }

            if ((array_key_last($arrayCustomer)) != $customerIndex) {
                $key = $customerIndex + POSITION;
                $nextCustomer = $arrayCustomer[$key];
            }
        }
        return [
            'next_id' => $nextCustomer,
            'back_id' => $backCustomer
        ];
    }

    public function findCustomerExist($companyId, $phone = null, $mail = null) {
        $customer = $this->_model->where('company_id', $companyId);
        if ($phone || $mail) {
            $customer = $customer->where('phone', $phone)
                                ->orWhere('email', $mail);
        }
        return $customer->first();
    }
}

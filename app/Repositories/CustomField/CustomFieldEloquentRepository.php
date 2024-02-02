<?php

namespace App\Repositories\CustomField;

use App\Models\CustomField;
use App\Repositories\Base\BaseEloquentRepository;

class CustomFieldEloquentRepository extends BaseEloquentRepository implements CustomFieldRepositoryInterface
{
    public function getModel()
    {
        return CustomField::class;
    }

    public function paramsCustomField($patternType, $customField, $companyId)
    {
        if ($customField['master_field_id'] == STRING_TYPE) {
            $length = STRING_LENGHT;
        } else if ($customField['master_field_id'] == TEXT_TYPE) {
            $length = TEXT_LENGHT;
        } else {
            $length = null;
        }
        if ($patternType == CUSTOMER) {
            $pattern = 'customers';
            $patternType = CUSTOMER;
        } else {
            $pattern = 'properties';
            $patternType = PROPERTY;
        }
        $attributes = [
            'company_id' => $companyId,
            'length' => $length,
            'pattern' => $pattern,
            'pattern_type' => $patternType,
            'name' => $customField['name'],
            'master_field_id' => $customField['master_field_id'],
            'note' => $customField['note'] ?? null,
            'is_required' => $customField['is_required'] ?? 0,
        ];
        return $attributes;
    }

    public function getListCustomField($companyId, $type)
    {
        return $this->_model->where('company_id', $companyId)
            ->leftJoin('companies', 'companies.id', 'custom_fields.company_id')
            ->leftJoin('master_fields', 'master_fields.id', 'custom_fields.master_field_id')
            ->where('pattern_type', $type)
            ->select('custom_fields.*', 'companies.name as company', 'master_fields.type');
    }

    public function checkExists($customField, $companyId, $patternType)
    {
        return $this->_model->where([
            'company_id' => $companyId,
            'pattern_type' => $patternType,
            'name' => $customField['name'],
            'master_field_id' => $customField['master_field_id']
        ])->exists();
    }
}

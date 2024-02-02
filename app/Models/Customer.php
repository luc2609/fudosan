<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;

class Customer extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $guarded = ['id'];

    public function advertisingForms()
    {
        return $this->belongsToMany(MasterAdvertisingForm::class, 'customer_advertising_forms', 'customer_id', 'advertising_form_id')
            ->select(
                'master_advertising_forms.id',
                'master_advertising_forms.name',
                'master_advertising_forms.status',
                'customer_advertising_forms.note_other',
                'customer_advertising_forms.advertising_form_id'
            );
    }

    public function getPurchaseTimeAttribute()
    {
        if (!is_null($this->attributes['purchase_time'])) {
            return date('Y-m', strtotime($this->attributes['purchase_time']));
        }

        return null;
    }

    public function purchasePurposes()
    {
        return $this->belongsToMany(MasterPurchasePurpose::class, 'customer_purchase_purposes', 'customer_id', 'purchase_purpose_id')
            ->select(
                'master_purchase_purposes.id',
                'master_purchase_purposes.purchase_purpose',
                'master_purchase_purposes.status',
                'customer_purchase_purposes.note_other',
                'customer_purchase_purposes.purchase_purpose_id'
            );
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_customers')
            ->whereNull('project_customers.deleted_at')->withTimestamps();
    }

    public function customFields()
    {
        return $this->belongsToMany(CustomField::class, 'customer_custom_values', 'customer_id', 'custom_field_id')
            ->select('customer_custom_values.id', 'custom_fields.name', 'customer_custom_values.value', 'master_fields.type')
            ->leftJoin('master_fields', 'master_fields.id', 'custom_fields.master_field_id');
    }

    public function customerCustomValues()
    {
        return $this->hasMany(CustomerCustomValue::class);
    }
}

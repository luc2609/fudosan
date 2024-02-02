<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;


class CustomField extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $guarded = ['id'];
    protected $appends = ['is_used'];

    public function getIsUsedAttribute()
    {
        $customFieldId =  $this->id;
        $propertyCustomValue = PropertyCustomValue::where('custom_field_id', $customFieldId)->count();
        $customerCustomValue = CustomerCustomValue::where('custom_field_id', $customFieldId)->count();
        if ($propertyCustomValue > 0 || $customerCustomValue > 0) {
            return true;
        }
        return false;
    }
}

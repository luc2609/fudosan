<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Time\UnixTimestampSerializable;

class Property extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $fillable = [
        'avatar', 'name',
        'construction_date',
        'postal_code',
        'province',
        'district',
        'address',
        'price',
        'contract_type_id',
        'properties_type_id',
        'land_area',
        'total_floor_area',
        'usage_ratio',
        'empty_ratio',
        'floor',
        'building_structure_id',
        'design',
        'description',
        'status',
        'created_id',
        'created_name',
        'company_id'
    ];

    protected $appends = ['land_area_ja', 'total_floor_area_ja', 'first_station'];

    public function propertyFiles()
    {
        return $this->hasMany(PropertyFile::class);
    }

    public function images()
    {
        return $this->propertyFiles()->where('property_files.type', IMAGE_FILE_TYPE);
    }

    public function documents()
    {
        return $this->propertyFiles()->where('property_files.type', DOCUMENT_FILE_TYPE);
    }

    public function propertyStations()
    {
        return $this->hasMany(PropertyStation::class);
    }

    public function getFirstStationAttribute()
    {
        return $this->propertyStations()->first();
    }

    public function getAvatarAttribute()
    {
        if ($this->attributes['avatar']) {
            return Storage::disk('s3')->url($this->attributes['avatar']);
        }
        
        return null;
    }

    public function getLandAreaJaAttribute()
    {
        if (!is_null($this->land_area)) {
            return round(0.3025 * $this->land_area, 2);
        }

        return null;
    }

    public function getTotalFloorAreaJaAttribute()
    {
        if (!is_null($this->total_floor_area)) {
            return round(0.3025 * $this->total_floor_area, 2);
        }

        return null;
    }

    public function getConstructionDateAttribute()
    {
        if (!is_null($this->attributes['construction_date'])) {
            return date('Y-m', strtotime($this->attributes['construction_date']));
        }

        return null;
    }

    public function getPriceAttribute()
    {
        return round($this->attributes['price']);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_properties')
            ->whereNull('project_properties.deleted_at')->withTimestamps();
    }

    public function customFields()
    {
        return $this->belongsToMany(CustomField::class, 'property_custom_values', 'property_id', 'custom_field_id')
            ->select('property_custom_values.id', 'custom_fields.name', 'property_custom_values.value', 'master_fields.type')
            ->leftJoin('master_fields', 'master_fields.id', 'custom_fields.master_field_id');
    }

    public function propertyCustomValues()
    {
        return $this->hasMany(PropertyCustomValue::class);
    }
}

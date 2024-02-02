<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;

class PropertyStation extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $fillable = ['property_id', 'rail_cd', 'station_cd', 'on_foot'];

    protected $appends = ['rail_name', 'station_name'];

    public function rails()
    {
        return $this->hasMany(MasterRail::class, 'cd', 'rail_cd');
    }

    public function stations()
    {
        return $this->hasMany(MasterStation::class, 'cd', 'station_cd');
    }

    public function getRailNameAttribute()
    {
        return $this->rails()->first()->name;
    }

    public function getStationNameAttribute()
    {
        return $this->stations()->first()->name;
    }
}

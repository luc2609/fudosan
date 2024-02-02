<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Time\UnixTimestampSerializable;

class File extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $fillable = [
        'name',
        'url',
        'type'
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_files');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_files');
    }

    public function getUrlAttribute()
    {
        if ($this->attributes['url']) {
            return Storage::disk('s3')->url($this->attributes['url']);
        }

        return null;
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_files');
    }
}

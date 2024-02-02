<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Time\UnixTimestampSerializable;

class SubCalendarFile extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $guarded = ['id'];

    public function getUrlAttribute()
    {
        if ($this->attributes['url']) {
            return Storage::disk('s3')->url($this->attributes['url']);
        }

        return null;
    }
}

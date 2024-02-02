<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;

class Certificate extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDegreeDateAttribute()
    {
        if (!is_null($this->attributes['degree_date'])) {
            return date('Y-m', strtotime($this->attributes['degree_date']));
        }

        return null;
    }
}

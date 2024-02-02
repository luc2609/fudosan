<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserDeviceToken extends Model
{
    use HasFactory, UnixTimestampSerializable, SoftDeletes;
    protected $fillable = [
        'user_id',
        'device_token',
        'deleted_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class UserColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'color_web',
        'color_app',
    ];
}

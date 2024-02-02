<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;


class CalendarUser extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $guarded = ['id'];
}

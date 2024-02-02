<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class PasswordReset extends Model
{
    public $timestamps = false;
    use HasFactory, UnixTimestampSerializable;

    protected $fillable = [
        'email',
        'token'
    ];
}

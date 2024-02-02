<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class AuthAccessToken extends Model
{
    use HasFactory, UnixTimestampSerializable;
    protected $table = 'oauth_access_tokens';
}

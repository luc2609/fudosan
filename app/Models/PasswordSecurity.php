<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class PasswordSecurity extends Model
{
    use HasFactory, UnixTimestampSerializable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'security_enable',
        'type',
        'token',
        'active',
        'incorrect',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;

class Contact extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;

    protected $fillable = [
        'user_id',
        'type_id',
        'subject',
        'contents',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(MasterContactReason::class);
    }
}

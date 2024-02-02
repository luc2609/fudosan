<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class CustomerPurchasePurpose extends Model
{
    use HasFactory, UnixTimestampSerializable;
    protected $guarded = ['id'];
}

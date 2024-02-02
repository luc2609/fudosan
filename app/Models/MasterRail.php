<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function stations()
    {
        return $this->hasMany(MasterStation::class, 'rail_cd', 'cd');
    }
}

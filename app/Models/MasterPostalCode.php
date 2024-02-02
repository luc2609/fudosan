<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPostalCode extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getStreetAttribute()
    {
        if ($this->attributes['street'] == EMPTY_STREET) {
            return null;
        }

        return $this->attributes['street'];
    }
}

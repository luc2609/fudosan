<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Time\UnixTimestampSerializable;

class ProjectPhase extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $fillable = ['project_id', 'm_phase_project_id', 'preliminary_test_date', 'actual_test_date', 'status', 'created_name', 'updated_name'];

    public function calendars()
    {
        return $this->hasMany(Calendar::class, 'project_phase_id', 'id');
    }
}

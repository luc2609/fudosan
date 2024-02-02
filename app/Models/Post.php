<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Time\UnixTimestampSerializable;

class Post extends Model
{
    use HasFactory, SoftDeletes, UnixTimestampSerializable;
    protected $fillable = ['project_id', 'created_id', 'title'];
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }
}

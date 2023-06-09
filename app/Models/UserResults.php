<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResults extends Model
{
    use HasFactory;

    public function employee()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }
}

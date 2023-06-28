<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillsEvaluationResult extends Model
{
    use HasFactory;
    protected $table = 'skills_evaluation_result';

    protected $guarded = ['id'];
    // protected $fillable = [];

    public function employee()
    {
        return $this->belongsTo('App\Models\User', 'employee_id', 'id');
    }
    public function examiner()
    {
        return $this->belongsTo('App\Models\User', 'examiner_id', 'id');
    }
}

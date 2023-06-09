<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function answers()
    {
        return $this->hasMany('App\Models\Answer', 'question_id', 'id')->inRandomOrder();
    }
}

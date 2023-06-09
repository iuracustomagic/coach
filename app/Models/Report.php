<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'reports';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function quiz()
    {
        return $this->belongsTo('App\Models\Quiz', 'quiz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getCourseAttribute()
    {
        if($this->quiz){
            return  $this->quiz->course->name;
        } else {
            return '-';
        }
    }

    public function getLessonAttribute()
    {
        if($this->quiz){
            return $this->quiz->lesson->name;
        } else {
            return '-';
        }
    }
    
    public function getAvgTimeFormattedAttribute()
    {
        $seconds = round($this->avg_time);
        return sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}

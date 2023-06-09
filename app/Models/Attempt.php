<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'attempts';
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
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

    /*public function getUserCompanyAttribute()
    {
        $companies = "";
        if(!empty($this->user->companies)){
            foreach($this->user->companies as $company){
                $companies .= $company->name;
            }
        }
        return $companies;
    }*/

    public function getUserDivisionAttribute()
    {
        $divisions = "";
        if(!empty($this->user->divisions)){
            foreach($this->user->divisions as $division){
                $divisions .= $division->name;
            }
        }
        return $divisions;
    }

    public function getUserBranchAttribute()
    {
        $branches = "";
        if(!empty($this->user->branches)){
            foreach($this->user->branches as $branch){
                $branches .= $branch->address;
            }
        }
        return $branches;
    }

    public function getStartedAttribute()
    {
        $timestamp = date_create($this->created_at);
        $date = date_format($timestamp,'Y-m-d');
        $time = date_format($timestamp,'H:i');
        return  $date . " " . $time;
    }

    public function getFinishedAttribute()
    {
        $timestamp = date_create($this->updated_at);
        $date = date_format($timestamp,'Y-m-d');
        $time = date_format($timestamp,'H:i');
        return  $date . " " . $time;
    }

    public function getDurationAttribute()
    {
        $start = date_create($this->created_at);
        $end   = date_create($this->updated_at);
        $duration = date_diff($start, $end);
        return $duration->format('%h h. %i min. %s sec.');
    }
}

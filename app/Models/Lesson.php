<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'lessons';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'video' => 'array',
        'gallery' => 'array'
    ];

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

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }

    public function quiz()
    {
        return $this->hasOne('App\Models\Quiz', 'lesson_id', 'id');
    }

    public function passedByUser()
    {
        if($this->quiz === null){
            // Handle cases when there is no quiz
            return true;
        }

        $attempt = \App\Models\Attempt::where([
            'quiz_id' => $this->quiz->id, 
            'user_id' => backpack_user()->id, 
            'status' => 'PASSED'
            ])->first();
        return !empty($attempt) ? true : false;
    }

    public function prevPassedByUser()
    {
        $lesson = $this::where([
            'course_id' => $this->course->id,
            'sort_order' => $this->sort_order - 1])->first();
        if($lesson){
            return $lesson->passedByUser();
        } else {
            // Case when there is no prev lesson
            return true;
        }
    }

    public function nextLesson()
    {
        $lesson = $this::where([
            'course_id' => $this->course->id,
            'sort_order' => $this->sort_order + 1])->first();
        if($lesson){
            return $lesson->id;
        } else {
            // Case when there is no next lesson
            return null;
        }
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

    public function setSortOrderAttribute($value)
    {
        if(null == $value){
            $lessons = self::all()->count();
            $this->attributes['sort_order'] = $lessons + 1;
        } else {
            if($value < 1){
                $value = 1;
            }
            $this->attributes['sort_order'] = $value;
        }
    }

    /*public function setVideoAttribute($value)
    {
        $attribute_name = "video";
        $disk = "public";
        $lessonNr = $this->id ?? self::where('course_id', $this->course->id)->count() + 1;
        $destination_path = "courses/course_".$this->course->id."/lesson_".$lessonNr;

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);

    // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }*/
}

<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'courses';
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

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            Storage::delete(Str::replaceFirst('storage/','public/', $obj->image));
        });
    }


    public static function isAvailable($course_id)
    {
        $pivot = \App\Models\UserAvailableCourses::where(['user_id' => backpack_user()->id, 'course_id' => $course_id])->first();
        return !empty($pivot);
    }

    public function isPassed($employee_id)
    {
        $result = \App\Models\UserResults::where(['user_id' => $employee_id, 'course_id' => $this->id])->first();
        if(empty($result)){
            return false;
        }
        return (bool) $result->course_is_passed;
    }

    public static function finalQuiz($course_id)
    {
        return \App\Models\Quiz::where(['course_id' => $course_id, 'is_final' => 1])->first();
    }

    public function getProfessionsList()
    {
        if($this->professions->count() === 0){
            return null;
        }
        return $this->professions->count() > 1 ? $this->professions[0]->name . "[...]" : $this->professions[0]->name;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function companies()
    {
        return $this->belongsToMany('App\Models\Company','course_companies')->using(\App\Models\CourseCompanies::class);
    }

    public function divisions()
    {
        return $this->belongsToMany('App\Models\Division','course_divisions')->using(\App\Models\CourseDivisions::class);
    }

    public function lessons()
    {
        return $this->hasMany('App\Models\Lesson', 'course_id', 'id')->orderBy('lessons.sort_order','ASC');
    }

    /*public function profession()
    {
        return $this->belongsTo('App\Models\Profession', 'profession_id', 'id');
    }*/

    public function professions()
    {
        return $this->belongsToMany('App\Models\Profession', 'course_professions')->using(\App\Models\CourseProfessions::class);
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
            $courses = self::all()->count();
            $this->attributes['sort_order'] = $courses + 1;
        } else {
            if($value < 1){
                $value = 1;
            }
            $this->attributes['sort_order'] = $value;
        }
    }

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        // or use your own disk, defined in config/filesystems.php
//        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/courses";

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            Storage::delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {
            // 0. Make the image
            $image = Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';

            // 2. Store the image on disk.
            Storage::put($destination_path.'/'.$filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            Storage::delete(Str::replaceFirst('storage/','public/', $this->{$attribute_name}));

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', 'storage/', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path.'/'.$filename;
        }
//        elseif (!empty($value)) {
//            // if value isn't empty, but it's not an image, assume it's the model value for that attribute.
//            $this->attributes[$attribute_name] = $this->{$attribute_name};
//        }
    }
}

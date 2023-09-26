<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use CrudTrait;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'idnp',
        'name',
        'business_phone',
        'personal_phone',
        'email',
        'password',
        'birthday',
        'image',
        //'division_id',
        //'branch_id',
        'profession_id',
        'supervisor_id',
        'created_by',
        'edited_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'files' => 'array'
    ];

    /*public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }*/

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'user_id', 'id');
    }

    public function results()
    {
        return $this->hasMany('App\Models\UserResults', 'user_id', 'id');
    }

    public function companies()
    {
        return $this->belongsToMany('App\Models\Company','user_companies')->using(\App\Models\UserCompanies::class);
    }

    /*public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id');
    }*/

    public function divisions()
    {
        return $this->belongsToMany('App\Models\Division','user_divisions')->using(\App\Models\UserDivisions::class);
    }

    public function getDivisionsList()
    {
        if($this->divisions->count() === 0){
            return null;
        }
        return $this->divisions->count() > 1 ? $this->divisions[0]->name . "[...]" : $this->divisions[0]->name;
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function editedBy()
    {
        return $this->belongsTo('App\Models\User', 'edited_by', 'id');
    }

    /*public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }*/

    public function getLocalityName()
    {
        $locality = "";
        if($this->branches->count() > 0){
            foreach($this->branches as $branch){
                $locality .= $branch->locality->name;
            }
        }
        return $locality;
    }

    public function branches()
    {
        return $this->belongsToMany('App\Models\Branch','user_branches')->using(\App\Models\UserBranches::class);
    }

    public function getBranchesList()
    {
        if($this->branches->count() === 0){
            return null;
        }
        return $this->branches->count() > 1 ? $this->branches[0]->name . "[...]" : $this->branches[0]->name;
    }

    public function profession()
    {
        return $this->belongsTo('App\Models\Profession', 'profession_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\User', 'supervisor_id', 'id');
    }

    public function subordinates()
    {
        return $this->hasMany('App\Models\User', 'supervisor_id', 'id');
    }

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course','user_available_courses')->using(\App\Models\UserAvailableCourses::class)->orderBy('courses.sort_order','ASC');
    }

    public function evaluations()
    {
        return $this->hasMany('App\Models\EvaluationResult','employee_id', 'id');
    }
    public function skillsEvaluations()
    {
        return $this->hasMany('App\Models\SkillsEvaluationResult','employee_id', 'id');
    }

    public function monthsEvaluations()
    {
        return $this->hasMany('App\Models\EvaluationResult','employee_id', 'id')->orderBy('evaluation_results.id','ASC')->limit(4);
    }

    public function todaysEvaluation()
    {
        return $this->hasMany('App\Models\EvaluationResult','employee_id', 'id')->whereDate('created_at', date('Y-m-d'));
    }

    public function getTotalAvailableAttribute()
    {
        return $this->courses->count();
    }
    public function getUsersCount()
    {
        return $this->count();
    }

    public function getTotalPassedAttribute()
    {
        return $this->results->where('course_is_passed', 1)->count();
    }

    public function getTotalInProgressAttribute()
    {
        return $this->results->where('course_is_passed', 0)->count();
    }

    public function getAvgPassedAttribute()
    {
        $passed = $this->results->where('course_is_passed', 1)->count();
        return $passed > 0 ? round($this->results->where('course_is_passed', 1)->sum('avg_mark') / $passed, 2) : 0;
    }

    public function getAvgTotalAttribute()
    {
        $available = [];
        if($this->courses->count() > 0){
            foreach($this->courses as $course){
                $available[] = $course->id;
            }
            return $this->results->count() > 0 ? round($this->results->where('course_is_passed', 1)->whereIn('course_id', $available)->sum('avg_mark') / count($available), 2) : 0;
        }
        return 0;
//        return $this->results->count() > 0 ? round($this->results->where('course_is_passed', 1)->whereIn('course_id', $available)->sum('avg_mark') / count($available), 2) : 0;
    }

    public function getLastActivityAttribute()
    {
        if(empty($this->last_active_at)){
            return 'Нет данных';
        }

        if(date("Y-m-d", strtotime($this->last_active_at)) == date ("Y-m-d")){
            return "Сегодня";
        }

        if(date("Y-m-d", strtotime($this->last_active_at)) == date("Y-m-d", strtotime("-1 day"))){
            return "Вчера";
        }

        /** Simply return last activity date */
        return date("Y-m-d", strtotime($this->last_active_at));

        /*if(date("Y-m-d", strtotime($this->last_active_at)) >= date("Y-m-d", strtotime("-7 day"))){
            return "На этой неделе";
        }

        if(date("Y-m-d", strtotime($this->last_active_at)) < date("Y-m-d", strtotime("-7 day"))){
            return "Больше недели назад";
        }*/
    }

    public function setPhotoAttribute($value)
    {
        $attribute_name = "photo";
        $disk = "public";
        $destination_path = "employees/".$this->idnp;

        $this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function getLocalityAttribute()
    {
        /*$check = [];
        $locality = "";
        if($this->branches->count() > 0){
            foreach($this->branches as $branch){
                if (!in_array($branch->locality->name, $check)) {
                    if(!empty($check)){
                        $locality .= ', ';
                    }
                    $check[] = $branch->locality->name;
                    $locality .= $branch->locality->name;
                }
            }
        }

        return $locality ?? '-';*/
        if($this->branches->count() === 0){
            return null;
        }
        return $this->branches->count() > 1 ? $this->branches[0]->locality->name . "[...]" : $this->branches[0]->locality->name;
    }

    public function getCurrentMonthEvaluationPointsAttribute()
    {
        if($this->monthsEvaluations->count()){
            $pointsSum = 0;
            foreach($this->monthsEvaluations as $evaluation){
                $pointsSum += $evaluation->total_points;
            }
            return round($pointsSum/$this->monthsEvaluations->count(),2);
        }
        return "-";
    }

    public function getEvaluationPointsAttribute()
    {
        if($this->evaluations->count()){
            $pointsSum = 0;
            foreach($this->evaluations as $evaluation){
                $pointsSum += $evaluation->total_points;
            }
            return round($pointsSum/$this->evaluations->count(),2);
        }
        return "-";
    }

    public function getCurrentMonthEvaluationMarkAttribute()
    {
        if($this->monthsEvaluations->count()){
            $marksSum = 0;
            foreach($this->monthsEvaluations as $evaluation){
                $marksSum += $evaluation->mark;
            }
            return round($marksSum/$this->monthsEvaluations->count(),2);
        }
        return "-";
    }

    public function getEvaluationMarkAttribute()
    {
        if($this->evaluations->count()){
            $marksSum = 0;
            foreach($this->evaluations as $evaluation){
                $marksSum += $evaluation->mark;
            }
            return round($marksSum/$this->evaluations->count(),2);
        }
        return "-";
    }

    public function getLastEvaluation(){
        return $this->hasOne('App\Models\EvaluationResult','employee_id', 'id')->orderBy('evaluation_results.id','DESC');
    }
    public function getLastMarks(){
        return $this->hasMany('App\Models\EvaluationResult','employee_id', 'id')->orderBy('evaluation_results.id','ASC')->limit(4);
    }

    public function getSubordinatesAvg(){
        if($this->subordinates->count() > 0){
            $sum = 0;
            foreach($this->subordinates as $subordiante){
                $sum += $subordiante->getLastEvaluation ? $subordiante->getLastEvaluation->mark : 0;
            }
            return round($sum / $this->subordinates->count(), 2);
        }
        return "-";
    }

    public function getFinalGrade(){
        $lastEvaluationMark = $this->getLastEvaluation ? $this->getLastEvaluation->mark : 0;
        if($this->subordinates->count() > 0){
            $subordinatesAvg = $this->getSubordinatesAvg();
            return round(($lastEvaluationMark + $subordinatesAvg) / 2, 2);
        }
        return $lastEvaluationMark;
    }

    /* Buttons */
    public function evaluate($crud = false)
    {
        if(null != $this->profession && null !== $this->profession->evaluation){
            return '<a class="btn btn-sm btn-success" href="/admin/evaluation/'.$this->id.'/start"><i class="la la-chalkboard"></i></a>';
        } else {
            return null;
        }
    }

    public function evaluation_marks($crud = false)
    {
        if($this->evaluations->count()){
            return '<a class="btn btn-sm btn-info" href="/admin/evaluation/'.$this->id.'/list"><i class="la la-award"></i></a>';
        } else {
            return null;
        }
    }
    public function skills_evaluate($crud = false)
    {
       if( $this->profession !==  null  && $this->profession->name == "Агент"){
            return '<a class="btn btn-sm btn-outline-success" href="/admin/skills-evaluation/'.$this->id.'/start"><i class="la la-area-chart"></i></a>';
       } else {
            return null;
       }
    }
    public function skills_marks($crud = false)
    {
        if($this->skillsEvaluations->count() && $this->profession->name == "Агент"){
        return '<a class="btn btn-sm btn-outline-info" href="/admin/skills-evaluation/'.$this->id.'/list"><i class="la la-pie-chart"></i></a>';
        } else {
            return null;
        }
    }
    public function setImageAttribute($value)
    {
        $attribute_name = "image";

        $disk = 'profile';
        // destination path relative to the disk above
        $destination_path = "app/public/profilePhoto";


        // if the image was erased
        if (empty($value)) {
            // delete the image from disk
            if (isset($this->{$attribute_name}) && !empty($this->{$attribute_name})) {
                Storage::disk($disk)->delete($this->{$attribute_name});
            }
            // set null on database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {

            $image = Image::make($value);


            $filename = md5($value.time()).'.jpg';

            Storage::disk($disk)->put('/'.$filename, $image->stream());


            if (isset($this->{$attribute_name}) && !empty($this->{$attribute_name})) {
                Storage::disk($disk)->delete($this->{$attribute_name});
            }


            $this->attributes[$attribute_name] = $filename;
        } elseif (!empty($value)) {

            $this->attributes[$attribute_name] = $this->{$attribute_name};
        }
    }
}

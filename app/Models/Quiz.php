<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'quizzes';
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

    public function userAttempts()
    {
        return $this->attempts()->where('user_id', backpack_user()->id)->count();
    }

    public function finalQuizQuestions()
    {
        $quizzes = \App\Models\Quiz::select('id')->where('course_id', $this->course_id)->get();
        $questions = \App\Models\Question::whereIn('quiz_id', $quizzes)->inRandomOrder()->limit($this->questions_to_show)->get();
        return $questions;
    }

    public function isPassed($employee_id)
    {
        $attempt = \App\Models\Attempt::where(['quiz_id' => $this->id, 'user_id' => $employee_id, 'status' => 'PASSED'])->first();
        return !empty($attempt) ? true : false;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Models\Lesson', 'lesson_id', 'id');
    }

    public function questionsList()
    {
      //  return $this->hasMany('App\Models\Question', 'quiz_id', 'id')->inRandomOrder()->limit($this->questions_to_show);
        return $this->hasMany('App\Models\Question', 'quiz_id', 'id')->limit($this->questions_to_show);
    }

    public function allQuestions()
    {
        return $this->hasMany('App\Models\Question', 'quiz_id', 'id');
    }

    public function attempts() {
        return $this->hasMany('App\Models\Attempt', 'quiz_id', 'id');
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

    public function getTotalQuestionsAttribute(){
        return $this->allQuestions->count();
    }

    public function getFinalAttribute(){
        return $this->is_final ? '<span class="badge badge-success"><i class="las la-check-circle"></i></span>' : '<span class="badge badge-danger"><i class="las la-times-circle"></i></span>';
    }

    public function getQuestionsInfoAttribute(){
        $markup = "<table>";
        $markup .= "<tr><th>Вопрос</th><th>Опции</th></tr>";

        $questions = json_decode($this->questions, true);
        if(!empty($questions)){
            foreach($questions as $question){
                $answers = "";
                if(!empty($question['answers'])){
                    $answers = "<table width='100%'>";
                    foreach(json_decode($question['answers'], true) as $answer){
                        if(isset($answer['option'])){
                            $isTrue = $answer['is_true'] ? '<span class="badge badge-success"><i class="las la-check-circle"></i></span>' : '<span class="badge badge-danger"><i class="las la-times-circle"></i></span>';
                            $answers .= "<tr><td width='90%'>" . $answer['option'] . "</td><td width='10%'>" . $isTrue . "</td></tr>";
                        }
                    }
                    $answers .= "</table>";
                }
                $markup .= "<tr><td>" . $question['question'] . "</td><td>" . $answers . "</td></tr>";
            }
        }

        $markup .= "</table>";
        return $markup;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}

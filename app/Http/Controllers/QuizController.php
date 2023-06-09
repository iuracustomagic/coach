<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Answer;
 
class QuizController extends Controller
{
    public function start($quiz_id)
    {
        $quiz = Quiz::find($quiz_id);

        if(!$quiz){
            abort(404, 'Quiz not found');
        }

        $attempt = new Attempt();
        $attempt->quiz_id = $quiz_id;
        $attempt->user_id = backpack_user()->id;
        $attempt->save();

        $questions = $quiz->is_final ? $quiz->finalQuizQuestions() : $quiz->questionsList;

        return view('quizzes.start', [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'questions' => $questions
        ]);
    }

    public function verify($attempt_id)
    {
        $request = request()->all();

        if($request['attempt_id'] == $attempt_id){
            $attempt = Attempt::find($attempt_id);

            if(!$attempt){
                abort(404, 'Attempt not found');
            }

            //$questionMark = 10 / $attempt->quiz->questionsList->count();
            $questionMark = 10 / $attempt->quiz->questions_to_show;
            $mark = 0;
            $userAnswers = [];

            if(!empty($request['questions'])){
                foreach($request['questions'] as $id => $answers){
                    $question = Question::find($id);
                    $options = Answer::where('question_id',$id)
                                                    ->get()
                                                    ->mapWithKeys(
                                                        function ($item) {
                                                            return [
                                                                $item['id'] => [
                                                                    'answer' => $item['answer'],
                                                                    'is_true' => $item['is_true']
                                                                ]
                                                            ];
                                                        }
                                                    );
                    $totalTrue = Answer::where(['question_id' => $id, 'is_true' => 1])->count();
                    $userAnswer = [];
                    $isTrue = false;
                    if(!empty($answers)){
                        foreach($answers as $answer){
                            if($options[$answer]['is_true'] && count($answers) == $totalTrue){
                                $isTrue = true;
                            } else {
                                $isTrue = false;
                            }
                            
                            $userAnswer[] = $options[$answer]['answer'];
                        }
                    }


                    if($isTrue){
                        $mark += $questionMark;
                    }

                    $userAnswers[] = [
                        'question' => $question->question,
                        'answer' => $userAnswer,
                        'duration' => $request['duration'][$id],
                        'is_true' => $isTrue
                    ];
                }
            }

            $attempt->answers = json_encode($userAnswers, JSON_UNESCAPED_UNICODE);
            $attempt->status = (round($mark, 1) >= 8) ? 'PASSED' : 'FAILED';
            $attempt->mark = round($mark, 1);
            $attempt->updated_at = date('Y-m-d H:i:s');
            if($attempt->save()){
                $report = \App\Models\Report::where(['quiz_id' => $attempt->quiz_id, 'user_id' => $attempt->user_id])->first();
                if(empty($report)){
                    $report = new \App\Models\Report();
                    $report->course_id = $attempt->quiz->course->id;
                    $report->lesson_id = $attempt->quiz->lesson->id;
                    $report->quiz_id = $attempt->quiz_id;
                    $report->user_id = $attempt->user_id;
                }
                $report->total_attempts += 1;
                if($attempt->status == 'STARTED'){
                    $report->started_attempts += 1; // Not finished
                }
                if($attempt->status == 'PASSED'){
                    $report->successful_attempts += 1;
                }
                if($attempt->status == 'FAILED'){
                    $report->failed_attempts += 1;
                }
                $report->total_points += $attempt->mark; 
                $report->avg_mark = round($report->total_points / $report->total_attempts, 2);
                $report->best_mark = null == $report->best_mark ? 0 : $report->best_mark;
                $report->best_mark = $attempt->mark > $report->best_mark ? $attempt->mark : $report->best_mark;
                $report->passed = null == $report->passed ? 0 : $report->passed;
                if($report->best_mark >= 8){
                    $report->passed = 1;
                }
                $attempt_duration = 0;
                $answers = json_decode($attempt->answers, true);
                if(!empty($answers)){
                    foreach($answers as $answer){
                        $attempt_duration += $answer['duration'];
                    }
                }
                $report->total_seconds += $attempt_duration;
                $report->avg_time = round($report->total_seconds / $report->total_attempts);
                if($report->save()){
                    $result = \App\Models\UserResults::where(['user_id' => $report->user_id, 'course_id' => $report->course_id])->first();
                    if(empty($result)){
                        $result = new \App\Models\UserResults();
                        $result->user_id = $report->user_id;
                        $result->course_id = $report->course_id;
                        $result->lessons_passed = 0;
                        $result->sum_marks = 0;
                        $result->avg_mark = 0;
                    }
                    $result->lessons_total = $report->quiz->course->lessons->count();
                    $result->lessons_passed += $report->passed ? 1 : 0;
                    //$result->sum_marks += $report->best_mark;
                    $result->sum_marks = \App\Models\Report::where(['user_id' => $report->user_id, 'course_id' => $report->course_id])->sum('avg_mark');
                    $result->avg_mark = round($result->sum_marks / $result->lessons_total, 2);
                    //$result->course_is_passed = $result->lessons_total == $result->lessons_passed ? 1 : 0;
                    $result->course_is_passed = $result->lessons_passed >= $result->course->lessons->count() ? 1 : 0;
                    $result->save();
                }
            }

        } else {
            abort(400, 'Wrong attempt');
        }

        return view('quizzes.result', [
            'attempt' => $attempt
        ]); 
    }

    public function fix()
    {
        // Fix for reports table
        // Step 1
        /*$reports = \App\Models\Report::get();
        foreach($reports as $report){
            if($report->quiz){
                $report->course_id = $report->quiz->course->id;
                $report->lesson_id = $report->quiz->lesson->id;
                $report->save();
            } else {
                echo $report->quiz_id . "\n";
            }
        }*/

        // Step 2
        
        /*$reports = \App\Models\Report::where('course_id', '!=', 0)->get();
        foreach($reports as $report){
            $result = \App\Models\UserResults::where(['user_id' => $report->user_id, 'course_id' => $report->course_id])->first();
            if(empty($result)){
                $result = new \App\Models\UserResults();
                $result->user_id = $report->user_id;
                $result->course_id = $report->course_id;
                $result->lessons_total = $report->quiz->course->lessons->count();
                $result->lessons_passed = 0;
                $result->avg_mark = 0;
                $result->course_is_passed = 0;
                $result->save();
            }
        }*/

        // Step 3
        /*$results = \App\Models\UserResults::get();
        foreach($results as $result){
            $totalPassed = \App\Models\Report::where('course_id', '!=', 0)->where(['course_id' => $result->course_id, 'user_id' => $result->user_id, 'passed' => 1])->count();
            $result->lessons_passed = $totalPassed;
            $result->course_is_passed = $result->lessons_total == $totalPassed ? 1 : 0;
            $result->save();
        }*/

        // Step 4
        /*$results = \App\Models\UserResults::get();
        foreach($results as $result){
            $reports = \App\Models\Report::where('course_id', '!=', 0)->where(['course_id' => $result->course_id, 'user_id' => $result->user_id])->get();
            $avg = 0;
            foreach($reports as $report){
                $avg += $report->best_mark;
            }
            $result->sum_marks = $avg;
            $result->avg_mark = round($avg / $result->lessons_total, 2);
            $result->save();
        }*/

        /* Fix for evaluation papers */
        /*$criteriasList = \App\Models\EvaluationCriteria::all()->pluck(
            'name',
            'id'
        );
        $papers = \App\Models\EvaluationPaper::all();
        foreach($papers as $paper){
            $structure = json_decode($paper->structure);
            foreach($structure as $skey => $criteria){
                if(!isset($structure[$skey]->code)){
                    $structure[$skey]->code = \Illuminate\Support\Str::random(12);
                }
                $points = json_decode($criteria->points);
                foreach($points as $pkey => $point){
                    $points[$pkey]->title = trim($points[$pkey]->title);
                    if(!isset($points[$pkey]->code)){
                        $points[$pkey]->code = \Illuminate\Support\Str::random(12);
                    }
                    if(isset($points[$pkey]->mark)){
                        $points[$pkey]->mark = floatval(str_replace(',', '.', $points[$pkey]->mark));
                    } else {
                        $points[$pkey]->mark = 0;
                    }
                }
                $structure[$skey]->points = json_encode($points, JSON_UNESCAPED_UNICODE);
            }

            $paper->structure = json_encode($structure, JSON_UNESCAPED_UNICODE);
            $paper->save();
        }*/
    }

    public function fixReports(){
        die('ok');
        /*$attempts = \App\Models\Attempt::offset(50000)->limit(10000)->get();
        foreach($attempts as $attempt){
            if($attempt->quiz != null){
                $report = \App\Models\Report::where(['quiz_id' => $attempt->quiz_id, 'user_id' => $attempt->user_id])->first();
                if(empty($report)){
                    $report = new \App\Models\Report();
                    $report->course_id = $attempt->quiz->course->id;
                    $report->lesson_id = $attempt->quiz->lesson->id;
                    $report->quiz_id = $attempt->quiz_id;
                    $report->user_id = $attempt->user_id;
                }
                $report->total_attempts += 1;
                if($attempt->status == 'STARTED'){
                    $report->started_attempts += 1; // Not finished
                }
                if($attempt->status == 'PASSED'){
                    $report->successful_attempts += 1;
                }
                if($attempt->status == 'FAILED'){
                    $report->failed_attempts += 1;
                }
                $report->total_points += $attempt->mark; 
                $report->avg_mark = round($report->total_points / $report->total_attempts, 2);
                $report->best_mark = null == $report->best_mark ? 0 : $report->best_mark;
                $report->best_mark = $attempt->mark > $report->best_mark ? $attempt->mark : $report->best_mark;
                $report->passed = null == $report->passed ? 0 : $report->passed;
                if($report->best_mark >= 8){
                    $report->passed = 1;
                }
                $attempt_duration = 0;
                $answers = json_decode($attempt->answers, true);
                if(!empty($answers)){
                    foreach($answers as $answer){
                        $attempt_duration += $answer['duration'];
                    }
                }
                $report->total_seconds += $attempt_duration;
                $report->avg_time = round($report->total_seconds / $report->total_attempts);
                if($report->save()){
                    $result = \App\Models\UserResults::where(['user_id' => $report->user_id, 'course_id' => $report->course_id])->first();
                    if(empty($result)){
                        $result = new \App\Models\UserResults();
                        $result->user_id = $report->user_id;
                        $result->course_id = $report->course_id;
                        $result->lessons_passed = 0;
                        $result->sum_marks = 0;
                        $result->avg_mark = 0;
                    }
                    $result->lessons_total = $report->quiz->course->lessons->count();
                    $result->lessons_passed += $report->passed ? 1 : 0;
                    //$result->sum_marks += $report->best_mark;
                    $result->sum_marks = \App\Models\Report::where(['user_id' => $report->user_id, 'course_id' => $report->course_id])->sum('avg_mark');
                    $result->avg_mark = round($result->sum_marks / $result->lessons_total, 2);
                    //$result->course_is_passed = $result->lessons_total == $result->lessons_passed ? 1 : 0;
                    $result->course_is_passed = $result->lessons_passed >= $result->course->lessons->count() ? 1 : 0;
                    $result->save();
                }
            }
        }*/
    }
}
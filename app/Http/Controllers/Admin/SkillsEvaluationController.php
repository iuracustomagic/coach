<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use App\Models\SkillsEvaluationResult;
use Illuminate\Http\Request;

use App\Models\User;

use App\Models\EvaluationResult;
use Illuminate\Support\Facades\App;

class SkillsEvaluationController extends \App\Http\Controllers\Controller
{
    public function start($employee_id)
    {
        $employee = User::find($employee_id);



        // TODO: check if user can evaluate the employee

        // TODO: prevent two evaluations for one employee in same day

        if(!$employee){
            abort(404, 'Employee not found');
        }

//        if($employee->monthsEvaluations->count() === 4){
//            return redirect("/admin/evaluation/$employee_id/list");
//        }

        if($employee->todaysEvaluation->count() > 0){
            abort(403, 'It is not allowed to start more than one evaluation for each employee at the same day');
        }

        /* Check if user can evaluate employee */
        if(backpack_user()->hasRole('Admin')){
            $employees = [];
            if(!empty(backpack_user()->divisions)){
                foreach(backpack_user()->divisions as $division){
                    if(!empty($division->employees)){
                        foreach($division->employees as $allowed){
                            $employees[] = $allowed->id;
                        }
                    }
                }
            }
            if(!in_array($employee_id, $employees)){
                abort(403, 'You are not allowed to evaluate this employee');
            }
        }

        if(backpack_user()->hasRole('Manager')){
            $employees = [];
            if(!empty(backpack_user()->branches)){
                foreach(backpack_user()->branches as $branch){
                    if(!empty($branch->employees)){
                        foreach($branch->employees as $allowed){
                            $employees[] = $allowed->id;
                        }
                    }
                }
            }
            if(!in_array($employee_id, $employees)){
                abort(403, 'You are not allowed to evaluate this employee');
            }
        }

        $skillsList = Skill::all();

        $evaluation = [];
        $evaluationRu = [];
        $evaluationRo = [];
        $evaluationEn = [];

        foreach($skillsList as $skill) {

            $evaluationRu[$skill['name']] = json_decode($skill->criteria, true);
             if(isset($skill['name_ro'])) {
                 $evaluationRo[$skill['name_ro']] = json_decode($skill->criteria, true);
             } else $evaluationRo[$skill['name']] = json_decode($skill->criteria, true);
             if(isset($skill['name_en'])) {
                 $evaluationEn[$skill['name_en']] = json_decode($skill->criteria, true);
             } else $evaluationEn[$skill['name']] = json_decode($skill->criteria, true);
            if(App::getLocale() == 'ru') {
                $evaluation[$skill['name']] = json_decode($skill->criteria, true);
            }

           else if (App::getLocale() == 'ro') {
               if(isset($skill['name_ro'])) {
                   $evaluation[$skill['name_ro']] = json_decode($skill->criteria, true);
               } else $evaluation[$skill['name']] =json_decode($skill->criteria, true);

            }
           else if (App::getLocale() == 'en') {
               if(isset($skill['name_en'])) {
                   $evaluation[$skill['name_en']] = json_decode($skill->criteria, true);
               } else $evaluation[$skill['name']] =json_decode($skill->criteria, true);

            };


             }

//dd($evaluation);
        return view('skills-evaluations.start', [
            'employee' => $employee,
            'evaluation' => $evaluation,
            'evaluation_ru' => $evaluationRu,
            'evaluation_ro' => $evaluationRo,
            'evaluation_en' => $evaluationEn,

        ]);
    }

    public function list($employee_id)
    {
        $employee = User::find($employee_id);

        if(!$employee){
            abort(404, 'Employee not found');
        }

        $results = [];
        $dates = [];
        $marks = [];
        $title_questions = [];
        $evaluation_subtotals = [];
        $evaluation_values = [];

        foreach($employee->skillsEvaluations as $key => $evaluation){ // $employee->monthsEvaluations
            $date = date('d/m/Y  H:i', strtotime($evaluation->created_at));
            if(App::getLocale() == 'ru') {
                $results[$key] = json_decode($evaluation->result);
            } elseif(App::getLocale() == 'ro') {
                $results[$key] = json_decode($evaluation->result_ro);
            }else $results[$key] = json_decode($evaluation->result_en);


            $dates[] = [
                'date' => $date,
                'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '-',
                'recommendation' => $evaluation->recommendation,
                'conclusion' => json_decode($evaluation->conclusion)
            ];
        }

//dump($results);

        foreach($results as $key => $result){
            $evaluation_key = 'eval_'.$key;
            $marks[] = isset($result->finalTotal) ?? $result->finalTotal ;
            if(isset($result->items)) {
                foreach($result->items as $key_title => $question_ar){
                    $evaluation_subtotals[$evaluation_key][$key_title] = $question_ar->total;
                    //dump($question_ar->total);
                    $title_questions[$key_title] = [];
                    $idx =0;

                    foreach ($question_ar->items as $key => $value) {
                        $evaluation_values[$evaluation_key][$key_title][] = $value->value;
                        $title_questions[$key_title][$idx] = $key;
                        $idx++;
                    }
                }
            }

        }
//        dd($evaluation_values);
        return view('skills-evaluations.list', [
            'employee' => $employee,
            'dates' => $dates,
            'marks' => $marks,
            'title_questions' => $title_questions,
            'subtotals' => $evaluation_subtotals,
            'values' => $evaluation_values,

        ]);
    }

    public function view($id)
    {
        $evaluation = EvaluationResult::find($id);
        if(empty($evaluation)){
            abort(404, 'Evaluation list not found');
        }
        $info = [];
        $date = date('Y-m-d', strtotime($evaluation->created_at));
        $result = json_decode($evaluation->result);
        foreach($result as $id => $criteria){
                $info[$id]['title'] = $criteria->title;
                foreach($criteria->points as $group => $points){
                    foreach($points as $code => $point){
                        $info[$id]['points'][$group]['legend'][$code] = $point->title . ' (' . $point->mark . ')';
                        if($point->selected){
                            $info[$id]['points'][$group]['marks'][$date] = $point->mark;
                        }
                    }
                }
        }
        return view('evaluations.view', [
            'employee' => $evaluation->employee,
            'date' => [
                'date' => $date,
                'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '-',
                'comment' => $evaluation->comment
            ],
            'info' => $info,
            'totals' => [
                'points' => $evaluation->total_points,
                'mark' => $evaluation->mark
            ]
        ]);
    }

    public function save($employee_id, Request $request)
    {
//dd($request);
        $skillsEvaluation = new SkillsEvaluationResult();
        $skillsEvaluation->employee_id = $employee_id;
        $skillsEvaluation->examiner_id = backpack_user()->id;
        $skillsEvaluation->mark = $request->input('mark');
        $skillsEvaluation->result = $request->input('result');
        $skillsEvaluation->result_ro = $request->input('result_ro');
        $skillsEvaluation->result_en = $request->input('result_en');
        $skillsEvaluation->conclusion = $request->input('conclusion');
        $skillsEvaluation->recommendation = $request->input('recommendation');
         if($skillsEvaluation->save()){

            return redirect("/admin");
        } else {
            abort(400, 'Result has not been saved');
        }

    }
}

?>

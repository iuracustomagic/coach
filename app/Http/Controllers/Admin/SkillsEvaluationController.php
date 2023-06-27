<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use App\Models\SkillsEvaluationResult;
use Illuminate\Http\Request;

use App\Models\User;

use App\Models\EvaluationResult;

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

        foreach($skillsList as $skill) {
            $criteria = json_decode($skill->criteria, true);
            $evaluation[$skill['name']] = $criteria;

             }


        return view('skills-evaluations.start', [
            'employee' => $employee,
            'evaluation' => $evaluation,

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
            $date = date('Y-m-d', strtotime($evaluation->created_at));
            $results[$key] = json_decode($evaluation->result);

            $dates[] = [
                'date' => $date,
                'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '-',
                'recommendation' => $evaluation->recommendation,
                'conclusion' => json_decode($evaluation->conclusion)
            ];
        }


        foreach($results as $key => $result){
            $evaluation_key = 'eval_'.$key;
            $marks[] = $result->finalTotal;
            foreach($result->items as $key_title => $question_ar){
                $evaluation_subtotals[$evaluation_key][$key_title] = $question_ar->total;
                //dump($question_ar->total);
                $title_questions[$key_title] = [];
                $idx =0;

                foreach ($question_ar->items as $key => $value) {
                    ///dump($value->value);
                    $evaluation_values[$evaluation_key][$key_title][] = $value->value;
                    $title_questions[$key_title][$idx] = $key;
                    $idx++;
                }
            }
        }

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

        $skillsEvaluation = new SkillsEvaluationResult();
        $skillsEvaluation->employee_id = $employee_id;
        $skillsEvaluation->examiner_id = backpack_user()->id;
        $skillsEvaluation->mark = $request->input('mark');
        $skillsEvaluation->result = $request->input('result');
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

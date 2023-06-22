<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use App\Models\SkillsEvaluationResult;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\EvaluationCriteria;
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
        $validation = [];
        $counter = 0;

        foreach($skillsList as $skill) {
            $criteria = json_decode($skill->criteria, true);
            $evaluation[$skill['name']] = $criteria;

             }

//            dd($evaluation);
//                foreach($object as $criterias){
//                    $evaluation[$criterias->criteria]['title'] = $criteriasList[$criterias->criteria];
//                    $points = json_decode($criterias->points, true);
//                    $bestMark = 0;
//                    $worstMark = 0;
//                    foreach($points as $key => $point){
//                        $bestMark = $point['mark'] > $bestMark ? $point['mark'] : $bestMark;
//                        $worstMark = $point['mark'] < $worstMark ? $point['mark'] : $worstMark;
//                        $points[$key]['selected'] = false;
//                        $points[$point['code']] = $points[$key];
//                        unset($points[$key]);
//                    }
//                    $evaluation[$criterias->criteria]['points'][$criterias->code] = $points;
//                    $validation[$criterias->code] = false;
//                    $evaluation[$criterias->criteria]['best'] = $bestMark;
//                    $evaluation[$criterias->criteria]['worst'] = $worstMark;
//                    $counter++;
//                }


        //dd($evaluation);

        return view('skills-evaluations.start', [
            'employee' => $employee,
            'evaluation' => $evaluation,
//            'validation' => json_encode($validation, JSON_UNESCAPED_UNICODE),
//            'counter' => $counter,
//            'json' => json_encode($evaluation, JSON_UNESCAPED_UNICODE)
        ]);
    }

    public function list($employee_id)
    {
        $employee = User::find($employee_id);


        if(!$employee){
            abort(404, 'Employee not found');
        }

        $result = [];
        $dates = [];
        $totals = [];
        $mark = 0;
//        dd($employee->evaluations);
        foreach($employee->skillsEvaluations as $evaluation){ // $employee->monthsEvaluations
            $date = date('Y-m-d', strtotime($evaluation->created_at));
            $result = json_decode($evaluation->result);

//            foreach($result as $id => $criteria){
//                $results[$id]['title'] = $criteria->title;
//                foreach($criteria->points as $group => $points){
//                    foreach($points as $code => $point){
//                        $results[$id]['points'][$group]['legend'][$code] = $point->title . ' (' . $point->mark . ')';
//                        if($point->selected){
//                            $results[$id]['points'][$group]['marks'][$date] = $point->mark;
//                        }
//                    }
//                }
//            }
            $dates[] = [
                'date' => $date,
                'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '-',
                'recommendation' => $evaluation->recommendation,
                'conclusion' => $evaluation->conclusion
            ];
//            dd($dates);

            $mark = $evaluation->mark;
        }

        return view('skills-evaluations.list', [
            'employee' => $employee,
            'dates' => $dates,
            'result' => $result,
            'mark' => $mark,

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
//print_r($request->input('mark'));
//print_r($request->input('result'));
//print_r($request->input('conclusion'));
//print_r( $request->input('recommendation'));
//exit();
//dd($request->input('result'));
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
//        $evaluation = new EvaluationResult();
//        $evaluation->employee_id = $employee_id;
//        $evaluation->examiner_id = backpack_user()->id;
//        $evaluation->total_points = round($request->input('total_points'),2);
//        $evaluation->total_questions = $request->input('total_questions');
//        $evaluation->mark = round($request->input('mark'),2);
//        $evaluation->result = $request->input('result');
//        $evaluation->comment = $request->input('comment');
//        if($evaluation->save()){
//            //return redirect('/admin/report');
//            return redirect("/admin/evaluation/$employee_id/list");
//        } else {
//            abort(400, 'Result has not been saved');
//        }
    }
}

?>

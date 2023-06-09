<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationResult;

class EvaluationController extends \App\Http\Controllers\Controller
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

        $criteriasList = EvaluationCriteria::all()->pluck(
            'name',
            'id'
        );

        $evaluation = [];
        $validation = [];
        $counter = 0;

        if(null !== $employee->profession){
            if(null !== $employee->profession->evaluation){
                $object = json_decode($employee->profession->evaluation->structure);
                foreach($object as $criterias){
                    $evaluation[$criterias->criteria]['title'] = $criteriasList[$criterias->criteria];
                    $points = json_decode($criterias->points, true);
                    $bestMark = 0;
                    $worstMark = 0;
                    foreach($points as $key => $point){
                        $bestMark = $point['mark'] > $bestMark ? $point['mark'] : $bestMark;
                        $worstMark = $point['mark'] < $worstMark ? $point['mark'] : $worstMark;
                        $points[$key]['selected'] = false;
                        $points[$point['code']] = $points[$key];
                        unset($points[$key]);
                    }
                    $evaluation[$criterias->criteria]['points'][$criterias->code] = $points;
                    $validation[$criterias->code] = false;
                    $evaluation[$criterias->criteria]['best'] = $bestMark;
                    $evaluation[$criterias->criteria]['worst'] = $worstMark;
                    $counter++;
                }
            } else {
                abort(404, 'There is no evaluation list for profession ' . $employee->profession->name);
            }
        } else {
            abort(404, 'There is no profession assigned to ' . $employee->name);
        }

        //dd($evaluation);

        return view('evaluations.start', [
            'employee' => $employee,
            'evaluation' => $evaluation,
            'validation' => json_encode($validation, JSON_UNESCAPED_UNICODE),
            'counter' => $counter,
            'json' => json_encode($evaluation, JSON_UNESCAPED_UNICODE)
        ]);
    }

    public function list($employee_id)
    {
        $employee = User::find($employee_id);

        // TODO: May be better to show for last 6 month

        // TODO: check if user can view results for the employee

        if(!$employee){
            abort(404, 'Employee not found');
        }

        $results = [];
        $dates = [];
        $totals = [];
        $pointsSum = 0;
        $marksSum = 0;
        foreach($employee->evaluations as $evaluation){ // $employee->monthsEvaluations
            $date = date('Y-m-d', strtotime($evaluation->created_at));
            $result = json_decode($evaluation->result);
            foreach($result as $id => $criteria){
                $results[$id]['title'] = $criteria->title;
                foreach($criteria->points as $group => $points){
                    foreach($points as $code => $point){
                        $results[$id]['points'][$group]['legend'][$code] = $point->title . ' (' . $point->mark . ')';
                        if($point->selected){
                            $results[$id]['points'][$group]['marks'][$date] = $point->mark;
                        }
                    }
                }
            }
            $dates[] = [
                'date' => $date,
                'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '-',
                'comment' => $evaluation->comment
            ];
            $totals['points'][$date] = $evaluation->total_points;
            $pointsSum += $evaluation->total_points;
            $totals['marks'][$date] = $evaluation->mark;
            $marksSum += $evaluation->mark;
        }

        return view('evaluations.list', [
            'employee' => $employee,
            'dates' => $dates,
            'results' => $results,
            'totals' => $totals,
            'avg_points' => round($pointsSum / $employee->evaluations->count(),2),
            'avg_mark' => round($marksSum / $employee->evaluations->count(),2)
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
        if(md5($employee_id) !== $request->input('signature')){
            abort(403, 'You are not allowed to evaluate provided employee');
        }

        $evaluation = new EvaluationResult();
        $evaluation->employee_id = $employee_id;
        $evaluation->examiner_id = backpack_user()->id;
        $evaluation->total_points = round($request->input('total_points'),2);
        $evaluation->total_questions = $request->input('total_questions');
        $evaluation->mark = round($request->input('mark'),2);
        $evaluation->result = $request->input('result');
        $evaluation->comment = $request->input('comment');
        if($evaluation->save()){
            //return redirect('/admin/report');
            return redirect("/admin/evaluation/$employee_id/list");
        } else {
            abort(400, 'Result has not been saved');
        }
    }
}

?>

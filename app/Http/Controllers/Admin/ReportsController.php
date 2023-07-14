<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Models\EvaluationResult;
use Illuminate\Http\Request;


class ReportsController extends Controller
{
    public function setup()
    {

        CRUD::enableExportButtons();
    }

    public function branchSummary($branch_id){
        $branch = Branch::find($branch_id);

        $hierarchy = $this->buildSummaryHierarchy($branch->activeEmployees);
        $rows = $this->drawSummaryRows($hierarchy);
        return view('reports/branch-summary', [
            'rows' => $rows,
            'title' => $branch->name
        ]);
    }

    public function branchEvaluations(Request $request, $branch_id){
        $branch = Branch::find($branch_id);
        $from = $request->get('from') ?? Carbon::today()->startOfMonth()->format('Y-m-d');
        $to = $request->get('to') ?? Carbon::today()->endOfMonth()->format('Y-m-d');
        $hierarchy = $this->buildEvaluationsHierarchy($branch->activeEmployees, null, $from, $to);
        $rows = $this->drawEvaluationsRows($hierarchy);
        return view('reports/branch-evaluations', [
            'range' => [
                'from' => $from,
                'to' => $to
            ],
            'rows' => $rows,
            'title' => $branch->name
        ]);
    }

    protected function buildSummaryHierarchy($employees, $supervisor = null)
    {
        $leadership = [12, 18, 22, 44, 36, 64, 68, 85, 79, 95, 105, 115];
        $hierarchy = array();
//        dd($employees);
//        exit();
        foreach($employees as $employee){
            if($supervisor == $employee->supervisor_id){
//                if($employee->getLastEvaluation) {
//
//                }
                if($employee->active == 1) {
                    $hierarchy[$employee->id] = [
                        'id' => $employee->id,
                        'supervisor' => $employee->supervisor ? $employee->supervisor->name : '-',
                        'name' => $employee->name,
                        'proffession' => $employee->profession ? $employee->profession->name : '-',
                        'leader' => $employee->profession ? in_array($employee->profession->id, $leadership) : false,
                        'has_subordinates' => $employee->subordinates->count() > 0 ? true : false,
                        'subordinates' => $employee->subordinates->count() > 0 ? $this->buildSummaryHierarchy($employee->subordinates, $employee->id) : null,
                        'registered' => date('Y-m-d',strtotime($employee->created_at)),
                        'theory_available' => $employee->totalAvailable,
                        'theory_passed' => $employee->totalPassed,
                        'theory_avg'  => $employee->avg_total,
                        'last_mark' => $employee->getLastEvaluation ? $employee->getLastEvaluation->mark : null,
                        'last_marks' => $employee->getLastMarks ?: '-',
                        'subordinates_avg' => $employee->getSubordinatesAvg(),
                        'final_grade' => $employee->getFinalGrade()
                    ];
                }

            }
        }

        return $hierarchy;
    }

    protected function buildEvaluationsHierarchy($employees, $supervisor = null, $from = null, $to = null)
    {
        $leadership = [12, 18, 22, 44, 36, 64, 68, 85, 79, 95, 105, 115];
        $hierarchy = array();
        foreach($employees as $employee){
            $evaluations = array();
            $list = $employee->evaluations->where('created_at', '>=', $from)->where('created_at', '<=', $to);
            foreach($list as $evaluation){
                $evaluations[] = [
                    'id' => $evaluation->id,
                    'date' => Carbon::parse($evaluation->created_at)->format('Y-m-d'),
                    'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '-',
                    'points' => $evaluation->total_points,
                    'mark' => $evaluation->mark
                ];
            }
            if($supervisor == $employee->supervisor_id){
                $hierarchy[$employee->id] = [
                    'id' => $employee->id,
                    'supervisor' => $employee->supervisor ? $employee->supervisor->name : '-',
                    'name' => $employee->name,
                    'proffession' => $employee->profession ? $employee->profession->name : '-',
                    'leader' => $employee->profession ? in_array($employee->profession->id, $leadership) : false,
                    'has_subordinates' => $employee->subordinates->count() > 0 ? true : false,
                    'subordinates' => $employee->subordinates->count() > 0 ? $this->buildEvaluationsHierarchy($employee->subordinates, $employee->id, $from, $to) : null,
                    'evaluations' => $evaluations
                ];
            }
        }
        return $hierarchy;
    }

    protected function drawSummaryRows($hierarchy, $lvl = 0, $index = 1)
    {
        $rows = array();

        foreach($hierarchy as $id => $employee){

            for($i = 0; $i < $lvl; $i++){
                $own = $i+1 == $lvl ? "<span class=''></span>" : "";
            }
            $marker = "";
            if($employee['final_grade'] <= 6) {
                $marker = "class='table-danger'";
            } else if ($employee['final_grade'] <= 8) {
                $marker = "class='table-warning'";
            } else if ($employee['final_grade'] <= 9) {
                $marker = "class='table-success'";
            } else if($employee['final_grade'] > 9) {
                $marker = "class='table-perfect'";
            }
            $toggler = "<span class='toggler t-lvl-".$lvl." toggle-subordinates' data-subordinates-to='" . $id . "'>+</span>";
            $row = "<tr ".$marker.">";
//            $row .= "<td class='controls not-export-col'>";
//                $row .= $employee['has_subordinates'] ? $toggler : '';
//                $row .= $own ?? "";
//            $row .= "</td>";
            $row .= "<td class='index'>";
            $row .= $index;
            $row .= "</td>";
            $row .= "<td class='prof'>";
            if($employee['leader']){
                $row .= "<b>" . $employee['proffession'] . "</b>";
            } else {
                $row .= $employee['proffession'];
            }
            $row .= "</td>";
            $row .= "<td class='employee'>";
            if($employee['leader']){
                $row .= "<b>" . $employee['name'] . "</b>";
            } else {
                $row .= $employee['name'];
            }
            $row .="</td>";
            $row .= "<td class='date'>";
            $row .= $employee['registered'];
            $row .="</td>";
            $row .= "<td class='available'>";
            $row .= $employee['theory_available'];
            $row .="</td>";
            $row .= "<td class='passed'>";
            $row .= $employee['theory_passed'];
            $row .="</td>";
            $row .= "<td class='avg'>";
            $row .= $employee['theory_avg'];
            $row .="</td>";
            $row .= "<td class='ev-list'><div style='display: flex; justify-content: center'>";

            foreach($employee['last_marks'] as $employeMark) {
                $row .= "<span class='mr-3'>".$employeMark['mark']."</span>";
            }

            if($employee['last_mark']){
                $row .= " <button class='btn btn-primary btn-sm show-evaluations' data-employee-id='".$employee['id']."'><i class='las la-eye'></i></button></div>";
            } else {
                $row .= "-";
            }

            $row .="</td>";
            $row .= "<td class='avg'>";
            $row .= $employee['subordinates_avg'];
            $row .="</td>";
            $row .= "<td class='res'>";
            $row .= $employee['final_grade'];
            $row .="</td>";
            $row .= "<td class='supervisor'>";
            $row .= $employee['supervisor'];
            $row .="</td>";

            $row .= "</tr>";

            $rows[] = $row;

            if($employee['has_subordinates']){
                $subordinates = $this->drawSummaryRows($employee['subordinates'], $lvl + 1,  $index + 1);
//                $rows[] = "<tr class='may-hide hidden' data-supervisor='" . $employee['id'] . "'><td >";
                foreach($subordinates as $subordinate){
                    $rows[] = $subordinate;
                }
//                $rows[] = "</td></tr>";
                $index += count($subordinates) + 1;
            } else {
                $index++;
            }
        }

        return $rows;
    }

    protected function drawEvaluationsRows($hierarchy, $lvl = 0, $index = 1)
    {
        $rows = array();

        foreach($hierarchy as $id => $employee){
            for($i = 0; $i < $lvl; $i++){
                $own = $i+1 == $lvl ? "<span class=''></span>" : "";
            }

            $toggler = "<span class='toggler t-lvl-".$lvl." toggle-subordinates' data-subordinates-to='" . $id . "'>+</span>";
            $row = "<tr>";
//            $row .= "<td class='controls not-export-col'>";
//                $row .= $employee['has_subordinates'] ? $toggler : '';
//                $row .= $own ?? "";
//            $row .= "</td>";
            $row .= "<td class='index not-export-col'>";
            $row .= $index;
            $row .= "</td>";
            $row .= "<td class='employee'>";
            if($employee['leader']){
                $row .= "<b>" . $employee['name'] . "</b>";
            } else {
                $row .= $employee['name'];
            }
            $row .="</td>";
            $row .= "<td class='prof'>";
            if($employee['leader']){
                $row .= "<b>" . $employee['proffession'] . "</b>";
            } else {
                $row .= $employee['proffession'];
            }
            $row .= "</td>";
            $row .= "<td class='supervisor'>";
            $row .= $employee['supervisor'];
            $row .="</td>";
            $row .= "<td class='ev-list'>";
            if(!empty($employee['evaluations'])){
                $row .= "<div class='d-flex evaluations_container' >";
                $row .= "<div class='mr-3'>";
//                $row .= "<th>";
                $row .= "<p>Дата: </p>";
                $row .= "<p>Оценивал: </p>";
                $row .= "<p>Баллов: </p>";
                $row .= "<p>Оценка: </p>";
                $row .= "</div>";
                foreach($employee['evaluations'] as $evaluation){
//                    $row .= "<td>";
                    $row .= "<div class='mr-3'>";
                    $row .= "<p><a href='/admin/evaluation/view/".$evaluation['id']."' target='_blank'>" . $evaluation['date'] . "</a></p>";
                    $row .= "<p>" . $evaluation['examiner'] . "</p>";
                    $row .= "<p>" . $evaluation['points'] . "</p>";
                    $row .= "<p>" . $evaluation['mark'] . "</p>";
                    $row .= "</div>";
//                    $row .= "</div>";
                }
             $row .= "</div>";
//                $row .= "</table>";
            } else
                $row .= "<p>-</p>";
            $row .="</td>";
            $row .= "</tr>";

            $rows[] = $row;

            if($employee['has_subordinates']){
                $subordinates = $this->drawEvaluationsRows($employee['subordinates'], $lvl + 1,  $index + 1);
//                $rows[] = "<tr class='may-hide hidden' data-supervisor='" . $employee['id'] . "'><td colspan='12'><table class='lvl lvl-".$lvl."'>";
                foreach($subordinates as $subordinate){
                    $rows[] = $subordinate;
                }
//                $rows[] = "</table></td></tr>";
                $index += count($subordinates) + 1;
            } else {
                $index++;
            }
        }

        return $rows;
    }

    public function viewEvaluations($employee_id){
        $employee = User::find($employee_id);
        if($employee->monthsEvaluations){
            foreach($employee->monthsEvaluations as $evaluation){
                $evaluations[] = [
                    'id' => $evaluation->id,
                    'date' => $evaluation->created_at,
                    'examiner' => $evaluation->examiner ? $evaluation->examiner->name : '',
                    'mark' => $evaluation->mark
                ];
            }
        }
        if(!empty($evaluations)){
            $table = "<table class='table table-stripped'>";
            $table .= "<tr>";
            $table .= "<th>Дата</th>";
            foreach($evaluations as $evaluation){
                $table .= "<td>";
                $table .= date('Y-m-d',strtotime($evaluation['date']));
                $table .= "</td>";
            }
            $table .= "</tr>";
            $table .= "<tr>";
            $table .= "<th>Оценивал</th>";
            foreach($evaluations as $evaluation){
                $table .= "<td>";
                $table .= $evaluation['examiner'];
                $table .= "</td>";
            }
            $table .= "</tr>";
            $table .= "<tr>";
            $table .= "<th>Оценка</th>";
            foreach($evaluations as $evaluation){
                $table .= "<td>";
                $table .= $evaluation['mark'];
                $table .= "</td>";
            }
            $table .= "</tr>";
            $table .= "<tr>";
            $table .= "<th></th>";
            foreach($evaluations as $evaluation){
                $table .= "<td>";
                $table .= "<a href='/admin/evaluation/view/".$evaluation['id']."' target='_blank'>Подробнее</a>";
                $table .= "</td>";
            }
            $table .= "</tr>";
            $table .= "</table>";
        } else {
            $table = "Нет доступных оценочных листов";
        }
        return [
            'employee' => $employee->name,
            'evaluations' => $table
        ];
    }
}

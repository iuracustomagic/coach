<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $attempts = Attempt::get();
        foreach($attempts as $attempt){
            $report = Report::where(['quiz_id' => $attempt->quiz_id, 'user_id' => $attempt->user_id])->first();
            if(empty($report)){
                $report = new Report();
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
            $report->save();
        }
    }
}
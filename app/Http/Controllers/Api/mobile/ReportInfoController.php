<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportInfoController extends Controller
{
    public function list()
    {
        $user = Auth::user();
        $reports = $user->reports;

        foreach ($reports as $report) {
            $course = Course::where('id', $report['course_id'])->first();
            $report['course_id'] = $course->name;
            $lesson = Lesson::where('id', $report['lesson_id'])->first();
            $report['lesson_id'] = $lesson->name;
    }

        return response()->json([
            'status' => 'ok',
            'reports' => $reports,

        ], 200);
    }
}

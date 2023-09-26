<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserAvailableCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseInfoController extends Controller
{
    public function list()
    {
        $user = Auth::user();
        $courses = $user->courses;

        return response()->json([
            'status' => 'ok',
            'userName' => $user->name,
            'courses' => $courses
        ], 200);
    }

    public function view($course_id)
    {
        $course = Course::find($course_id);
        $lessons = Lesson::where('course_id', $course_id)->get();

        if(!$lessons){
            abort(404, 'Lessons not found');
        }


        return response()->json([
            'status' => 'ok',
            'name' => $course->name,
            'lessons' => $lessons
        ], 200);
    }
}

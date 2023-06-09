<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
 
class MyCoursesController extends Controller
{
    public function list()
    {
        $user = backpack_user();
        $courses = $user->courses;
        return view('courses.list', [
            'courses' => $courses
        ]);
    }

    public function view($course_id)
    {
        $course = Course::find($course_id);

        if(!$course){
            abort(404, 'Course not found');
        }

        if(!Course::isAvailable($course_id)){
            abort(403, 'You are not allowed to access the course');
        }

        return view('courses.view', [
            'course' => $course
        ]);
    }

    public function lesson($course_id, $lesson_id)
    {
        $lesson = Lesson::find($lesson_id);
        
        if(!$lesson){
            abort(404, 'Lesson not found');
        }

        if(!Course::isAvailable($course_id)){
            abort(403, 'You are not allowed to access the course');
        }

        return view('courses.lesson.view', [
            'lesson' => $lesson
        ]);
    }
}
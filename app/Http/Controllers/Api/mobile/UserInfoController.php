<?php
namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Models\UserAvailableCourses;
use App\Models\UserResults;
use App\Models\Lesson;
use App\Models\Attempt;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    public function index() {
        $user = Auth::user();
        $courses = UserAvailableCourses::where(['user_id' => $user->id])->get();

        $courseCount = $courses->count();
        $lessonsCount = 0;
        $lessonsPassed =0;
        $avgMarkSum = 0;
        if($courseCount > 0) {
            foreach ($courses as $course) {
                $lessons = Lesson::where('course_id', $course->course_id)->count();
                $lessonsCount += $lessons;
            }
        }

        $lessonsResult = UserResults::where(['user_id' => $user->id])->get();
        $lessonsResultCount = $lessonsResult->count();
        if($lessonsResultCount > 0) {
            foreach ($lessonsResult as $lesson) {
                $lessonsPassed += $lesson->lessons_passed;
                $avgMarkSum += $lesson->avg_mark;
            }
        }
        $coursePassed = UserResults::where(['user_id' => $user->id, 'course_is_passed' => 1])->count();

        $quizPassed = Attempt::where(['user_id' => $user->id, 'status' =>  'PASSED'])->count();
        $quizFailed = Attempt::where(['user_id' => $user->id, 'status' =>  'FAILED'])->count();
        $quizStarted = Attempt::where(['user_id' => $user->id, 'status' =>  'STARTED'])->count();
        $quizAll = $quizPassed + $quizFailed + $quizStarted;

        if($courseCount > 0) {
            $coursePercent = intval(round($coursePassed / $courseCount * 100));
        } else $coursePercent = 0;


        if($lessonsCount > 0) {
            $lessonPercent =  intval(round($lessonsPassed / $lessonsCount * 100));
        } else $lessonPercent = 0;


        if($quizAll > 0) {
            $quizPercent =  intval(round($quizPassed / $quizAll * 100));
        } else $quizPercent = 0;

        if($lessonsResultCount > 0) {
            $avgMark = round($avgMarkSum / $lessonsResultCount, 2);
        } else $avgMark = 0;


        return response()->json([
            'status' => 'ok',
            'userName' => $user->name,
            'courses' => [
                'passed' => $coursePassed,
                'count' => $courseCount
            ],
            'lessons' => [
                'passed' => $lessonsPassed,
                'count' => $lessonsCount
            ],
            'quizzes' => [
                'passed' => $quizPassed,
                'mark' => $avgMark
            ],
        ], 200);
    }
}

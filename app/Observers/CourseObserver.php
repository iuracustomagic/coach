<?php

namespace App\Observers;

use App\Models\Course;

class CourseObserver
{
    /**
     * Handle the Course "created" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function created(Course $course)
    {
        if(!empty($course->professions)){
            foreach($course->professions as $profession){
                if(!empty($profession->employees)){
                    foreach($profession->employees as $employee){
                        $pivot = \App\Models\UserAvailableCourses::where(['user_id' => $employee->id, 'course_id' => $course->id])->first();
                        if(empty($pivot)){
                            $pivot = new \App\Models\UserAvailableCourses();
                            $pivot->user_id = $employee->id;
                            $pivot->course_id = $course->id;
                            $pivot->save();
                        }
                    }
                }
            }
        }
        /*if(!empty($course->profession->employees)){
            foreach($course->profession->employees as $employee){
                $pivot = \App\Models\UserAvailableCourses::where(['user_id' => $employee->id, 'course_id' => $course->id])->first();
                if(empty($pivot)){
                    $pivot = new \App\Models\UserAvailableCourses();
                    $pivot->user_id = $employee->id;
                    $pivot->course_id = $course->id;
                    $pivot->save();
                }
            }
        }*/
    }

    /**
     * Handle the Course "updated" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function updated(Course $course)
    {

//        if(!empty($course->professions)){
//            foreach($course->professions as $profession){
//                if(!empty($profession->employees)){
//                    foreach($profession->employees as $employee){
//                        $pivot = \App\Models\UserAvailableCourses::where(['user_id' => $employee->id, 'course_id' => $course->id])->first();
//                        if(empty($pivot)){
//                            $pivot = new \App\Models\UserAvailableCourses();
//                            $pivot->user_id = $employee->id;
//                            $pivot->course_id = $course->id;
//                            $pivot->save();
//                        }
//                    }
//                }
//            }
//        }
    }

    /**
     * Handle the Course "deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function deleted(Course $course)
    {
        //
    }

    /**
     * Handle the Course "restored" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function restored(Course $course)
    {
        //
    }

    /**
     * Handle the Course "force deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function forceDeleted(Course $course)
    {
        //
    }
}

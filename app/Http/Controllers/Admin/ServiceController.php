<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\CourseProfessions;

class ServiceController extends Controller
{
    public function fix(){
        die('fix');
        /*$courses = Course::get();
        foreach($courses as $course){
            $pivot = new CourseProfessions();
            $pivot->course_id = $course->id;
            $pivot->profession_id = $course->profession_id;
            $pivot->save();
        }*/
    }

    public function fixOrder(){
        die('fix-order');
        /*$courses = Course::get();
        foreach($courses as $course){
            $order = 1;
            foreach($course->lessons as $lesson){
                $lesson->sort_order = $order;
                $lesson->update();
                $order++;
            }   
        }*/
    }
}
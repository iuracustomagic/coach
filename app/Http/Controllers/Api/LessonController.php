<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lesson;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $dependencies = $request->input('dependencies');
        $dependencies = json_decode($dependencies, true);
        $course_id = 1;

        if(!empty($dependencies)){
            foreach($dependencies as $key => $value){
                if($key == 'course_id'){
                    $course_id = $value;
                }
            }
        }
        
        if ($search_term){
            $results = Lesson::where([
                ['name', 'LIKE', '%'.$search_term.'%'],
                ['course_id', '=', $course_id]
            ])->paginate(10);

        } else {
            $results = Lesson::where('course_id', $course_id)->paginate(10);
        }

        return $results;
    }
}
<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
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
}

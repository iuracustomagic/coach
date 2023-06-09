<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBranches;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $formData = $request->input('form');
        $branches = [];

        foreach($formData as $part){
            if($part['name'] === 'branches[]'){
                $branches[] = $part['value'];
            }
        }

        $availableEmployees = [];
        if(!empty($branches)){
            $availableEmployees = UserBranches::whereIn('branch_id', $branches)->get()->pluck('user_id')->toArray();
        }
    
        if ($search_term){
            $results = User::whereIn('id', $availableEmployees)->where('name', 'LIKE', '%'.$search_term.'%')->where('active', 1)->paginate(10);
        } else {
            $results = User::whereIn('id', $availableEmployees)->where('active', 1)->paginate(10);
        }

        return $results;
    }
}
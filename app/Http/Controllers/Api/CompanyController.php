<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');

        if ($search_term){
            $results = Company::where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
            $results = Company::paginate(10);
        }

        return $results;
    }

    public function fixDb()
    {
        $users = \App\Models\User::get();
        foreach($users as $user){
            $uc = new \App\Models\UserCompanies();
            $uc->user_id = $user->id;
            $uc->company_id = $user->company_id;
            $uc->save();

            $ud = new \App\Models\UserDivisions();
            $ud->user_id = $user->id;
            $ud->division_id = $user->division_id;
            $ud->save();

            $ub = new \App\Models\UserBranches();
            $ub->user_id = $user->id;
            $ub->branch_id = $user->branch_id;
            $ub->save();
        }
    }
}
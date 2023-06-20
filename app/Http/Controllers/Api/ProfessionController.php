<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Models\EvaluationPaper;

class ProfessionController extends Controller
{
    public function index(Request $request)
    {
      //  $papers = EvaluationPaper::get()->pluck('profession_id')->toArray();

        $search_term = $request->input('q');

        if ($search_term){
//            $results = Profession::where('name', 'LIKE', '%'.$search_term.'%')->whereNotIn('id', $papers)->paginate(10);
            $results = Profession::where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
//            $results = Profession::whereNotIn('id', $papers)->paginate(10);
            $results = Profession::paginate(10);
        }

        return $results;
    }
}

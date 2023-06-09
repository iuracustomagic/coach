<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EvaluationCriteria;

class CriteriasController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $keys = $request->input('keys');
        if ($search_term){
            $results = EvaluationCriteria::where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
            if($keys){
                $results = EvaluationCriteria::find($request);
            } else {
                $results = EvaluationCriteria::paginate(10);
            }
        }
        return $results;
    }
}
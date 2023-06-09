<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $formData = $request->input('form');
        $division_id = [];

        foreach($formData as $part){
            if($part['name'] === 'divisions[]'){
                $division_id[] = $part['value'];
            }
        }

        if ($search_term){
            $results = Branch::whereIn('division_id', $division_id)->where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);

        } else {
            $results = Branch::whereIn('division_id', $division_id)->paginate(10);
        }

        return $results;
    }
}
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Division;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $formData = $request->input('form');
        $company_id = [];

        foreach($formData as $part){
            if($part['name'] === 'companies[]'){
                $company_id[] = $part['value'];
            }
        }

        if ($search_term){
            $results = Division::whereIn('company_id', $company_id)->where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);

        } else {
            if(!empty($company_id)){
                $results = Division::whereIn('company_id', $company_id)->paginate(10);
            } else {
                $results = Division::paginate(10);
            }
        }

        return $results;
    }
}
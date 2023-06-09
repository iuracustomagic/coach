<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Locality;

class LocalityController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $formData = $request->input('form');
        $region_id = 1;

        foreach($formData as $part){
            if($part['name'] === 'region_id'){
                $region_id = $part['value'];
            }
        }

        if ($search_term){
            $results = Locality::where([
                ['name', 'LIKE', '%'.$search_term.'%'],
                ['region_id', '=', $region_id]
            ])->paginate(10);

        } else {
            $results = Locality::where('region_id', $region_id)->paginate(10);
        }

        return $results;
    }
}
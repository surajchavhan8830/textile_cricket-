<?php

namespace App\Http\Controllers\cricket;

use App\Http\Controllers\Controller;
use App\Models\TournamentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TournamentTypeController extends Controller
{
    public function index_tournament_type()
    {
        $type = TournamentType::all();
        
        return json_encode([
            'success' => true,
            'message' => 'Tournament Type List',
            'data' => $type
        ]);
    }

    public function create_tournament_type(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required'
        ]);

        if($validator->fails())
        {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{
            $type = new TournamentType();
            $type->name = $request->name;
            $type->description	= $request->description;
            $type->save();

            return json_encode([
                'success' => true,
                'message' => 'Tornament Type Added Successfully'
            ]);
        }
    }

    public function update_tournament_type($id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required'
        ]);

        if($validator->fails())
        {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{
            $type = TournamentType::find($id);
            $type->name = $request->name;
            $type->description	= $request->description;
            $type->save();

            return json_encode([
                'success' => true,
                'message' => 'Tornament Type Updated Successfully'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\cricket;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\MatchInformation;
use App\Models\Team;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{

    public function index_tournament(Request $request)
    {
        $user = $request->user_id;
        $tournament = Tournament::with('tournamenttype','user')->orderBy('strat_date')->orderBy('id')->where('created_by', $user)->get();

        // $tournament->transform(function ($tournament) {
        //     $tournament->stratdate = date('d-m-Y', strtotime($tournament->start_date));

        //     return $tournament;
        // });
     
        return response()->json([
            'success' => true,
            'message' => 'Tournament List',
            'data'    => $tournament
        ]);
    }


    public function index_all_tournament()
    {
        $tournament = Tournament::with('tournamenttype','user')->where('is_delete', 1)->orderBy('strat_date', 'desc')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Tournament List',
            'data'    => $tournament
        ]);
    }

        public function tournament_details($id)
        {
            $tournament = Tournament::where('id', $id)->with('tournamenttype','user')->orderBy('strat_date', 'desc')->orderBy('id')->get();
            
            $matchinfo = MatchInformation::where('tournament_id', $id)->where('is_delete', 1)->with('user','tournament','team1','team2', 'match_status')->get();
            $team = Team::where('tournament_id', $id)->where('is_delete', 1)->with('user','teamplaye')->get();

            $teamA = Team::where('tournament_id', $id)->where('group_id', '=', "Group A")->where('is_delete', 1)->with('user','teamplaye')->get();

            // $teamA = Team::where('tournament_id', $id)->where('group_id', '=', "Group A")->where('is_delete', 1)->with('user','teamplaye')->get()->toArray();

        return response()->json([
            'success' => false,
            'message' => 'Teams',
            'tournament' => $tournament,
            'matchinfo' => $matchinfo,
            'team' => $team,
            'teamA' => $teamA
        ]);
        }

    public function tournamentsrc(Request $request)
    {
        $tournament = Tournament::with('tournamenttype');
        if(!empty($request->search))
        {
            $tournament->where('tournament_name', 'LIKE', '%' . $request->search. '%');
            
        }
        
        $tournament = $tournament->get();

        if(empty($tournament) || $request->search == null)
        {
            json_encode([
                'success' => false,
                'message' => 'Data Not Found'
            ]);
        }
    }
    public function create_tournament(Request $request)
    {
       
       $validator = Validator::make($request->all(),[
        'tournament_name' => 'required',
        'location'        => 'required',
        'address'         => 'required'
       ]);

       if($validator->fails())
       {
        return json_encode([
            'success' => false,
            'message' => $validator->messages()
        ]);
       }else{
        $tournament = new Tournament();
        $tournament->tournament_name       = $request->tournament_name;
        $tournament->created_by            = $request->created_by;
        $tournament->location              = $request->location;
        // $tournament->tournament_type_id    = $request->tournament_type_id;
        $tournament->ball_type             = $request->ball_type;
        $tournament->cricket_type          = $request->cricket_type;
        $tournament->description           = $request->description;
        $tournament->strat_date            = $request->strat_date;
        $tournament->end_date              = $request->end_date;
        $tournament->due_date              = $request->due_date;
        $tournament->address               = $request->address;
        $tournament->status                = $request->status;
        $tournament->organization_name     = $request->organization_name;
        $tournament->organization_number   = $request->organization_number;
        $tournament->cricket_type          = $request->Cricket_type;
        $tournament->ball_type             = $request->ball_type;
        if($request->hasFile('logo')) {
            $file = $request->file('logo');
            $tournament_photo = time() . $file->getClientOriginalName();
            $tournament->logo           = $tournament_photo;
            $file->move(public_path() .  "/assets/tournament/", $tournament_photo);
        }else{
            $tournament->logo = "default.png";
        }
        $tournament->save();

        return json_encode([
            'success' => false,
            'message' => 'Tournament Added successfully'
        ]);
       }
    }

    public function update_tournament($id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'tournament_name' => 'required',
            'location'        => 'required',
            
        ]);

        if($validator->fails())
        {
            return json_encode([
                'success' =>false,
                'message' => $validator->messages()
            ]);
        }else{
            $tournament = Tournament::find($id);
            $tournament->tournament_name       = $request->tournament_name;
            $tournament->created_by            = $request->created_by;
            $tournament->location              = $request->location;
            $tournament->tournament_type_id    = $request->tournament_type_id;
            $tournament->ball_type             = $request->ball_type;
            $tournament->cricket_type          = $request->cricket_type;
            $tournament->description           = $request->description;
            $tournament->strat_date            = $request->strat_date;
            $tournament->end_date              = $request->end_date;
            $tournament->due_date              = $request->due_date;
            $tournament->status                = $request->status;
            $tournament->address               = $request->address;
            $tournament->organization_name     = $request->organization_name;
            $tournament->organization_number   = $request->organization_number;
            $tournament->cricket_type          = $request->Cricket_type;
            if($request->hasFile('logo')) {
                $file = $request->file('logo');
                $tournament_photo = time() . $file->getClientOriginalName();
                $tournament->logo           = $tournament_photo;
                $file->move(public_path() .  "/assets/tournament/", $tournament_photo);
            }else{
                $tournament->logo = "default.png";
            }
            $tournament->save();
    

            return json_encode([
                'message' => 'Tournament Updated Successfully',
                'success' => true
            ]);
        }
    }

    public function tournament_delete(Request $request)
    {
        $tournament_id = $request->tournament_id;
        $tournament = Tournament::find($tournament_id);
        $tournament->is_delete = 0;
        $tournament->save();

        return json_encode([
            'success' => true,
            'message' => 'Tournament Deleted Successfully'
        ]);
    }
}
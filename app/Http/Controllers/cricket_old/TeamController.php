<?php

namespace App\Http\Controllers\cricket;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{

    public function index_team(Request $request, $id)
    {
        $user = $request->user_id;

        $team = Team::where('tournament_id', $id)->where('created_by', $user)
            ->where('is_delete', 1)
            ->with('user', 'teamplaye')->get();



        return response()->json([
            'success' => false,
            'message' => 'Teams',
            'data' => $team
        ]);
    }


    public function create_team(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_name' => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $team = new Team();
            $team->team_name     = $request->team_name;
            $team->tournament_id = $request->tournament_id;
            $team->created_by    = $request->created_by;
            $team->short_name    = $request->short_name;
            $team->status        = $request->status;
            $team->group_id      = $request->group_id;
            $team->team_owner    = $request->team_owner;



            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $player_photo = time() . $file->getClientOriginalName();
                $team->logo           = $player_photo;
                $file->move(public_path() .  "/assets/team/", $player_photo);
            } else {
                $team->logo = "default.png";
            }
            $team->save();

            return json_encode([
                'success' => true,
                'message' => 'Team Added Successfully'
            ]);
        }
    }

    public function update_team($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_name' => 'required'
        ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $team = Team::find($id);
            $team->team_name     = $request->team_name;
            $team->tournament_id = $request->tournament_id;
            $team->created_by    = $request->created_by;
            $team->short_name    = $request->short_name;
            $team->status        = $request->status;
            $team->group_id         = $request->group_id;
            $team->team_owner    = $request->team_owner;
            $team->save();

            if ($request->has('logo')) {
                $file = $request->file('logo');
                $team_photo = time() . $file->getClientOriginalName();
                $team->logo =  $team_photo;
                $file->move(public_path() . "/assets/team" . $team_photo);
            }

            return json_encode([
                'success' => true,
                'message' => 'Team Updated Successfully'
            ]);
        }
    }

    public function team_delete(Request $request)
    {
        $team_id = $request->team_id;
        $team = Team::find($team_id);
        $team->is_delete = 0;
        $team->save();

        return json_encode([
            'success' => true,
            'message' => 'Team Deleted Successfully'
        ]);
    }

    public function groupbyteam(Request $request, $id)
    {
        $user = $request->user_id;

        $team_A = Team::where('tournament_id', $id)
            ->where('is_delete', 1)
            ->where('group_id', "Group 1")
            ->with('user', 'teamplaye')->get();

        $team_B = Team::where('tournament_id', $id)
            ->where('is_delete', 1)
            ->where('group_id', "Group 2")
            ->with('user', 'teamplaye')->get();

        $team_C = Team::where('tournament_id', $id)
            ->where('is_delete', 1)
            ->where('group_id', "Group 3")
            ->with('user', 'teamplaye')->get();
        $team_D = Team::where('tournament_id', $id)
            ->where('is_delete', 1)
            ->where('group_id', "Group 4")
            ->with('user', 'teamplaye')->get();

        $ganeral = Team::where('tournament_id', $id)
            ->where('is_delete', 1)
            ->where('group_id', " ")
            ->with('user', 'teamplaye')->get();




        return response()->json([
            'success' => false,
            'message' => 'Teams',
            'team_A' => $team_A,
            'team_B' => $team_B,
            'team_C' => $team_C,
            'team_D' => $team_D,
            'genaral' => $ganeral
        ]);
    }



    //  public function sequence(Request $request)
    //     {
    //         $sequenc = Team::find($request->team_id);
    //         $sequenc->order_sequence = $request->order_sequence;
    //         $sequenc->save();

    //         return json_encode([
    //             'success' => true,
    //             'message' => 'Order Sequence Updated'
    //         ]);
    //     }

}

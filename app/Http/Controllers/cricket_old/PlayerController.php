<?php

namespace App\Http\Controllers\cricket;

use App\Http\Controllers\Controller;
use App\Models\MatchInformation;
use App\Models\Player;
use App\Models\TeamPlayer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index_players(Request $request)
    {
        $user = $request->user_id;
        $player = Player::where('created_by', $user)->where('is_delete', 1)->with('user', 'playerteam')->get();

        return response()->json([
            'success' => true,
            'message' => 'Players',
            'data' => $player

        ]);
    }

    public function user_check(Request $request)
    {
        $mobile = $request->mobile_number;

        $player_details = Player::where('mobile_number', $mobile)->first();

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'unique:players',
        ], [
            'mobile_number.unique'   => 'Account already exists for this mobile number.',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Your Profile',
                'data'    => $player_details
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Add Your details',
            ]);
        }
    }

    public function player(Request $request)
    {
        $player_id = $request->player_id;
        $player = Player::where('id', $player_id)->with('user', 'playerteam')->first();
        // dd(strtotime($player->bob));

        $dateOfBirth = date('Y-m-d', strtotime($player->bob));
        $years = Carbon::parse($dateOfBirth)->age;
        $player->cal_age = $years;

        //    $playerTeam = TeamPlayer::where('player_id', $player_id)->get()->toArray();
        //    dd($playerTeam);

        // $teams= DB::table('team_player')
        $teams = TeamPlayer::join('team', 'team_player.team_id', '=', 'team.id')
            ->join('players', 'team_player.player_id', '=', 'players.id')
            ->join('tournaments', 'team_player.tournament_id', '=', 'tournaments.id')
            ->select('team.id as team_id', 'team.team_name as team_name', 'players.player_name as player_name', 'players.id', 'team.short_name', 'tournaments.tournament_name')
            ->where('players.id', '=', $player_id)
            ->get();


        $matchs = [];
        foreach ($teams as $team) {

            $matchs_new = MatchInformation::where('team_1', $team->team_id)
                ->where(function ($query) use ($team) {
                    $query->where('team_1', $team->team_id)
                        ->orWhere('team_2', $team->team_id);
                })
                ->where('is_delete', 1)
                ->with('match_status', 'team1', 'team2','tournament')
                ->get();
 
            array_push($matchs, $matchs_new);
        }


        return response()->json([
            'success' => true,
            'message' => 'profile',
            '       ata' => $player,
            'teams' => $teams,
            'matchs' => $matchs
        ]);
    }

    public function playersrc(Request $request)
    {
        $player = Player::with('user', 'playerteam');
        $teamId = $request->team_id;

        if (!empty($request->search)) {
            $player->where('player_name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('nickname', 'LIKE', '%' . $request->search . '%')
                ->orWhere('mobile_number', 'LIKE', '%' . $request->search . '%');
        }


        $player = $player->get();

        if (empty($player) || $request->search == null) {
            return "Data is not available";
        } else {
            return response()->json([
                'success' => true,
                'message' => "Data",
                "data" => $player
            ]);
        }
    }

    public function create_players(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'player_name' => 'required',
            'mobile_number' => 'required|unique:players',
        ], [
            'mobile_number.unique'   => 'Account already exists for this mobile number.',

        ]);
        if ($validator->fails()) {

            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $player = new Player();
            $player->created_by     = $request->created_by;
            $player->player_name    = $request->player_name;
            $player->skills         = $request->skills;
            $player->bowling_style    = $request->bowling_style;
            $player->batting_style  = $request->batting_style;
            $player->Bowling_pace   = $request->Bowling_pace;
            $player->first_preference = $request->first_preference;
            $player->wicket_keeper  = $request->wicket_keeper;
            $player->cap_experience = $request->cap_experience;
            $player->nickname       = $request->nickname;
            $player->mobile_number  = $request->mobile_number;
            $player->weight         = $request->weight;
            // $player->age            = $request->age;
            $player->visti_no       = $request->visti_no;
            $player->email          = $request->email;
            $player->weight         = $request->weight;
            $player->city            = $request->city;
            $player->bob            = $request->bob;
            $player->visti_no       = $request->visti_no;
            $player->email          = $request->email;
            $player->status         = $request->status;
            $player->save();

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $player_photo = time() . $file->getClientOriginalName();
                $player->logo           = $player_photo;
                $file->move(public_path() .  "/assets/player/", $player_photo);
            } else {
                $player->logo = "default.png";
            }

            $player->save();


            return json_encode([
                'success' => true,
                'message' => 'Player Added Successfully'
            ]);
        }
    }

    public function update_players(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'player_name' => 'required',
        ]);

        if ($validator->fails()) {
            json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $player =  Player::find($id);
            $player->created_by     = $request->created_by;
            $player->player_name    = $request->player_name;
            $player->skills         = $request->skills;
            $player->bowling_style    = $request->bowling_style;
            $player->batting_style  = $request->batting_style;
            $player->Bowling_pace   = $request->Bowling_pace;
            $player->first_preference = $request->first_preference;
            $player->wicket_keeper  = $request->wicket_keeper;
            $player->cap_experience = $request->cap_experience;
            $player->nickname       = $request->nickname;
            $player->mobile_number  = $request->mobile_number;
            $player->weight         = $request->weight;
            // $player->age            = $request->age; 
            $player->visti_no       = $request->visti_no;
            $player->email          = $request->email;
            $player->weight         = $request->weight;
            $player->city            = $request->city;
            $player->bob            = $request->bob;
            $player->visti_no       = $request->visti_no;
            $player->email          = $request->email;
            $player->status         = $request->status;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $player_photo = time() . $file->getClientOriginalName();
                $player->logo           = $player_photo;
                $file->move(public_path() .  "/assets/player/", $player_photo);
            } else {
                $player->logo = "default.png";
            }
            $player->save();

            return json_encode([
                'success' => true,
                'message' => 'Player Updated Successfully'
            ]);
        }
    }

    public function playerdropdowndetails()
    {
        $batting_style_list = [
            "Right-handed batsman",
            "Left-handed batsman",
            "None",

        ];

        $bowling_style_list = [
            "Right-handed bowler",
            "Left-handed bowler",
            "None",

        ];

        $cricketer_roles = [
            "Batsman",
            "Bowler",
            "All-rounder",
            "Spin bowler",
            "Fast bowler",
            "Swing bowler",
        ];

        $bowling_pace_list = [
            "Fast bowler",
            "Medium-fast bowler",
            "Medium-pace bowler",
            "Seam bowler",
            "Swing bowler",
            "Left-arm fast bowler",
            "Left-arm medium-fast bowler",
            "Right-arm fast bowler",
            "Right-arm medium-fast bowler",
            "Left-arm fast-medium bowler",
            "Right-arm fast-medium bowler",
        ];
        return response()->json([
            'success' => true,
            'message' => 'Drop Down',
            'batting_style_list' => $batting_style_list,
            'bowling_style_list' => $bowling_style_list,
            'cricketer_roles' => $cricketer_roles,
            'bowling_pace_list' =>  $bowling_pace_list,
        ]);
    }

    public function player_delete(Request $request)
    {
        $player = $request->player_id;

        $player = Player::find($player);
        $player->is_delete = 0;
        $player->save();

        return json_encode([
            'success' => true,
            'message' => 'Player Deleted Successfully'
        ]);
    }
}

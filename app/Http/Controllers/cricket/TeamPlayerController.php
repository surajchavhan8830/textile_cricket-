<?php

namespace App\Http\Controllers\cricket;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\playerPosition;
use App\Models\TeamPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Team;
use Illuminate\Validation\Rule;


class TeamPlayerController extends Controller
{

    public function index_team_player($id)
    {
        $teamplayer = TeamPlayer::where('team_id', $id)->with('team','player')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Teams',
            'date' => $teamplayer
        ]);
    }

    public function create_team_player(Request $request)
    {
        $player_id = $request->player_id;
         $tournament_id = $request->tournament_id;


    //   $player_avl_check = TeamPlayer::where('player_id', $player_id)->first();
    //   if($player_avl_check)
    //   {
    //   $team_name = Team::select('team_name')->where('id', $player_avl_check->team_id)->first(); 
      
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'This player has already been selected in ' . $team_name,
    //     ]);
    //   }else{
        
        $validator = Validator::make($request->all(),[
            'team_id' => 'required',
            'player_id' => Rule::unique('team_player')->where(function ($query) use ($tournament_id) {
                return $query->where('tournament_id', $tournament_id);
            }),
        ]);

        if($validator->fails())
        {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{
            $teamplayer = new TeamPlayer();
            $teamplayer->team_id = $request->team_id;
            $teamplayer->player_id = $request->player_id;
            $teamplayer->points = $request->points;
            $teamplayer->player_roll = $request->player_roll;
            $teamplayer->postion = $request->postion;
            $teamplayer->sequence = $request->sequence;
            $teamplayer->is_extra = $request->is_extra;
            $teamplayer->tournament_id = $request->tournament_id;
            $teamplayer->save();

            return json_encode([
                'success' => true,
                'message' => 'Players Team created'
            ]);

        }
       
    }

    public function update_team_player(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'team_id' => 'required',
        ]);

        if($validator->fails())
        {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{

            $teamplayer = TeamPlayer::find($id);
            $teamplayer->team_id = $request->team_id;
            $teamplayer->player_id = $request->player_id;
            $teamplayer->points = $request->points;
            $teamplayer->player_roll = $request->player_roll;
            $teamplayer->postion = $request->postion;
            $teamplayer->sequence = $request->sequence;
            $teamplayer->is_extra = $request->is_extra;
            $teamplayer->tournament_id = $request->tournament_id;
            $teamplayer->save();

            return json_encode([
                'success' => true,
                'message' => 'Players Team Updated'
            ]);

        }
    }

    public function team_wise_player(Request $request)
    {
        $team_id = $request->team_id;
        $teamplayer = TeamPlayer::where('team_id', $team_id)->where('postion', "!=", 5 )->orderBy('sequence')->orderBy('id')->with('team','player','playerrole')->get();
        $teamEXplayer = TeamPlayer::where('team_id', $team_id)->where('postion', 5)->with('team', 'player', 'playerrole')->get();

        $teamplayer->transform(function ($teamplayer) {
            $teamplayer->roll = $teamplayer->playerrole->position_name;
            $teamplayer->team = $teamplayer->team->team_name;

            return $teamplayer;
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Teams',
            'date' => $teamplayer,
            'extra' => $teamEXplayer
        ]);
    }

    public function add_player(Request $request)
    {
        $team_id = $request->team_id;
        $tournament_id = $request->tournament_id;
        // dd($tournament_id);

        $validator = Validator::make($request->all(), [
            'player_id' => Rule::unique('team_player')->where(function ($query) use ($team_id, $tournament_id) {
                return $query->where('tournament_id', $tournament_id);
            }),
        ],[
            'player_id.unique'   => 'This player has already been selected',
        ]);

        if($validator->fails()) {   
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        }else{
        $player_id = $request->player_id;
        $player = Player::where('id', $player_id)->first();
        $addplayer = new TeamPlayer();
        $addplayer->team_id    = $request->team_id;
        $addplayer->player_id  = $request->player_id;
        $addplayer->player_roll= $player->skills;
        $addplayer->tournament_id = $request->tournament_id;
        $addplayer->postion  = 4;
        $addplayer->save();     

        return response()->json([
            'success' => true,
            'message' => 'Player Added Successfully !'
        ]);

    }
    }

    public function playerRole()
    {
        

        $player_role = playerPosition::select('id', 'position_name')->get();

        return response()->json([
            'success' => true,
            'data'    => $player_role
        ]);
        
    }
    
     public function remove_team_player(Request $request, $id)
    {
       

            $teamplayer = TeamPlayer::find($id);
            $teamplayer->delete();
 
            return json_encode([
                'success' => true,
                'message' => 'Player Removed from the Team'
            ]);

        
    }

    public function editRole(Request $request, $id)
    {
      if($request->position != 5 && $request->position != 6 && $request->position != 3 )
      {
        $changeRole = TeamPlayer::where('postion', $request->position)->where('team_id', $request->team_id)->first();
        if($changeRole)
        {
           $editrole = TeamPlayer::find($changeRole->id);
           $editrole->postion = 4;
           $editrole->save(); 
        }
        
        $editrole = TeamPlayer::find($id);
        $editrole->postion = $request->position;
        $editrole->save();
      }elseif($request->position == 3){

        $changeRole = TeamPlayer::where('postion', $request->position)
                                 ->where('is_wicketkeeper', 1)
                                 ->where('team_id', $request->team_id)->first();
    
                                 if(!$changeRole){
                                    $changeRole = TeamPlayer::
                                    where('is_wicketkeeper', 1)
                                    ->where('team_id', $request->team_id)->first();
                                    
                                 }

                                 
        if($changeRole)
        {
           $editrole = TeamPlayer::find($changeRole->id);
           if($editrole->postion == 3 && $editrole->is_wicketkeeper == 1)
           {
            $editrole->postion = 3;
            $editrole->is_wicketkeeper = 0;
           }
           $editrole->is_wicketkeeper = 0;
           $editrole->save(); 
        }
    
        $editrole = TeamPlayer::find($id);
            $editrole->is_wicketkeeper	 = 1;
            if($editrole->postion == 3)
            {
                
                $editrole->postion = 3;
            }else{
                $editrole->postion = $editrole->postion;

            }
            $editrole->save();
      }
      else{
        $editrole = TeamPlayer::find($id);
        $editrole->postion = $request->position;
        $editrole->save();
      }
         

        return response()->json([
            'success' => true,
            'message' => "Player Role Updated"
        ]);
    }
    
    public function order_sequence(Request $request)
    {
        $array = $request->order_by;
        $team_id = $request->team_id;

        foreach($array as $key => $player_id)
        {
            $id = TeamPlayer::where('player_id', $player_id)->where('team_id', $team_id)->first();
            $id->sequence = $key;
            $id->save();
        }

        return json_encode([
            'success' => true,
            'message' => 'Players Reordered'
        ]);
    }

}
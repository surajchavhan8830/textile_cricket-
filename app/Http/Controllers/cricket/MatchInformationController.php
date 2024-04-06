<?php

namespace App\Http\Controllers\cricket;

use App\Http\Controllers\Controller;
use App\Models\MatchInformation;
use App\Models\MatchOver;
use App\Models\Sechedule;
use App\Models\MatchBatsman;
use App\Models\MatchBowler;
use App\Models\MatchHistory;
use App\Models\Team;
use App\Models\TeamPlayer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class MatchInformationController extends Controller
{
    public function index_match_info(Request $request, $id)
    {

        $user = $request->user_id;

        $matchinfo = MatchInformation::where('tournament_id', $id)->where('is_delete', 1)
            ->where('created_by', $user)->with('user', 'tournament', 'team1', 'team2', 'match_status')
            ->whereIn('match_status', [0, 1])
            ->get();

        $matchinfoPast = MatchInformation::where('tournament_id', $id)->where('is_delete', 1)
            ->where('created_by', $user)->with('user', 'tournament', 'team1', 'team2', 'match_status')
            ->whereNotIn('match_status', [0, 1])
            ->get();



        return response()->json([
            'success' => true,
            'message' => 'Match information',
            'data' => $matchinfo,
            'past' => $matchinfoPast,
        ]);
    }
    public function create_match_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'created_by' => 'required',
            'tournament_id' => 'required',
            'team_1' => 'required',
            'team_2' => 'required',
            'match_date' => 'required',
            'umpires' => 'required',
        ]);
        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {

            $matchinfo                 = new MatchInformation();
            $matchinfo->created_by     = $request->created_by;
            $matchinfo->tournament_id  = $request->tournament_id;
            $matchinfo->team_1         = $request->team_1;
            $matchinfo->team_2         = $request->team_2;
            $matchinfo->match_date     = $request->match_date;
            $matchinfo->match_time     = $request->match_time;
            $matchinfo->venue          = $request->venue;
            $matchinfo->description    = $request->description;
            $matchinfo->umpires        = $request->umpires;
            $matchinfo->venue          = $request->venue;
            $matchinfo->overseas       = $request->overseas;
            $matchinfo->match_type     = $request->match_type;
            $matchinfo->save();

            return json_encode([
                'success' => true,
                'message' => "Match info Added"
            ]);
        }
    }

    public function update_match_info(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'created_by' => 'required',
            'tournament_id' => 'required',
            'team_1' => 'required',
            'team_2' => 'required',
            'match_date' => 'required',
            'umpires' => 'required',
        ]);
        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $matchinfo = MatchInformation::find($id);
            $matchinfo->created_by  = $request->created_by;
            $matchinfo->tournament_id  = $request->tournament_id;
            $matchinfo->description  = $request->description;
            $matchinfo->team_1  = $request->team_1;
            $matchinfo->team_2  = $request->team_2;
            $matchinfo->match_date  = $request->match_date;
            $matchinfo->match_time     = $request->match_time;
            $matchinfo->umpires  = $request->umpires;
            $matchinfo->venue          = $request->venue;
            $matchinfo->overseas       = $request->overseas;
            $matchinfo->match_type     = $request->match_type;
            $matchinfo->save();

            return json_encode([
                'success' => true,
                'message' => "Match information Updated"
            ]);
        }
    }

    public function match_status()
    {
        $match_status = Sechedule::all();

        return response()->json([
            'success' => true,
            'message' => 'status',
            'data' => $match_status
        ]);
    }

    public function match_delete(Request $request)
    {
        $match_id = $request->match_id;
        $match = MatchInformation::find($match_id);
        $team_1 = $match->team_1;
        $team_2 = $match->team_2;
        $team_1_points = $match->team_1_point;
        $team_2_points = $match->team_2_point;

        // SUM -> TEAM1 OR TEAM2 -> Count
        $team_one = Team::find($team_1);
        $old_points = $team_one->point;
        $team_one->point  = $old_points - $team_1_points;
        $team_one->save();

        $team_two = Team::find($team_2);
        $old_points = $team_two->point;
        $team_two->point  = $old_points - $team_2_points;
        $team_two->save();


        $match->is_delete = 0;
        $match->save();

        return json_encode([
            'success' => true,
            'message' => "Match Deleted Updated"
        ]);
    }

    // STEP:2 Assign Betsman & Bowler
    public function Add_match_player(Request $request)
    {


        $batsman = new MatchBatsman();
        $batsman->player_id     = $request->sticker_player_id;
        $batsman->team_id       = $request->betting_team_id;
        $batsman->tournament_id = $request->tournament_id;
        $batsman->match_id      = $request->match_id;
        $batsman->save();

        $batsman = new MatchBatsman();
        $batsman->player_id     = $request->nonsticker_player_id;
        $batsman->team_id       = $request->betting_team_id;
        $batsman->tournament_id = $request->tournament_id;
        $batsman->match_id      = $request->match_id;
        $batsman->save();

        $bowler = new MatchBowler();
        $bowler->player_id     = $request->bowler_player_id;
        $bowler->team_id       = $request->bowling_team_id;
        $bowler->tournament_id = $request->tournament_id;
        $bowler->match_id = $request->match_id;
        $bowler->save();

        $matchinfo = MatchInformation::find($request->match_id);
        $matchinfo->sticker_player_id = $request->sticker_player_id;
        $matchinfo->nonsticker_player_id = $request->nonsticker_player_id;
        $matchinfo->bowler_id = $request->bowler_player_id;
        $matchinfo->runing_over = 0;
        $matchinfo->save();

        return response()->json([
            'message' => 'Players Added',
            'success' => true
        ]);
    }

    // STEP:3 Match OVER
    public function add_match_over(Request $request)
    {
        $history_id = $this->add_ball_history($request);
        // dd($history_id);

        $match_id = $request->match_ids;
        $match_info = MatchInformation::find($match_id);
        $current_over = 1;
        $current_ball = 0;
        $is_new_over = false;
        $is_normal_ball_delivery = false;
        $is_extra_ball_delivery = false;

        if ($match_info) {
            $current_over = (int) $match_info->runing_over;
            $current_ball =  ($match_info->runing_over - (int)$match_info->runing_over) * 10;
            $current_ball++;
        }
        // dd(($current_over) . '.' . $current_ball);
        // CURRENT BALL
        // --- --- --- CREATING NEW OVER -> BALL
        $matchover = new MatchOver();
        $matchover->sticker_player_id = $request->sticker_player_id;
        $matchover->nonsticker_player_id = $request->nonsticker_player_id;
        if ($request->ball_type == "by" || $request->ball_type == "lb") {
            $matchover->run = 0;
        } else {
            $matchover->run = $request->run;
        }
        $matchover->ball_type = $request->ball_type;    // NORMAL, WIDE, NB
        $matchover->bowler_player_id = $request->bowler_player_id;
        $matchover->over_number = $current_over;    //$request->over_number;
        $matchover->ball_number = $current_ball;    //$request->ball_number;
        $matchover->team_id = $request->team_id;
        // if (!empty($request->no_ball_type)) {
        //     $matchover->no_ball_type = $request->no_ball_type;
        // }
        // --- INCASE OF OUT ONLY
        $matchover->out_type = $request->out_type;
        $matchover->out_by_player_id = $request->out_by_player_id;
        $matchover->out_player_id = $request->out_player_id;
        // --- INCASE OF OUT ONLY
        // if($request->ball_type == "normal" || $request->ball_type == "by" || $request->ball_type == "leg_by")
        $bowler_run = 0;
        $batsman_run = 0;
        $bowler_ball_count = 0;
        $batsman_ball_count = 0;
        $team_run = 0;

        if ($request->ball_type == "normal") {
            $is_normal_ball_delivery = true;
            $matchover->is_normal = 1;

            $bowler_run = $request->run;
            $batsman_run = $request->run;
            $bowler_ball_count = 1;
            $batsman_ball_count = 1;
            $team_run = 0;
        }
        if ($request->ball_type == "wb") {
            
            if ($request->out_type == "stumped" || $request->out_type == "hitwicket" ) {
                $request->run = 0;
            }
            
            $is_extra_ball_delivery = true;
            $matchover->is_extra = 1;

            $bowler_run = $request->run + 1;        // $request->run
            $batsman_run = 0;
            $bowler_ball_count = 0;
            $batsman_ball_count = 0;
            $team_run = $request->run + 1;
        }

        if ($request->ball_type == "nb") {
            $is_extra_ball_delivery = true;
            $matchover->is_extra = 1;

            $bowler_run = 1;  // extra ball run
            $batsman_run = 0;
            $bowler_ball_count = 0;
            $batsman_ball_count = 1;
            // IF BY BAT / BY / LB
            $team_run = $request->run + 1;
            if ($request->no_ball_type == "bat") {
                $bowler_run = $request->run + 1;
                $batsman_run = $request->run;
                // $batsman_ball_count = 1;
                $team_run = 1;
            } else if ($request->no_ball_type == "byes" || $request->no_ball_type == "legbyes") {
                // $batsman_ball_count = 1;
            }
        }


        if ($request->ball_type == "by" || $request->ball_type == "lb") {
            $is_normal_ball_delivery = true;
            $matchover->is_normal = 1;      // ARJUN

            $matchover->is_extra = $request->run;

            $bowler_run = 0;
            $batsman_run = 0;
            $bowler_ball_count = 1;
            $batsman_ball_count = 1;

            $team_run = $request->run;
        }

        $matchover->match_ids = $match_id;
        $matchover->save();
        $this->add_ball_history_update($history_id, $matchover->id);
        // --- --- --- CREATING NEW OVER -> BALL

        // --- --- --- CHECKING FOR NEW OVER
        $current_over_total_normal_balls = MatchOver::where('over_number', $current_over)->where('is_normal', 1)->where('team_id', $request->betting_team_id)->where('match_ids', $match_id)->count();
        if ($current_over_total_normal_balls >= 6)

            $is_new_over = 1;
        // <--- --- --- CHECKING FOR NEW OVER--- --- --- >



        if ($is_normal_ball_delivery)
            $running_over = ($current_over) . '.' . $current_ball;

        else
            $running_over = ($current_over) . '.' . ($current_ball - 1);        // NOT CONSIDIRING OTHER BALL

        if ($is_new_over) {
            $running_over = $current_over + 1;
        }

        $count_out = ($request->out_type == null) ? 0 : 1;



        $match_info_update = MatchInformation::find($request->match_ids);
        $match_info_update->sticker_player_id = $request->sticker_player_id;
        $match_info_update->nonsticker_player_id = $request->nonsticker_player_id;
        $match_info_update->bowler_id = $request->bowler_player_id;

        if ($request->betting_team_id == $match_info->team_1) {
            $match_info_update->team_1_total_run = $match_info_update->team_1_total_run + $batsman_run;
            $match_info_update->team_1_extra_run = $match_info_update->team_1_extra_run + $team_run; //1;
            $match_info_update->team_1_total_wickets = $match_info_update->team_1_total_wickets + $count_out;
            $match_info_update->team_1_total_over = $running_over;
            // $match_info_update->team_1_total_over = $request->over_number;
        } else if ($request->betting_team_id == $match_info->team_2) {

            $match_info_update->team_2_total_run = $match_info_update->team_2_total_run + $batsman_run;
            $match_info_update->team_2_extra_run = $match_info_update->team_2_extra_run + $team_run;    //1;
            $match_info_update->team_2_total_wickets = $match_info_update->team_2_total_wickets + $count_out;
            $match_info_update->team_2_total_over = $running_over;
        }

        $match_info_update->runing_over = $running_over;
        $match_info_update->save();

        // dd($match_info_update);
        $six  = ($batsman_run == 6) ? 1 : 0;
        $four = ($batsman_run == 4) ? 1 : 0;


        // --- --- --- UPDATE BETSMAN SUMMARY 
        // dd($four);
        // TODO: NEED TO CHANGE out_player_id from API
        $matchBatsman = MatchBatsman::where('player_id', $match_info->sticker_player_id)->where('team_id', $request->betting_team_id)->where('match_id', $match_id)->first();
        $matchBatsman->run = $matchBatsman->run +  $batsman_run;
        $matchBatsman->balls = $matchBatsman->balls + $batsman_ball_count;

        // TODO: need to check with NB 4s.
        $matchBatsman->sixers = $matchBatsman->sixers + $six;
        $matchBatsman->fours = $matchBatsman->fours + $four;
        $matchBatsman->save();

        // --- --- --- UPDATE BETSMAN SUMMARY 


        // --- --- --- UPDATE OUT SUMMARY 
        if (!empty($request->out_type)) {
            $matchBatsmanOut = MatchBatsman::where('player_id', $request->out_player_id)->where('team_id', $request->betting_team_id)->where('match_id', $match_id)->first();
            $matchBatsmanOut->type_out = $request->out_type;
            $matchBatsmanOut->out_by_player_id = $request->out_by_player_id;
            $matchBatsmanOut->out_by_bowler_id = $request->bowler_player_id;
            $matchBatsmanOut->save();
        }
        // --- --- --- UPDATE OUT SUMMARY 



        // --- --- --- UPDATE BOWLER SUMMARY 
        $over_match = MatchOver::where('bowler_player_id', $request->bowler_player_id)->where('match_ids', $match_id)->get();

        $over_by_player = $over_match->groupBy('over_number')->count(); // COUNT TOTAL OVER OF BOWLER
        $over_by_run = $over_match->sum('run');
        $over_by_extra = $over_match->sum('is_extra');
        $wicket_by_player = $over_match->whereNotNull('out_type')->where('out_type', '!=', 'runout')->count(); // RUNOUT WICKET NOT COUNT IN BOWLER



        $maiden_overs = false;
        if ($is_new_over) {
            $maiden_overs = MatchOver::select('over_number', DB::raw('SUM(run) as total'))
                ->where('match_ids', $match_id)
                ->where('bowler_player_id', $request->bowler_player_id)
                ->groupBy('over_number')
                ->havingRaw('SUM(run) = 0')         // TODO:REMOVE NB/WIDE QUERY 
                ->havingRaw('SUM(is_extra) = 0')         // TODO:REMOVE NB/WIDE QUERY 
                ->count();
        }

        $matchBowler = MatchBowler::where('player_id', $request->bowler_player_id)->where('team_id', $request->bowling_team_id)->where('match_id', $match_id)->first();
        // dd($matchBowler);
        if ($is_normal_ball_delivery) {
            $matchBowler->overs = ($over_by_player - 1) . '.' . $current_ball;    // TODO: UPDATE BALL
        } else {
            $matchBowler->overs = ($over_by_player - 1) . '.' . ($current_ball - 1);    // TODO: UPDATE BALL
        }
        if ($is_new_over) {
            $matchBowler->overs = $over_by_player;
        }

        $matchBowler->runs = $matchBowler->runs + $bowler_run;



        if (!empty($maiden_overs))
            $matchBowler->maiden_over =  $maiden_overs;

        $matchBowler->wickets = $wicket_by_player;
        $matchBowler->save();

        // --- --- --- UPDATE BOWLER SUMMARY 


        // $is_normal_ball_delivery
        // $is_extra_ball_delivery
        // !empty($request->out_type)
        if ($request->run % 2 == 1) {
            $match_info = MatchInformation::find($request->match_ids);
            $match_info->sticker_player_id = $request->nonsticker_player_id;
            $match_info->nonsticker_player_id = $request->sticker_player_id;
            $match_info->save();
        }

        // TODO: CHECK IF RUNOUT STRICK / NONSTRICK
        // if ($request->run == 2 && $request->out_type == "runout");       // TODO: NEED TO CHECK....
        // { 
        //     $match_info = MatchInformation::find($request->match_ids);   
        //     $match_info->sticker_player_id =  $request->sticker_player_id;
        //     $match_info->nonsticker_player_id = $request->nonsticker_player_id;
        //     $match_info->save();
        // }


        return response()->json([
            'success' => true,
            'is_new_over' => $is_new_over,
            'message' => 'Ball Added'
        ]);
    }

    // STEP:4.1 OUT BETSMAN
    public function newbatsman(Request $request)
    {
        $matchovers = MatchOver::where('match_ids', $request->match_id)->latest()->first();
        if ($matchovers->out_type == "runout") {
            $match_id = $request->match_id;
            $team_id = $request->betting_team_id;
            $new_player_id = $request->player_id;
            $existing_player_id = 0;

            // echo($match_id); die();
            // $matchBatsman = MatchBatsman::where('player_id', $match_info->sticker_player_id)->where('team_id', $team_id)->where('match_id', $match_id)->first();
            // if ($matchBatsman && empty($matchBatsman->out_type)) {
            if ($matchovers->out_player_id == $matchovers->sticker_player_id) {
                $existing_player_id = $matchovers->nonsticker_player_id;
            } else {
                $existing_player_id = $matchovers->sticker_player_id;
            }

            $match_info = MatchInformation::find($match_id);

            if ($request->is_on_strike == 1) {
                $match_info->sticker_player_id = $new_player_id;
                $match_info->nonsticker_player_id = $existing_player_id;
                $match_info->save();
            } else {
                // $match_info = MatchInformation::find($request->match_id);  
                $match_info->sticker_player_id = $existing_player_id;
                $match_info->nonsticker_player_id = $new_player_id;
                $match_info->save();
            }
        } else {      // UPDATEING ON STRIKE
            $match_info = MatchInformation::find($request->match_id);
            $match_info->sticker_player_id  = $request->player_id;
            $match_info->save();
        }

        $batsman = new MatchBatsman();
        $batsman->player_id     = $request->player_id;
        $batsman->team_id       = $request->team_id;
        $batsman->tournament_id = $request->tournament_id;
        $batsman->match_id      = $request->match_id;
        $batsman->save();
        // $matchinfo = MatchInformation::where('tournament_id', $request->tournament_id)->where('is_delete', 1)->where('id', $batsman->match_id)->first();
        // $matchinfo->sticker_player_id  = $request->player_id;
        // $matchinfo->save();

        return response()->json([
            'success' => true,
            'message' => 'Player Added'
        ]);
    }

    // STEP:4.2 COMPLTE OVER -- NEW OVER
    public function newbowler(Request $request)
    {
        $match_info = MatchInformation::find($request->match_id);
        $sticker_player_id = $match_info->sticker_player_id;
        $nonsticker_player_id = $match_info->nonsticker_player_id;
        $match_info->sticker_player_id = $nonsticker_player_id;
        $match_info->nonsticker_player_id = $sticker_player_id;
        $match_info->bowler_id = $request->player_id;
        $match_info->save();


        $bowler_check = MatchBowler::where('player_id', $request->player_id)->where('match_id', $request->match_id)->first();
        if (empty($bowler_check)) {
            $bowler = new MatchBowler();
            $bowler->player_id     = $request->player_id;
            $bowler->team_id       = $request->team_id;
            $bowler->tournament_id = $request->tournament_id;
            $bowler->match_id      = $request->match_id;
            $bowler->save();
        }


        return response()->json([
            'success' => true,
            'message' => 'New Bowler Added'
        ]);
    }


    // STEP:5 COMPLTE INNING
    public function breakreason(Request $request)
    {
        $matchinfo = $request->match_id;
        $matchinfo = MatchInformation::find($matchinfo);
        $matchinfo->break_type = $request->break_type;
        $matchinfo->save();

        return response()->json([
            'success' => true,
            'message' => "Break Time!"
        ]);
    }


    // STEP:5.0 FINSH INNING
    public function inningToss(Request $request)
    {

        $team = \App\Models\Team::find($request->won_toss_team);
        $toss = $team->team_name . ' Wins toss, ' . 'elects to ' . $request->toss_elected;


        $match_id = $request->match_id;
        $matchinfo = MatchInformation::find($match_id);
        $matchinfo->won_toss = $request->won_toss_team;
        $matchinfo->toss_elected = $request->toss_elected;
        $matchinfo->toss = $toss;
        $matchinfo->match_status = 1;
        $matchinfo->inning_id = 1;
        $matchinfo->save();

        return response()->json([
            'success' => true,
            'message' => $team->team_name . ' Wins toss, ' . 'elects to ' . $request->toss_elected
        ]);
    }

    // STEP:5.1 FINSH INNING

    public function finishFirstInning(Request $request)
    {
        $match_id = $request->match_id;

        $matchinfo = MatchInformation::find($match_id);
        $matchinfo->sticker_player_id = null;
        $matchinfo->nonsticker_player_id = null;
        $matchinfo->bowler_id = null;
        $matchinfo->inning_id = 2;
        $matchinfo->runing_over = 0.0;
        $matchinfo->save();

        return response()->json([
            'success' => true,
            'message' => '1st inning is finished'
        ]);
    }

    public function finishSecondInning(Request $request)
    {
        $match_id = $request->match_id;

        $matchinfo = MatchInformation::find($match_id);
        $matchinfo->sticker_player_id = null;
        $matchinfo->nonsticker_player_id = null;
        $matchinfo->bowler_id = null;
        $matchinfo->inning_id = 3;
        $matchinfo->runing_over = 0.0;
        $matchinfo->save();

        return response()->json([
            'success' => true,
            'message' => '2nd inning is finished'
        ]);
    }

    // WON / DRAW / CANCEL
    public function declareResult(Request $request)
    {

        $matchinfo = MatchInformation::find($request->match_id);
        $win_team_id = $request->team_id;
        $matchinfo->won_team_id = $win_team_id;
        $matchinfo->player_of_the_match = $request->player_of_the_match;
        $result = '';

        if ($matchinfo) {
            $matchinfo->match_status = 5;
            // $matchinfo->notes = $request->notes;
            $matchinfo->summary = $request->notes;
            $matchinfo->inning_id = 4;

            if ($request->result == "won" || $request->result == "draw") {
                $win_points = 2;
                $lose_points = 0;
                $draw_points = 1;

                if ($win_team_id == $matchinfo->team_1) {
                    $win_team = $matchinfo->team_1;
                    $lose_team = $matchinfo->team_2;
                } else {
                    $win_team = $matchinfo->team_2;
                    $lose_team = $matchinfo->team_1;
                }

                $matchinfo->team_1_point = ($win_team == $matchinfo->team_1) ? $win_points : $lose_points;
                $matchinfo->team_2_point = ($win_team == $matchinfo->team_2) ? $win_points : $lose_points;


                if ($request->result == "draw") {
                    $matchinfo->team_1_point = $draw_points;
                    $matchinfo->team_2_point = $draw_points;
                }

                $win_team_stats = Team::find($win_team);
                $lose_team_stats = Team::find($lose_team);

                if ($win_team_stats->id == $matchinfo->team_1) {
                    $win_team_stats->point  += $matchinfo->team_1_point;
                    $lose_team_stats->point += $matchinfo->team_2_point;
                } else {
                    $win_team_stats->point  += $matchinfo->team_2_point;
                    $lose_team_stats->point += $matchinfo->team_1_point;
                }

                if ($request->result == "won") {
                    $win_team_stats->total_win += 1;
                    $win_team_stats->total_loss += 0;

                    $lose_team_stats->total_win += 0;
                    $lose_team_stats->total_loss += 1;

                    // $result = $win_team_stats->team_name . " WON Match";
                } elseif ($request->result == "draw") {
                    $win_team_stats->total_draw += 1;
                    $lose_team_stats->total_draw += 1;
                    // $result = "Match draw";
                }

                $win_team_stats->save();
                $lose_team_stats->save();
            } else {
                $result = "Match Canceled";
            }
        }


        // $matchinfo->summary = $result;

        $matchinfo->save();

        return response()->json([
            'success' => true,
            'message' => $matchinfo ? 'Success' : 'Match not found',
            'result' => $result
        ]);
    }

    // STEP:6 MATCH INFO (REPEAT)
    public function matchinfo(Request $request, $id)
    {
        $user = $request->user_id;
        $match_id = $request->match_id;
        // $players = TeamPlayer::where('tournament_id', $id)->where('team_id', )
        $matchinfo = MatchInformation::where('tournament_id', $id)->where('is_delete', 1)->where('id', $match_id)
            // ->where('created_by', $user)
            ->with(
                'user',
                'tournament',
                'team1',
                'team2',
                'match_status',
                'tosswonteam',
                'playerstrick',
                'playerNonStricker',
                'playerBowler',
                'stickerScore',
                'nonstickerScore',
                'bowlerScore',
                'playerofthematch'
            )
            ->first();
        
        if ($matchinfo->player_of_the_match) {
            $player_of_match_team = TeamPlayer::select('id', 'player_id', 'team_id')->with('team')->where('player_id', $matchinfo->playerofthematch->id)->first();
            $matchinfo->player_of_the_match_team = $player_of_match_team->team->short_name;
        } else{
            $matchinfo->player_of_the_match_team = null;
        }

        // --- --- --- CHECKING FOR NEW OVER
        $total_over = $matchinfo->overseas;
        $team_1_run = $matchinfo->team_1_total_run + $matchinfo->team_1_extra_run;
        $team_2_run = $matchinfo->team_2_total_run + $matchinfo->team_2_extra_run;
        $target_run = 0;
        $required_runrate = '0';
        $required_status = '';
        $current_over = 0;
        $current_ball = 0;
        $is_new_over = false;
        $is_bowler_assigned = false;
        $is_new_inning = false;
        $out_player_id = 0;
        $team_1_id = $matchinfo->team_1;
        $players = TeamPlayer::where('team_id', $team_1_id)->where('tournament_id', $id)->count();
        if ($matchinfo) {
            $current_over = (int) $matchinfo->runing_over;
            $current_ball =  ($matchinfo->runing_over - (int)$matchinfo->runing_over) * 10;
            $current_ball++;
        }
        /* ------------------------------------------ */
        $out_player_id = 0;
        $strikeOutCheck = MatchBatsman::where('player_id', $matchinfo->sticker_player_id)->where('match_id', $match_id)->first();
        if (!empty($strikeOutCheck->type_out)) {
            $out_player_id = $strikeOutCheck->player_id;
        } else {
            $nonstrikeOutCheck = MatchBatsman::where('player_id', $matchinfo->nonsticker_player_id)->where('match_id', $match_id)->first();
            if (!empty($nonstrikeOutCheck->type_out)) {
                $out_player_id = $nonstrikeOutCheck->player_id;
            }
        }
        /* ------------------------------------------ */

        /* ------------------------------------------ */
        if ($matchinfo->inning_id == 1) {
            // $matchinfo->inning_id == 1 && $matchinfo->won_toss == $matchinfo->team_2 && strtolower($matchinfo->toss_elected) == "bowl") {
            if (($matchinfo->won_toss == $matchinfo->team_1 && strtolower($matchinfo->toss_elected) == "bat") ||
                ($matchinfo->won_toss == $matchinfo->team_2 && strtolower($matchinfo->toss_elected) == "bowl")
            ) {
                $matchinfo->betting_team_id = $matchinfo->team_1;
                $matchinfo->bowling_team_id = $matchinfo->team_2;
            } else {
                $matchinfo->betting_team_id = $matchinfo->team_2;
                $matchinfo->bowling_team_id = $matchinfo->team_1;
            }
        } else {  // REVERSE OF 1 INNING
            if (($matchinfo->won_toss == $matchinfo->team_1 && strtolower($matchinfo->toss_elected) == "bat") ||
                ($matchinfo->won_toss == $matchinfo->team_2 && strtolower($matchinfo->toss_elected) == "bowl")
            ) {
                $matchinfo->betting_team_id = $matchinfo->team_2;
                $matchinfo->bowling_team_id = $matchinfo->team_1;

                $target_run = $team_1_run + 1;

                $total_due_run = $target_run - $team_2_run;
                $total_due_over = $total_over - (int) $matchinfo->runing_over;
                if ($matchinfo->runing_over > (int) $matchinfo->runing_over) {
                    $total_due_over = $total_due_over - 1;
                    $total_due_over = $total_due_over + (0.6 - ($matchinfo->runing_over - (int) $matchinfo->runing_over));
                }
                // $matchinfo->runing_over
                if ($total_due_over > 0) {      // ARJUN
                    $required_runrate = number_format(($total_due_run) / ((int)$total_due_over + (($total_due_over - (int) $total_due_over) * 10 / 6)),  2);
                }
            } else {
                $matchinfo->betting_team_id = $matchinfo->team_1;
                $matchinfo->bowling_team_id = $matchinfo->team_2;

                $target_run = $team_2_run + 1;

                $total_due_run = $target_run - $team_1_run;
                $total_due_over = $total_over - (int) $matchinfo->runing_over;
                if ($matchinfo->runing_over > (int) $matchinfo->runing_over) {
                    $total_due_over = $total_due_over - 1;
                    $total_due_over = $total_due_over + (0.6 - ($matchinfo->runing_over - (int) $matchinfo->runing_over));
                }
                // $matchinfo->runing_over
                if ($total_due_over > 0) {      // ARJUN
                    $required_runrate = number_format(($total_due_run) / ((int)$total_due_over + (($total_due_over - (int) $total_due_over) * 10 / 6)),  2);
                }
            }
            $total_due_balls = ((int) $total_due_over * 6) + ($total_due_over - (int)$total_due_over) * 10;
            if ($total_due_balls > 100) {

                if ($total_due_run == 1) {
                    $required_status = $total_due_run . ' run required from ' . $total_due_over . ' overs';
                } else {
                    $required_status = $total_due_run . ' runs required from ' . $total_due_over . ' overs';
                }
            } else {
                if ($total_due_run == 1) {
                    $required_status = $total_due_run . ' run required from ' . $total_due_balls . ' balls';
                } else {
                    $required_status = $total_due_run . ' runs required from ' . $total_due_balls . ' balls';
                }
            }
        }

        // $matchinfo->betting_team_id;


        /* ------------------------------------------ */
        $total_wicket_player = $players - 1;
        if ($matchinfo->team_1 == $matchinfo->betting_team_id && $matchinfo->team_1_total_wickets == $total_wicket_player) {
            $is_new_inning = true;
        }

        if ($matchinfo->team_2 == $matchinfo->betting_team_id && $matchinfo->team_2_total_wickets == $total_wicket_player) {
            $is_new_inning = true;
        }

        if ($matchinfo->runing_over && ($matchinfo->runing_over - (int)$matchinfo->runing_over == 0)) {
            $is_new_over = true;


            if ($total_over == (int)$matchinfo->runing_over) {
                $is_new_inning = true;
            }
        }

        if ($matchinfo->inning_id == 3) {
            $is_new_inning = false;
        }

        /* ------------------------------------------ */

        // --- --- --- CHECKING FOR NEW OVER

        $running_over = $matchinfo->runing_over;
        $bowler_id = $matchinfo->bowler_id;



        $over_ball_list = MatchOver::select('ball_number',  'team_id', 'run', 'ball_type', 'out_type', 'is_extra')->where('match_ids', $match_id)
            ->where('over_number', '=', ($is_new_over ? (int) $running_over - 1 : (int) $running_over))
            ->where('bowler_player_id', $bowler_id)
            ->get();
        $over_by_run = $over_ball_list->sum('run');
        $over_by_extra = $over_ball_list->sum('is_extra');
        $over_total_run = $over_by_run + $over_by_extra;

        if ($is_new_over) {
            $is_bowler_assigned = false;
            $overArray = [];
            // $running_over = 1;
            if ((int) $running_over == 0) {
                $overArray = [0];
            } else {
                $overArray = '' . ((int) $running_over - 1) . ',' . ((int) $running_over) . '';
            }
            // dd(explode(',' ,$overArray));
            $current_bowler_ball_count = MatchOver::select('ball_number',  'team_id', 'run', 'ball_type', 'out_type', 'is_extra')->where('match_ids', $match_id)
                ->where('bowler_player_id', $bowler_id)
                ->where(function ($query) use ($running_over) {
                    $query->where('over_number', ((int) $running_over - 1))
                        ->orWhere('over_number', ((int) $running_over));
                })
                ->get();

            $over_by_run = $current_bowler_ball_count->sum('run');
            $over_by_extra = $current_bowler_ball_count->sum('is_extra');
            $over_total_run = $over_by_run + $over_by_extra;


            // dd($current_bowler_ball_count);

            if ($current_bowler_ball_count->count() == 0) {
                $is_bowler_assigned = true;
            } else {
                $extra_balls = MatchOver::select('run', 'ball_type', 'is_extra')->where('match_ids', $match_id)
                    ->where('over_number', '=', (int) $matchinfo->runing_over)
                    ->where('bowler_player_id', $bowler_id)     // ARJUN
                    ->get();
                if ($extra_balls->sum('is_extra') > 0) {
                    $is_new_over = false;


                    $over_ball_list = MatchOver::select('ball_number',  'team_id', 'run', 'ball_type', 'out_type', 'is_extra')->where('match_ids', $match_id)
                        ->where('over_number', '=', ($is_new_over ? (int) $running_over - 1 : (int) $running_over))
                        ->where('bowler_player_id', $bowler_id)
                        ->get();
                    $over_by_run = $over_ball_list->sum('run');
                    $over_by_extra = $over_ball_list->sum('is_extra');
                    $over_total_run = $over_by_run + $over_by_extra;
                }
            }
        }


        /* ------------------------------------------ */
        // WINNING TEAM CHECK
        $matchinfo->winning_team_id = 0;
        // dd($matchinfo->team_1);

        $matchinfo->is_super_over = false;
        if ($matchinfo->inning_id == 3 || $matchinfo->inning_id == 2) {
            // 1.0 ALL OVER FINISHED with lesser RUN
            if ($total_over == (int)$matchinfo->runing_over) {
                // CHECK RUN
                if ($matchinfo->team_1_total_run > $matchinfo->team_2_total_run) {
                    $matchinfo->winning_team_id = $matchinfo->team_1;
                }

                // CHECK RUN
                if ($matchinfo->team_2_total_run > $matchinfo->team_1_total_run) {
                    $matchinfo->winning_team_id = $matchinfo->team_2;
                }

                // CHECK RUN
                if ($matchinfo->team_1_total_run == $matchinfo->team_2_total_run) {
                    // DECISION PENDING
                    $matchinfo->winning_team_id = -1;
                    $matchinfo->is_super_over = true;
                }
            }
            if (($matchinfo->betting_team_id == $matchinfo->team_1 && $total_over == (int)$matchinfo->team_1_total_over) ||
                ($matchinfo->betting_team_id == $matchinfo->team_2 && $total_over == (int)$matchinfo->team_2_total_over)
            ) {
                // CHECK RUN
                if ($matchinfo->team_1_total_run > $matchinfo->team_2_total_run) {
                    $matchinfo->winning_team_id = $matchinfo->team_1;
                }

                // CHECK RUN
                if ($matchinfo->team_2_total_run > $matchinfo->team_1_total_run) {
                    $matchinfo->winning_team_id = $matchinfo->team_2;
                }

                // CHECK RUN
                if ($matchinfo->team_1_total_run == $matchinfo->team_2_total_run) {
                    // DECISION PENDING
                    $matchinfo->winning_team_id = -1;
                    $matchinfo->is_super_over = true;
                }
            }

            // 2.0 ALL OUT
            if ($matchinfo->winning_team_id == 0) {

                $total_wicket_player = $players - 1;
                if ($matchinfo->betting_team_id == $matchinfo->team_1) {
                    if ($matchinfo->team_1_total_wickets == $total_wicket_player) {
                        $matchinfo->winning_team_id = $matchinfo->team_2;
                    }
                }

                if ($matchinfo->betting_team_id == $matchinfo->team_2) {
                    if ($matchinfo->team_2_total_wickets == $total_wicket_player) {
                        $matchinfo->winning_team_id = $matchinfo->team_1;
                    }
                }
            }

            // 3.0 INCASE OF RUN IS HIGHER
            if ($matchinfo->winning_team_id == 0) {
                // dd($matchinfo->betting_team_id);
                if ($matchinfo->betting_team_id == $matchinfo->team_1) {

                    // if ($matchinfo->team_1_total_run > $matchinfo->team_2_total_run) {
                    if ($matchinfo->team1_runs > $matchinfo->team2_runs) {
                        $matchinfo->winning_team_id = $matchinfo->betting_team_id;
                        $is_new_inning = true;
                    }
                }

                if ($matchinfo->betting_team_id == $matchinfo->team_2) {

                    // if ($matchinfo->team_2_total_run > $matchinfo->team_1_total_run) {
                    if ($matchinfo->team2_runs > $matchinfo->team1_runs) {
                        $matchinfo->winning_team_id = $matchinfo->betting_team_id;
                        $is_new_inning = true;
                    }
                }
            }
        }
        if ($matchinfo->winning_team_id == -1) {
            $matchinfo->summary = "Match Draw";
        }
        if ($matchinfo->winning_team_id > 0) {
            $winning_team = Team::find($matchinfo->winning_team_id);
            if ($matchinfo->winning_team_id == $matchinfo->betting_team_id) {
                $playercount = TeamPlayer::where('team_id', $matchinfo->winning_team_id)->where('tournament_id', $id)->count();

                $wickets = ($matchinfo->winning_team_id == $matchinfo->team_1 ? $matchinfo->team_1_total_wickets : $matchinfo->team_2_total_wickets);
                $matchinfo->summary = $winning_team->team_name . ' won by ' . ($playercount - $wickets - 1) . ' wkts';
            } else {

                $playercount = TeamPlayer::where('team_id', $matchinfo->winning_team_id)->where('tournament_id', $id)->count();
                $total_winning_run = ($matchinfo->winning_team_id == $matchinfo->team_1 ?
                    ($matchinfo->team_1_total_run + $matchinfo->team_1_extra_run - $matchinfo->team_2_total_run - $matchinfo->team_2_extra_run) : ($matchinfo->team_2_total_run + $matchinfo->team_2_extra_run - $matchinfo->team_1_total_run - $matchinfo->team_1_extra_run));

                $matchinfo->summary = $winning_team->team_name . ' won by ' . $total_winning_run . ' runs';
            }
        }


        /* ------------------------------------------ */

        // PARTNER SHIP

        $partnership_ball_list = MatchOver::select(
            'ball_number',
            'team_id',
            'run',
            'ball_type',
            'out_type',
            'is_extra',
            'is_normal',
            DB::raw('CASE WHEN ball_type != "wb" THEN 1 ELSE 0 END as ball_count')
        )
            // ->where('over_number', '=', ($is_new_over ? (int) $running_over - 1 : (int) $running_over))
            // ->where('is_normal', 1)     // TODO: need to check is_extra => nb & run_by = 'bat'
            ->where(function ($query) use ($matchinfo, $match_id) {
                $query->where('sticker_player_id', $matchinfo->sticker_player_id)
                    ->where('nonsticker_player_id', $matchinfo->nonsticker_player_id)
                    ->where('match_ids', $match_id);
            })->orWhere(function ($query) use ($matchinfo, $match_id) {
                $query->where('sticker_player_id', $matchinfo->nonsticker_player_id)
                    ->where('nonsticker_player_id', $matchinfo->sticker_player_id)
                    ->where('match_ids', $match_id);
            })
            ->get();


        $partnership_run = $partnership_ball_list->sum('run') + $partnership_ball_list->sum('is_extra');
        // Calculate all balls except WIDE balls 
        $partnership_ball = $partnership_ball_list->sum('ball_count');

        /* ------------------------------------------ */
        $matchinfo->button_label = 'Start Match';
        if ($matchinfo->inning_id == 0 && empty($matchinfo->won_toss)) {
            $matchinfo->button_label = 'Start 1st Inning';
        } else if ($matchinfo->inning_id == 1) {
            $matchinfo->button_label = 'Resume 1st Inning';
        } else if ($matchinfo->inning_id == 2 && empty($matchinfo->bowler_id)) {
            $matchinfo->button_label = 'Start 2nd Inning';
        } else if ($matchinfo->inning_id == 2 && !empty($matchinfo->bowler_id)) {
            $matchinfo->button_label = 'Resume 2nd Inning';
        } else if ($matchinfo->inning_id == 3) {
            $matchinfo->button_label = 'Declare Result';
        } else if ($matchinfo->inning_id == 4) {
            $matchinfo->button_label = 'View Scorecard';
        }
        /* ------------------------------------------ */


        return response()->json([
            'success' => true,
            'message' => 'Match information',
            'data' => $matchinfo,
            // 'balls' => $over_ball_list,
            'over_balls' => $over_ball_list,
            'over_runs' => $over_total_run,
            'partnership_run' => $partnership_run,
            'partnership_ball' => $partnership_ball,
            'is_new_over' => $is_new_over,
            'is_bowler_assigned' => $is_bowler_assigned,
            'target_run' => $target_run,
            'out_player_id' => $out_player_id,
            'required_runrate' => $required_runrate,
            'required_status' => $required_status,
            'is_new_inning' => $is_new_inning
        ]);
    }

    public function scorecard(Request $request, $id)
    {
        $user = $request->user_id;
        $match_id = $request->match_id;
        $team_id = $request->team_id;
        $another_team_id = 0;
        $matchinfo = MatchInformation::where('tournament_id', $id)->where('is_delete', 1)->where('id', $match_id)
            // ->where('created_by', $user)
            ->with(
                'user',
                'tournament',
                'team1',
                'team2',
                'match_status',
                'tosswonteam',
                'playerstrick',
                'playerNonStricker',
                'playerBowler',
                'stickerScore',
                'nonstickerScore',
                'bowlerScore',
                'bowlerScorePlayer',
            )->first();


        $bowler_id = $matchinfo->bowler_id;

        if ($team_id == $matchinfo->team_1) {
            $another_team_id = $matchinfo->team_2;
        } else {
            $another_team_id = $matchinfo->team_1;
        }

        $balls = MatchOver::select('run', 'ball_type', 'no_ball_type')->where('match_ids', $match_id)
            ->where('bowler_player_id', $bowler_id)
            ->get();




        $wbCount = strval(MatchOver::select('run', 'ball_type', 'no_ball_type', 'is_extra')->where('match_ids', $match_id)->where('team_id', $team_id)->where('is_extra', 1)->where('ball_type', "wb")->get()->sum('is_extra'));
        $nbCount = strval(MatchOver::select('run', 'ball_type', 'no_ball_type', 'is_extra')->where('match_ids', $match_id)->where('team_id', $team_id)->where('is_extra', 1)->where('ball_type', "nb")->sum('is_extra'));
        $byCount = strval(MatchOver::select('run', 'ball_type', 'no_ball_type', 'is_extra')->where('match_ids', $match_id)->where('team_id', $team_id)->where('ball_type', "by")->get()->sum('is_extra')); // ->where('is_extra', 1)
        $lbCount = strval(MatchOver::select('run', 'ball_type', 'no_ball_type', 'is_extra')->where('match_ids', $match_id)->where('team_id', $team_id)->where('ball_type', "lb")->get()->sum('is_extra')); // ->where('is_extra', 1)




        // --- --- --- CHECKING FOR NEW OVER
        $current_over = 0;
        $current_ball = 0;
        $is_new_over = false;

        if ($matchinfo) {
            $current_over = (int) $matchinfo->runing_over;
            $current_ball =  ($matchinfo->runing_over - (int)$matchinfo->runing_over) * 10;
            $current_ball++;
        }
        if ($matchinfo->runing_over - (int)$matchinfo->runing_over == 0)
            $is_new_over = true;
        // --- --- --- CHECKING FOR NEW OVER
        $betsmen_list = MatchBatsman::with('betsmens', 'outPlayername')->where('match_id', $match_id)->where('team_id', $team_id)->get();
        $bawler_list = MatchBowler::with('bowler')->where('match_id', $match_id)->where('team_id', $another_team_id)->get();



        return response()->json([
            'success' => true,
            'message' => 'Match information',
            'data' => $matchinfo,
            'betsmens' => $betsmen_list,
            'bawlers' => $bawler_list,
            'extra' => array(
                'by' => $byCount,
                'lb' => $lbCount,
                'wb' => $wbCount,
                'nb' => $nbCount,
                'total' => $matchinfo->team_1_extra_run,
            ),
            'data' => $matchinfo,
            'balls' => $balls,
            // 'ballss' => $ballss,
            'is_new_over' => $is_new_over
        ]);
    }

    public function ballbyball(Request $request, $id)
    {
        $user = $request->user_id;
        $match_id = $request->match_id;
        $first_team_id = 0;
        $second_team_id = 0;
        $matchinfo = MatchInformation::where('tournament_id', $id)->where('is_delete', 1)->where('id', $match_id)
            // ->where('created_by', $user)
            ->with(
                'user',
                'tournament',
                'team1',
                'team2',
                'match_status',
                'tosswonteam',
                'playerstrick',
                'playerNonStricker',
                'playerBowler',
                'stickerScore',
                'nonstickerScore',
                'bowlerScore'
            )
            ->first();
        // dd($matchinfo);
        // $running_over = $matchinfo->runing_over;
        $bowler_id = $matchinfo->bowler_id;

        if (($matchinfo->won_toss == $matchinfo->team_1 && strtolower($matchinfo->toss_elected) == "bat") ||
            ($matchinfo->won_toss == $matchinfo->team_2 && strtolower($matchinfo->toss_elected) == "bowl")
        ) {
            $first_team_id = $matchinfo->team_1;
            $second_team_id = $matchinfo->team_2;
        } else {
            $first_team_id = $matchinfo->team_2;
            $second_team_id = $matchinfo->team_1;
        }



        $over_list = [];
        for ($over_index = 0; $over_index < $matchinfo->overseas; $over_index++) {
            # code...

            $total_run = 0;
            $balls = MatchOver::select('ball_number',  'team_id', 'run', 'ball_type', 'out_type', 'is_extra')->where('match_ids', $match_id)
                // ->join('team', 'team.id', '=', 'match_overs.team_id')
                ->where('over_number', '=', $over_index)
                ->where('team_id', '=', $first_team_id)
                // ->where('bowler_player_id', $bowler_id)
                ->get();

            // $team = Team::find($balls->team_id);                
            $over_by_run = $balls->sum('run');
            $over_by_extra = $balls->sum('is_extra');
            $total_run = $over_by_run + $over_by_extra;

            $over_list[$over_index] = array(
                'over_name' => 'Over ' . ($over_index + 1),
                'balls' => $balls->toArray(),
                'total_run' => $total_run
            );
        }

        $over_list_2nd = [];
        for ($over_index = 0; $over_index < $matchinfo->overseas; $over_index++) {
            # code...
            $total_run = 0;
            $balls = MatchOver::select('team.*', 'ball_number', 'team_id', 'run', 'ball_type', 'out_type', 'is_extra')->where('match_ids', $match_id)
                ->join('team', 'team.id', '=', 'match_overs.team_id')
                ->where('over_number', '=', $over_index)
                ->where('team_id', $second_team_id)
                // ->where('bowler_player_id', $bowler_id)
                ->get();

            $over_by_run = $balls->sum('run');
            $over_by_extra = $balls->sum('is_extra');
            $total_run = $over_by_run + $over_by_extra;

            $over_list_2nd[$over_index] = array(
                'over_name' => 'Over ' . ($over_index + 1),
                'balls' => $balls->toArray(),
                'total_run' => $total_run
            );
        }

        // dd()
        $first_team = Team::find($first_team_id);
        $second_team = Team::find($second_team_id);

        return response()->json([
            'success' => true,
            'message' => 'Ball by Ball Overs',
            'data' => $over_list,
            'team_1' => array(
                'team' => $first_team,
                'overs' => $over_list,
            ),
            'team_2' =>  array(
                'team' => $second_team,
                'overs' => $over_list_2nd,
            ),
        ]);
    }

    public function batsmanList(Request $request)
    {
        $team_id = $request->team_id;
        $match_id = $request->match_id;

        $teamplayer = MatchBatsman::select('player_id')->where('team_id', $team_id)->where('match_id', $match_id)->pluck('player_id');

        $player = TeamPlayer::whereNotIn('player_id', $teamplayer)
            ->with('player')
            ->where('team_id', $team_id)
            ->where('is_extra', 1)
            ->select('player_id')->get()->toArray();



        return response()->json([
            'message' => 'team player list',
            'data' => $player
        ]);
    }

    public function bowlerList(Request $request)
    {
        $team_id = $request->team_id;
        $match_id = $request->match_id;
        $last_over = $request->last_over_player_id;

        $matchovers = MatchOver::where('match_ids', $request->match_id)->select('bowler_player_id')->latest()->first()->toArray();
        // dd($matchovers);
        //$player_check = MatchBowler::select('player_id')->where('team_id', $team_id)->where('match_id', $match_id)->latest()->first()->toArray();
        // $player = TeamPlayer::whereNotIn('player_id', $last_over)->get()->toArray();

        $player = TeamPlayer::whereNotIn('player_id', $matchovers)
            ->with('player')
            ->where('team_id', $team_id)
            ->where('is_extra', 1)
            ->select('player_id')->get()->toArray();

        return response()->json([
            'message' => 'team player list',
            'data' => $player
        ]);
    }

    public function group_list()
    {
        $groups = [
            "Group 1",
            "Group 2",
            "Group 3",
            "Group 4"
        ];


        return response()->json([
            'success' => true,
            'message' => "Group List",
            'data' => $groups
        ]);
    }

    public function add_ball_history(Request $request)
    {

        $match_id = $request->match_ids;

        $match_info = MatchInformation::where('id', $match_id)->latest()->first();
        // $sticker_player_id
        // $match_over = MatchOver::where('match_ids', $match_id)->latest()->first();
        $stricker_player = MatchBatsman::where('match_id', $match_id)->where('player_id', $match_info->sticker_player_id)->latest()->first();
        $nonstricker_player = MatchBatsman::where('match_id', $match_id)->where('player_id', $match_info->nonsticker_player_id)->latest()->first();
        $bowler_player = MatchBowler::where('match_id', $match_id)->where('player_id', $match_info->bowler_id)->latest()->first();

        $match_history = new MatchHistory();
        //  Match Information 
        $match_history->match_id = $match_info->id;
        $match_history->team_id = $match_info->team_1;
        $match_history->tournament_id = $match_info->tournament_id;
        $match_history->sticker_player_id = $match_info->sticker_player_id;
        $match_history->nonsticker_player_id = $match_info->nonsticker_player_id;
        $match_history->bowler_id = $match_info->bowler_id;
        $match_history->team_1_total_run = $match_info->team_1_total_run;
        $match_history->team_1_total_wickets = $match_info->team_1_total_wickets;
        $match_history->team_1_total_over = $match_info->team_1_total_over;
        $match_history->team_1_extra_run = $match_info->team_1_extra_run;
        $match_history->team_2_total_run = $match_info->team_2_total_run;
        $match_history->team_2_total_wickets = $match_info->team_2_total_wickets;
        $match_history->team_2_total_over = $match_info->team_2_total_over;
        $match_history->team_2_extra_run = $match_info->team_2_extra_run;
        $match_history->runing_over = $match_info->runing_over;

        $match_history->match_over_id = null;
        // Match Batsmen stricker
        $match_history->batsman_runs_s = $stricker_player->run;
        $match_history->batsman_balls_s = $stricker_player->balls;
        $match_history->sixers_s = $stricker_player->sixers;
        $match_history->fours_s = $stricker_player->fours;
        $match_history->type_out_s = $stricker_player->type_out;
        $match_history->out_by_player_id_s = $stricker_player->out_by_player_id;
        $match_history->out_by_bowler_id_s = $stricker_player->out_by_bowler_id;


        // Match Batsmen nonstricker
        $match_history->batsman_runs_n = $nonstricker_player->run;
        $match_history->batsman_balls_n = $nonstricker_player->balls;
        $match_history->sixers_n = $nonstricker_player->sixers;
        $match_history->fours_n = $nonstricker_player->fours;
        $match_history->type_out_n = $nonstricker_player->type_out;
        $match_history->out_by_player_id_n = $nonstricker_player->out_by_player_id;
        $match_history->out_by_bowler_id_n = $nonstricker_player->out_by_bowler_id;

        // Match Bowler 
        $match_history->overs = $bowler_player->overs;
        $match_history->bowler_runs  = $bowler_player->runs;
        $match_history->maiden_over = $bowler_player->maiden_over;
        $match_history->wickets = $bowler_player->wickets;

        $match_history->save();

        return $match_history->id;
    }

    public function add_ball_history_update($history_id, $over_id)
    {
        //
        $history = MatchHistory::where('id', $history_id)->first();
        $history->match_over_id = $over_id;
        $history->save();
    }

    public function undobutton(Request $request)
    {
        
        $match_id = $request->match_id;

        $match_history = MatchHistory::where('match_id', $match_id)->latest()->first();

        $match_info = MatchInformation::where('id', $match_history->match_id)->latest()->first();
        // $match_info->id             = $match_history->match_id;
        // $match_info->id              = $match_history->team_1;
        // $match_info->tournament_id        = $match_history->tournament_id;
        $match_info->sticker_player_id    = $match_history->sticker_player_id;
        $match_info->nonsticker_player_id = $match_history->nonsticker_player_id;
        $match_info->bowler_id            = $match_history->bowler_id;
        $match_info->team_1_total_run     = $match_history->team_1_total_run;
        $match_info->team_1_total_wickets = $match_history->team_1_total_wickets;
        $match_info->team_1_total_over    = $match_history->team_1_total_over;
        $match_info->team_1_extra_run     = $match_history->team_1_extra_run;
        $match_info->team_2_total_run     = $match_history->team_2_total_run;
        $match_info->team_2_total_wickets = $match_history->team_2_total_wickets;
        $match_info->team_2_total_over    = $match_history->team_2_total_over;
        $match_info->team_2_extra_run     = $match_history->team_2_extra_run;
        $match_info->runing_over          = $match_history->runing_over;
        $match_info->save();

        $stricker_player = MatchBatsman::where('match_id', $match_id)->where('player_id', $match_history->sticker_player_id)->first();
        $stricker_player->run              = $match_history->batsman_runs_s;
        $stricker_player->balls            = $match_history->batsman_balls_s;
        $stricker_player->sixers           = $match_history->sixers_s;
        $stricker_player->fours            = $match_history->fours_s;
        $stricker_player->type_out         = $match_history->type_out_s;
        $stricker_player->out_by_player_id = $match_history->out_by_player_id_s;
        $stricker_player->out_by_bowler_id = $match_history->out_by_bowler_id_s;
        $stricker_player->save();

        $nonstricker_player = MatchBatsman::where('match_id', $match_id)->where('player_id', $match_history->nonsticker_player_id)->first();
        $nonstricker_player->run              = $match_history->batsman_runs_n;
        $nonstricker_player->balls            = $match_history->batsman_balls_n;
        $nonstricker_player->sixers           = $match_history->sixers_n;
        $nonstricker_player->fours            = $match_history->fours_n;
        $nonstricker_player->type_out         = $match_history->type_out_n;
        $nonstricker_player->out_by_player_id = $match_history->out_by_player_id_n;
        $nonstricker_player->out_by_bowler_id = $match_history->out_by_bowler_id_n;
        $nonstricker_player->save();

         $bowler_player = MatchBowler::where('match_id', $match_id)->where('player_id', $match_history->bowler_id)->first();
         $bowler_player->overs        = $match_history->overs;
         $bowler_player->runs         = $match_history->bowler_runs;
         $bowler_player->maiden_over  = $match_history->maiden_over;
         $bowler_player->wickets      = $match_history->wickets;
         $bowler_player->save();


          MatchHistory::where('id', $match_history->id)->delete();


          Matchover::where('id', $match_history->match_over_id)->delete();



         return response()->json([
            'message' => 'Undo Done',
            'success' => true
         ]);
 


    }

    public function strickchange(Request $request)
    {
        $match_id = $request->match_id;
        $match = MatchInformation::find($match_id);

        $stricker_player_id = $match->sticker_player_id;
        $nonstricker_player_id = $match->nonsticker_player_id;

        $match->sticker_player_id = $nonstricker_player_id;
        $match->nonsticker_player_id = $stricker_player_id;

        $match->save();

        return response()->json([
            'success' => true,
            'message' => 'Strick changed'
        ]);
    }
    
    public function bothTeamPlayer(Request $request)
    {
        $team_1 = $request->team_1;
        $team_2 = $request->team_2;

        $teamplayer = TeamPlayer::select('id','player_id')->whereIn('team_id', [$team_1, $team_2])->with('player')->get();

        return response()->json([
            'success' => true,
            'message' => 'Team Players',
            'date' => $teamplayer
        ]);
    }
    
    public function declarePlayerOfTheMatch(Request $request)
    {
        $match_id = $request->match_id;
        $match_info = MatchInformation::find($match_id);
        $match_info->player_of_the_match = $request->player_id;
        $match_info->save();

        return response()->json([
            'message' => 'player og the match Selected',
            'success' => true
        ]);
    }
}

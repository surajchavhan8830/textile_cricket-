<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchInformation extends Model
{
  use HasFactory;

  protected $appends = ['team_1_crr', 'team_2_crr', 'toss_status', 'team1_runs', 'team2_runs'];

  public function user()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function tournament()
  {
    return $this->belongsTo(Tournament::class, 'tournament_id', 'id');
  }

  public function team1()
  {
    return $this->belongsTo(Team::class, 'team_1', 'id')->where('is_delete', 1);
  }

  public function team2()
  {
    return $this->belongsTo(Team::class, 'team_2', 'id')->where('is_delete', 1);
  }

  public function getTossStatusAttribute()
  {
      if (!empty($this->won_toss)) {
        if ($this->team_1 == $this->won_toss) {
          return $this->team1->team_name . ' opt to ' . strtolower(str_replace('Bowl', 'Field', $this->toss_elected));
        } else {
          return $this->team2->team_name . ' opt to ' . strtolower(str_replace('Bowl', 'Field', $this->toss_elected));
        }
      }
  }
  
  public function getTeam1CrrAttribute()
  {
    // $is_runningover = 0;
    // if ((int)$this->team_1_total_over < $this->team_1_total_over) {
    //   $is_runningover = 1;
    // }
    // if ($this->team_1_total_over > 0)
    // {
    //   return number_format( ($this->team_1_total_run + $this->team_1_extra_run) / ( (int)$this->team_1_total_over + $is_runningover ),  2);
    // }
    // return number_format('0', 2);

    $running_ball = ($this->team_1_total_over - (int)$this->team_1_total_over) * 10;
    if ($this->team_1_total_over > 0) {
      // 
      return number_format(($this->team_1_total_run + $this->team_1_extra_run) / ((int)$this->team_1_total_over + ($running_ball / 6)),  2);
    }
    return number_format('0', 2);
  }

  public function getTeam2CrrAttribute()
  {
    $running_ball = ($this->team_2_total_over - (int)$this->team_2_total_over) * 10;
    if ($this->team_2_total_over > 0) {
      // 
      return number_format(($this->team_2_total_run + $this->team_2_extra_run) / ((int)$this->team_2_total_over + ($running_ball / 6)),  2);
    }
    return number_format('0', 2);
  }

  public function tosswonteam()
  {
    return $this->belongsTo(Team::class, 'won_toss', 'id')->select('id', 'team_name');
  }

  public function match_status()
  {
    return $this->belongsTo(Sechedule::class, 'match_status', 'id');
  }

  public function playerstrick()
  {
    return $this->belongsTo(Player::class, 'sticker_player_id', 'id')->select('id', 'player_name');
  }

  public function playerNonStricker()
  {
    return $this->belongsTo(Player::class, 'nonsticker_player_id', 'id')->select('id', 'player_name');
  }

  public function playerBowler()
  {
    return $this->belongsTo(Player::class, 'bowler_id', 'id')->select('id', 'player_name');
  }

  public function playerofthematch()
  {
    return $this->belongsTo(Player::class, 'player_of_the_match', 'id')->select('id', 'player_name', 'logo');
  }

  public function stickerScore()
  {
    return $this->belongsTo(MatchBatsman::class, 'sticker_player_id', 'player_id');
  }

  public function nonstickerScore()
  {
    return $this->belongsTo(MatchBatsman::class, 'nonsticker_player_id', 'player_id');
  }

  public function bowlerScore()
  {
    return $this->belongsTo(MatchBowler::class, 'bowler_id', 'player_id');
  }

  public function bowlerScorePlayer()
  {
    return $this->belongsTo(Player::class, 'out_by_player_id', 'id')->select('id', 'player_name');
  }

  public function player_out()
  {
    return $this->belongsTo(MatchOver::class, 'player_id', 'id');
  }

  public function balls()
  {
    return $this->belongsTo(MatchOver::class, 'bowler_id', 'bowler_player_id')
      ->where('over_number', 1)
      ->where('bowler_player_id', 3);
  }

  public function getTeam1RunsAttribute()
  {
    return $this->team_1_total_run + $this->team_1_extra_run;
  }

  
  public function getTeam2RunsAttribute()
  {
    return $this->team_2_total_run + $this->team_2_extra_run;
  }
}

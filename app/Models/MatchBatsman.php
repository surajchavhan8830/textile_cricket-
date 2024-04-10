<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchBatsman extends Model
{
    use HasFactory;
    protected $appends = ['out_status_label', 'strike_rate'];


    public function betsmens()
    {
      return $this->belongsTo(Player::class, 'player_id', 'id')->select('id', 'player_name');
    }
    public function outPlayername()
    {
        return $this->belongsTo(Player::class, 'out_by_player_id', 'id');
    }

    public function outBowlername()
    {
      return $this->belongsTo(Player::class, 'out_by_bowler_id' , 'id');
    }

    public function getStrikeRateAttribute()
    {
        if ($this->balls > 0)   
        {
          return number_format($this->run * 100 / $this->balls, 2);
        }
        return number_format('0', 2);
    }
    public function getOutStatusLabelAttribute()
    {
        $text = 'not out';
        if ( empty($this->outPlayername) && empty($this->outBowlername) && empty($this->type_out))  {
           return $text;
        }

        $text = 'out';
        if ($this->type_out == 'bowled') {
          $text = 'b ' . $this->outPlayername->player_short_name;
        }

        if ($this->type_out == 'runout') {
          $text = 'runout (' . $this->outPlayername->player_short_name . ')';
        }

        if ($this->type_out == 'hitwicket') {
          $text = 'hw  b ' . $this->outBowlername->player_short_name . '';
        }

        
        if ($this->type_out == 'lbw') {
          $text = 'lbw b ' . $this->outPlayername->player_short_name . '';
        }

        if ($this->type_out == 'caught') {
          // $text = 'c ' . $this->outPlayername->player_short_name . ' b ' . 'bolwer';
             $text = 'c ' . $this->outPlayername->player_short_name . ' b ' . $this->outBowlername->player_short_name;

           //$this->outBowlername->player_short_name;
        }
        
        if ($this->type_out == 'stumped') {
          // $text = 'st ' . $this->outPlayername->player_short_name . ' b ' . 'bolwer';
          $text = 'st ' . $this->outPlayername->player_short_name . ' b ' .  $this->outBowlername->player_short_name;

        }

        if ($this->type_out == 'other') {
          $text = 'other';
        }
        return $text;
  
        // return $this->belongsTo(Player::class, 'out_by_player_id', 'id');
    }

}

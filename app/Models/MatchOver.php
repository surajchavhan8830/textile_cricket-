<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchOver extends Model
{
    use HasFactory;

        protected $appends = ['ball_tag'];


    public function out_playername()
    {
        return $this->belongsTo(Player::class, 'out_by_player_id', 'id');
    }
    
    public function bowler_player_name()
    {
         return $this->belongsTo(Player::class, 'bowler_player_id', 'id')->select('id', 'player_name');
    }

    public function getBallTagAttribute() 
    {
        $is_wicket = (empty($this->out_type) == false) ? 'W' : '';
        
        if ($this->ball_type == 'normal') {
            if ($is_wicket == 'W') {
                return $is_wicket . ($this->run > 0 ? $this->run : '');
            }
            else {
                return $this->run . '';
            }
        }
        else if ($this->ball_type == 'by') {
            return 'b' . ($this->is_extra > 0 ? $this->is_extra : '') . $is_wicket;
        }
        else if ($this->ball_type == 'lb') {
            return 'L' . ($this->is_extra > 0 ? $this->is_extra : '') . $is_wicket;
        }
        
        else if ($this->ball_type == 'nb') {
            return 'n' . ($this->run > 0 ? $this->run : 'b') . $is_wicket;
        }
        
        else if ($this->ball_type == 'wb') {
            return 'w' . ($this->run > 0 ? $this->run : 'd') . $is_wicket;
        }
        else if ($is_wicket == 'W') {
            return $is_wicket;
        }
        
        return 'ab';
    }
}

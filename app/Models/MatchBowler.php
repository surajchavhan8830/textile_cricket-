<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchBowler extends Model
{
    use HasFactory;
    protected $appends = ['economy_rate'];

    public function bowler()
    {
     return $this->belongsTo(Player::class, 'player_id', 'id')->select('id', 'player_name');
 
    }

    public function getEconomyRateAttribute()
    {
        $running_ball = ($this->overs - (int)$this->overs) * 10 ;
        // if ((int)$this->overs < $this->overs) {
        //   $is_runningover = 1;
        // }
        if ($this->overs > 0)
        {
          return number_format( $this->runs / ( (int)$this->overs + ($running_ball / 6) ),  2);
        }
        return number_format('0', 2);
    }
}

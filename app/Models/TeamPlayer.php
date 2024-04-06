<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamPlayer extends Model
{
    use HasFactory;
    protected $table = 'team_player';

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id', 'id');      //->select('id', 'player_name');
    }

    public function playerrole()
    {
        return $this->belongsTo(playerPosition::class, 'postion', 'id')->select('id', 'position_name');
    }

}

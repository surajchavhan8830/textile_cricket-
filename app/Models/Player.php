<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;
    protected $table = 'players';
    protected $appends = ['player_short_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select('id', 'name');
    }

   public function playerteam()
   {
     return  $this->hasMany(TeamPlayer::class, 'player_id', 'id');
   }

   public function getPlayerShortNameAttribute()
   {
      return explode(" ", $this->player_name)[0];
   }
}

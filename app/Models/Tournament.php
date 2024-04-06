<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    public function tournamenttype()
    {
        return $this->belongsTo(TournamentType::class, 'tournament_type_id', 'id')->select('id', 'name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select('id', 'name');
    }
}

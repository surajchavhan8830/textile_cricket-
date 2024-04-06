<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentType extends Model
{
    use HasFactory;
    protected $table = 'tournament_type';
    
    protected $fillable = [
        'tournament_name',
        'created_by',
        'location',
        'type_of_tournament_id',
        'logo',
        'description',
        'strat_date',
        'end_date',
        'due_date',
        'address'
    ];
}

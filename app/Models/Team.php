<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $table = 'team';

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function teamplaye()
    {
        return $this->hasMany(TeamPlayer::class,'team_id', 'id');
    }
    
    protected $appends = ['player_short_name', 'total_count', 'total_played_count', 'total_win_count', 'total_loss_count', 'total_draw_count', 'points', 'nrr'];
    public function getPlayerShortNameAttribute()
    {
        return explode(" ", $this->player_name)[0];
    }
    
    public function team_1()
    {
        return $this->hasMany(MatchInformation::class, 'team_1', 'id')->where('is_delete', '1')->select('id',
                    'created_by',
                    'tournament_id',
                    'team_1',
                    'team_1_total_run',
                    'team_1_total_wickets',
                    'team_1_total_over',
                    'team_1_extra_run',
                    'team_1_point',
                    'team_2',
                    'team_2_total_run',
                    'team_2_total_wickets',
                    'team_2_total_over',
                    'team_2_extra_run',
                    'team_2_point',
                    'won_team_id',
                    'inning_id');
    }
    
    public function team_2()
    {
        return $this->hasMany(MatchInformation::class, 'team_2', 'id')->where('is_delete', '1')->select('id',
                    'created_by',
                    'tournament_id',
                    'team_1',
                    'team_1_total_run',
                    'team_1_total_wickets',
                    'team_1_total_over',
                    'team_1_extra_run',
                    'team_1_point',
                    'team_2',
                    'team_2_total_run',
                    'team_2_total_wickets',
                    'team_2_total_over',
                    'team_2_extra_run',
                    'team_2_point',
                    'won_team_id',
                    'inning_id');
                    // ;
    }
    
    public function getTotalCountAttribute()
    {
        return $this->team_1->count() + $this->team_2->count();
    }
    
    public function getTotalPlayedCountAttribute()
    {
        return $this->team_1->where('inning_id', '4')->count() + 
                $this->team_2->where('inning_id', '4')->count();
    }
    
    public function getTotalWinCountAttribute()
    {
        return $this->team_1->where('inning_id', '4')->where('won_team_id', $this->id)->count() + 
                $this->team_2->where('inning_id', '4')->where('won_team_id', $this->id)->count();
    }
    
    
    public function getTotalLossCountAttribute()
    {
        return $this->team_1->where('inning_id', '4')->whereNotNull('won_team_id')->where('won_team_id', '!=', $this->id)->count() + 
                $this->team_2->where('inning_id', '4')->whereNotNull('won_team_id')->where('won_team_id', '!=',$this->id)->count();
    }
    
    public function getTotalDrawCountAttribute()
    {
        return $this->team_1->where('inning_id', '4')->whereNull('won_team_id')->count() + 
                $this->team_2->where('inning_id', '4')->whereNull('won_team_id')->count();
    }


    public function getPointsAttribute()
    {
        return ($this->total_win_count * 2) + 
                ($this->total_draw_count * 1);
    }
    
    public function getNrrAttribute()
    {
        return '0.00';
    }
    
}
